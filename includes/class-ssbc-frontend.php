<?php
/**
 * Frontend rendering and asset management for ClickReach – Social Share Buttons & Counter
 *
 * @package ClickReach_Social_Share_Buttons
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class SSBC_Frontend
 */
class SSBC_Frontend {

	/**
	 * Flag to track if assets should be printed.
	 *
	 * @var bool
	 */
	private static $assets_enqueued = false;

	/**
	 * Hook into WordPress.
	 */
	public static function init() {
		add_filter( 'the_content', array( __CLASS__, 'filter_content' ), 20 );
		add_shortcode( 'clickreach_social_share_buttons', array( __CLASS__, 'render_shortcode' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register_assets' ) );
		add_action( 'wp_head', array( __CLASS__, 'output_open_graph_tags' ) );
	}

	/**
	 * Register frontend scripts and styles.
	 */
	public static function register_assets() {
		wp_register_style(
			'ssbc-frontend-css',
			SSBC_PLUGIN_URL . 'assets/css/ssbc-frontend.css',
			array(),
			SSBC_VERSION
		);

		wp_register_script(
			'ssbc-frontend-js',
			SSBC_PLUGIN_URL . 'assets/js/ssbc-frontend.js',
			array(), // Pure vanilla JS - zero external or jQuery dependencies!
			SSBC_VERSION,
			true
		);

		wp_localize_script(
			'ssbc-frontend-js',
			'ssbcData',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'ssbc_nonce' ),
				'i18n'    => array(
					'copied' => __( 'Link Copied!', 'clickreach-social-share-buttons' ),
				),
			)
		);
	}

	/**
	 * Enqueue assets only when buttons are actually rendered.
	 */
	public static function enqueue_assets() {
		if ( ! self::$assets_enqueued ) {
			wp_enqueue_style( 'ssbc-frontend-css' );
			wp_enqueue_script( 'ssbc-frontend-js' );
			self::$assets_enqueued = true;
		}
	}

	/**
	 * Check whether buttons should be displayed for current post.
	 *
	 * @param int $post_id Post ID.
	 * @return bool
	 */
	public static function should_display( $post_id = 0 ) {
		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		if ( ! $post_id || is_admin() || is_feed() ) {
			return false;
		}

		// Check post metabox override
		$status = get_post_meta( $post_id, '_ssbc_status', true );
		if ( 'disable' === $status ) {
			return false;
		}
		if ( 'enable' === $status ) {
			return true;
		}

		// Check global position
		$position = SSBC_Settings::get_option( 'position', 'below' );
		if ( 'none' === $position ) {
			return false;
		}

		// Check post type
		$post_type  = get_post_type( $post_id );
		$post_types = (array) SSBC_Settings::get_option( 'post_types', array( 'post' ) );

		return in_array( $post_type, $post_types, true );
	}

	/**
	 * Filter post content to insert buttons.
	 *
	 * @param string $content Post content.
	 * @return string Modified content.
	 */
	public static function filter_content( $content ) {
		if ( ! is_main_query() || ! in_the_loop() || ! is_singular() ) {
			return $content;
		}

		$post_id = get_the_ID();
		if ( ! self::should_display( $post_id ) ) {
			return $content;
		}

		$position = SSBC_Settings::get_option( 'position', 'below' );
		$buttons  = self::render_buttons_html( $post_id );

		if ( empty( $buttons ) ) {
			return $content;
		}

		if ( 'above' === $position ) {
			return $buttons . $content;
		} elseif ( 'both' === $position ) {
			return $buttons . $content . $buttons;
		} else {
			return $content . $buttons;
		}
	}

	/**
	 * Render buttons shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	public static function render_shortcode( $atts ) {
		$post_id = get_the_ID();
		if ( ! $post_id ) {
			return '';
		}

		return self::render_buttons_html( $post_id );
	}

	/**
	 * Build complete buttons HTML markup.
	 *
	 * @param int $post_id Post ID.
	 * @return string HTML string.
	 */
	public static function render_buttons_html( $post_id ) {
		$settings         = SSBC_Settings::get_settings();
		$enabled_networks = (array) $settings['enabled_networks'];
		if ( empty( $enabled_networks ) ) {
			return '';
		}

		self::enqueue_assets();

		$post_url   = get_permalink( $post_id );
		$post_title = wp_strip_all_tags( get_the_title( $post_id ) );
		$style      = sanitize_html_class( $settings['button_style'] );
		$show_counts = $settings['show_counts'];
		$can_show_counts = true;

		if ( 'none' === $show_counts ) {
			$can_show_counts = false;
		} elseif ( 'logged_in' === $show_counts && ! is_user_logged_in() ) {
			$can_show_counts = false;
		}

		$output  = '<div class="ssbc-wrapper" data-post-id="' . esc_attr( $post_id ) . '">';

		// Optional heading label
		if ( ! empty( $settings['share_label'] ) ) {
			$output .= '<div class="ssbc-label">' . esc_html( $settings['share_label'] ) . '</div>';
		}

		$output .= '<div class="ssbc-buttons-container ssbc-style-' . esc_attr( $style ) . '">';

		// Optional Total Count badge
		if ( $can_show_counts && ! empty( $settings['show_total'] ) ) {
			$total_shares = 0;
			$all_networks = SSBC_Icons::get_supported_networks();
			foreach ( array_keys( $all_networks ) as $net_key ) {
				$total_shares += absint( get_post_meta( $post_id, '_ssbc_clicks_' . $net_key, true ) );
			}
			$output .= '<div class="ssbc-total-badge" aria-label="' . esc_attr__( 'Total Shares', 'clickreach-social-share-buttons' ) . '">';
			$output .= '<span class="ssbc-total-count" data-post-id="' . esc_attr( $post_id ) . '">' . esc_html( number_format_i18n( $total_shares ) ) . '</span>';
			$output .= '<span class="ssbc-total-text">' . esc_html__( 'Shares', 'clickreach-social-share-buttons' ) . '</span>';
			$output .= '</div>';
		}

		// Individual Network Buttons
		$supported = SSBC_Icons::get_supported_networks();
		foreach ( $enabled_networks as $network ) {
			if ( ! isset( $supported[ $network ] ) ) {
				continue;
			}

			$share_url = SSBC_Icons::get_share_url( $network, $post_url, $post_title );
			$count     = absint( get_post_meta( $post_id, '_ssbc_clicks_' . $network, true ) );
			$net_meta  = $supported[ $network ];

			$is_copy = ( 'copy' === $network );
			$target  = $is_copy ? '' : ' target="_blank" rel="noopener noreferrer"';
			$role    = $is_copy ? ' role="button"' : '';

			$output .= sprintf(
				'<a href="%1$s" class="ssbc-btn ssbc-btn-%2$s" data-platform="%2$s" data-post-id="%3$s" data-share-url="%4$s"%5$s%6$s aria-label="%7$s">',
				esc_url( $share_url ),
				esc_attr( $network ),
				esc_attr( $post_id ),
				esc_url( $post_url ),
				$target,
				$role,
				/* translators: %s: Social network name */
				esc_attr( sprintf( __( 'Share on %s', 'clickreach-social-share-buttons' ), $net_meta['name'] ) )
			);

			// SVG Icon
			$output .= '<span class="ssbc-icon">' . SSBC_Icons::get_svg( $network ) . '</span>';

			// Network name label
			$output .= '<span class="ssbc-name">' . esc_html( $net_meta['name'] ) . '</span>';

			// Click / Share Count badge
			if ( $can_show_counts ) {
				$output .= '<span class="ssbc-count" data-network="' . esc_attr( $network ) . '">' . esc_html( number_format_i18n( $count ) ) . '</span>';
			}

			$output .= '</a>';
		}

		$output .= '</div>'; // .ssbc-buttons-container
		$output .= '</div>'; // .ssbc-wrapper

		return $output;
	}

	/**
	 * Output Open Graph meta tags in <head> for accurate social preview cards.
	 */
	public static function output_open_graph_tags() {
		if ( ! is_singular() ) {
			return;
		}

		// Avoid duplicate OG tags if dedicated SEO plugins are active
		$seo_plugin_active = defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' )
			|| defined( 'SEOPRESS_VERSION' ) || defined( 'THE_SEO_FRAMEWORK_VERSION' ) || defined( 'SLIM_SEO_VER' )
			|| defined( 'SQ_VERSION' ) || class_exists( 'Jetpack' );

		/**
		 * Filter whether ClickReach prints Open Graph meta tags.
		 *
		 * @param bool $output True to print the tags.
		 */
		if ( ! apply_filters( 'ssbc_output_open_graph', ! $seo_plugin_active ) ) {
			return;
		}

		$post_id = get_the_ID();
		if ( ! $post_id ) {
			return;
		}

		$title = wp_strip_all_tags( get_the_title( $post_id ) );
		$url   = get_permalink( $post_id );
		$desc  = wp_strip_all_tags( get_the_excerpt( $post_id ) );

		echo "\n<!-- ClickReach – Social Share Buttons & Counter Open Graph Meta -->\n";
		echo '<meta property="og:title" content="' . esc_attr( $title ) . '" />' . "\n";
		echo '<meta property="og:type" content="article" />' . "\n";
		echo '<meta property="og:url" content="' . esc_url( $url ) . '" />' . "\n";
		if ( ! empty( $desc ) ) {
			echo '<meta property="og:description" content="' . esc_attr( $desc ) . '" />' . "\n";
		}
		if ( has_post_thumbnail( $post_id ) ) {
			$img = get_the_post_thumbnail_url( $post_id, 'large' );
			if ( $img ) {
				echo '<meta property="og:image" content="' . esc_url( $img ) . '" />' . "\n";
			}
		}
		echo "<!-- / ClickReach – Social Share Buttons & Counter Open Graph Meta -->\n\n";
	}
}
