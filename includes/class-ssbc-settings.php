<?php
/**
 * Settings management for ClickReach – Social Share Buttons & Counter
 *
 * @package ClickReach_Social_Share_Buttons
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class SSBC_Settings
 */
class SSBC_Settings {

	/**
	 * Option key stored in wp_options.
	 */
	const OPTION_NAME = 'ssbc_settings';

	/**
	 * Hook into WordPress actions.
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'add_settings_page' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
	}

	/**
	 * Default settings array.
	 *
	 * @return array
	 */
	public static function get_defaults() {
		return array(
			'enabled_networks' => array( 'facebook', 'x', 'linkedin', 'whatsapp' ),
			'position'         => 'below', // 'above', 'below', 'both', 'none'
			'post_types'       => array( 'post' ),
			'button_style'     => 'rounded', // 'rounded', 'pill', 'flat'
			'show_counts'      => 'all',     // 'all', 'logged_in', 'none'
			'show_total'       => 1,
			'share_label'      => __( 'Share this:', 'clickreach-social-share-buttons' ),
		);
	}

	/**
	 * Get all plugin settings merged with defaults.
	 *
	 * @return array
	 */
	public static function get_settings() {
		$options = get_option( self::OPTION_NAME, array() );
		return wp_parse_args( $options, self::get_defaults() );
	}

	/**
	 * Get single option value.
	 *
	 * @param string $key     Option key.
	 * @param mixed  $default Fallback value.
	 * @return mixed
	 */
	public static function get_option( $key, $default = null ) {
		$settings = self::get_settings();
		if ( isset( $settings[ $key ] ) ) {
			return $settings[ $key ];
		}
		return $default;
	}

	/**
	 * Add Settings page under Settings > Social Share Buttons.
	 */
	public static function add_settings_page() {
		add_options_page(
			__( 'ClickReach – Social Share Buttons & Counter', 'clickreach-social-share-buttons' ),
			__( 'ClickReach Social Share Buttons', 'clickreach-social-share-buttons' ),
			'manage_options',
			'clickreach-social-share-buttons',
			array( __CLASS__, 'render_settings_page' )
		);
	}

	/**
	 * Register settings, sections, and fields.
	 */
	public static function register_settings() {
		register_setting(
			'ssbc_settings_group',
			self::OPTION_NAME,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( __CLASS__, 'sanitize_settings' ),
				'default'           => self::get_defaults(),
			)
		);

		add_settings_section(
			'ssbc_general_section',
			__( 'General Display Settings', 'clickreach-social-share-buttons' ),
			'__return_false',
			'clickreach-social-share-buttons'
		);

		add_settings_field(
			'ssbc_enabled_networks',
			__( 'Social Networks', 'clickreach-social-share-buttons' ),
			array( __CLASS__, 'render_networks_field' ),
			'clickreach-social-share-buttons',
			'ssbc_general_section'
		);

		add_settings_field(
			'ssbc_position',
			__( 'Button Placement', 'clickreach-social-share-buttons' ),
			array( __CLASS__, 'render_position_field' ),
			'clickreach-social-share-buttons',
			'ssbc_general_section'
		);

		add_settings_field(
			'ssbc_post_types',
			__( 'Target Post Types', 'clickreach-social-share-buttons' ),
			array( __CLASS__, 'render_post_types_field' ),
			'clickreach-social-share-buttons',
			'ssbc_general_section'
		);

		add_settings_field(
			'ssbc_button_style',
			__( 'Button Shape / Style', 'clickreach-social-share-buttons' ),
			array( __CLASS__, 'render_style_field' ),
			'clickreach-social-share-buttons',
			'ssbc_general_section'
		);

		add_settings_field(
			'ssbc_show_counts',
			__( 'Display Share Counts', 'clickreach-social-share-buttons' ),
			array( __CLASS__, 'render_counts_field' ),
			'clickreach-social-share-buttons',
			'ssbc_general_section'
		);

		add_settings_field(
			'ssbc_show_total',
			__( 'Total Share Count Badge', 'clickreach-social-share-buttons' ),
			array( __CLASS__, 'render_total_field' ),
			'clickreach-social-share-buttons',
			'ssbc_general_section'
		);

		add_settings_field(
			'ssbc_share_label',
			__( 'Share Heading Label', 'clickreach-social-share-buttons' ),
			array( __CLASS__, 'render_label_field' ),
			'clickreach-social-share-buttons',
			'ssbc_general_section'
		);
	}

	/**
	 * Sanitize user input.
	 *
	 * @param array $input Raw input.
	 * @return array Sanitized array.
	 */
	public static function sanitize_settings( $input ) {
		if ( ! is_array( $input ) ) {
			$input = array();
		}

		$sanitized = self::get_defaults();
		$available = array_keys( SSBC_Icons::get_supported_networks() );

		// Networks
		if ( ! empty( $input['enabled_networks'] ) && is_array( $input['enabled_networks'] ) ) {
			$sanitized['enabled_networks'] = array_values( array_intersect( $input['enabled_networks'], $available ) );
		} else {
			$sanitized['enabled_networks'] = array();
		}

		// Position
		$allowed_positions = array( 'above', 'below', 'both', 'none' );
		if ( ! empty( $input['position'] ) && in_array( $input['position'], $allowed_positions, true ) ) {
			$sanitized['position'] = $input['position'];
		}

		// Post Types
		if ( ! empty( $input['post_types'] ) && is_array( $input['post_types'] ) ) {
			$registered_types = get_post_types( array( 'public' => true ) );
			$sanitized['post_types'] = array_values( array_intersect( $input['post_types'], array_keys( $registered_types ) ) );
		} else {
			$sanitized['post_types'] = array();
		}

		// Button Style
		$allowed_styles = array( 'rounded', 'pill', 'flat' );
		if ( ! empty( $input['button_style'] ) && in_array( $input['button_style'], $allowed_styles, true ) ) {
			$sanitized['button_style'] = $input['button_style'];
		}

		// Show Counts
		$allowed_counts = array( 'all', 'logged_in', 'none' );
		if ( ! empty( $input['show_counts'] ) && in_array( $input['show_counts'], $allowed_counts, true ) ) {
			$sanitized['show_counts'] = $input['show_counts'];
		}

		// Total Count
		$sanitized['show_total'] = ! empty( $input['show_total'] ) ? 1 : 0;

		// Label
		if ( isset( $input['share_label'] ) ) {
			$sanitized['share_label'] = sanitize_text_field( $input['share_label'] );
		}

		return $sanitized;
	}

	/**
	 * Field renderers.
	 */
	public static function render_networks_field() {
		$settings = self::get_settings();
		$networks = SSBC_Icons::get_supported_networks();
		$selected = (array) $settings['enabled_networks'];

		echo '<div class="ssbc-network-checkboxes">';
		foreach ( $networks as $key => $meta ) {
			$is_checked = in_array( $key, $selected, true );
			?>
			<label style="display:inline-block; margin-right: 18px; margin-bottom: 8px;">
				<input type="checkbox" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[enabled_networks][]" value="<?php echo esc_attr( $key ); ?>" <?php checked( $is_checked ); ?>>
				<strong><?php echo esc_html( $meta['name'] ); ?></strong>
			</label>
			<?php
		}
		echo '</div>';
		echo '<p class="description">' . esc_html__( 'Select which social share buttons should appear on your content.', 'clickreach-social-share-buttons' ) . '</p>';
	}

	public static function render_position_field() {
		$settings = self::get_settings();
		$positions = array(
			'below' => __( 'Below Content (Recommended)', 'clickreach-social-share-buttons' ),
			'above' => __( 'Above Content', 'clickreach-social-share-buttons' ),
			'both'  => __( 'Both Above & Below Content', 'clickreach-social-share-buttons' ),
			'none'  => __( 'Manual Only (Shortcode [clickreach_social_share_buttons])', 'clickreach-social-share-buttons' ),
		);

		foreach ( $positions as $value => $label ) {
			?>
			<label style="display:block; margin-bottom: 6px;">
				<input type="radio" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[position]" value="<?php echo esc_attr( $value ); ?>" <?php checked( $settings['position'], $value ); ?>> <?php echo esc_html( $label ); ?>
			</label>
			<?php
		}
	}

	public static function render_post_types_field() {
		$settings   = self::get_settings();
		$post_types = get_post_types( array( 'public' => true ), 'objects' );
		$selected   = (array) $settings['post_types'];

		foreach ( $post_types as $type ) {
			if ( 'attachment' === $type->name ) {
				continue;
			}
			$is_checked = in_array( $type->name, $selected, true );
			?>
			<label style="display:inline-block; margin-right: 18px;">
				<input type="checkbox" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[post_types][]" value="<?php echo esc_attr( $type->name ); ?>" <?php checked( $is_checked ); ?>> <?php echo esc_html( $type->label ); ?>
			</label>
			<?php
		}
		echo '<p class="description">' . esc_html__( 'Choose where social share buttons are automatically displayed.', 'clickreach-social-share-buttons' ) . '</p>';
	}

	public static function render_style_field() {
		$settings = self::get_settings();
		$styles   = array(
			'rounded' => __( 'Rounded (Soft Corners)', 'clickreach-social-share-buttons' ),
			'pill'    => __( 'Pill (Full Capsule)', 'clickreach-social-share-buttons' ),
			'flat'    => __( 'Flat (Sharp Corners)', 'clickreach-social-share-buttons' ),
		);

		foreach ( $styles as $value => $label ) {
			?>
			<label style="display:inline-block; margin-right: 20px;">
				<input type="radio" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[button_style]" value="<?php echo esc_attr( $value ); ?>" <?php checked( $settings['button_style'], $value ); ?>> <?php echo esc_html( $label ); ?>
			</label>
			<?php
		}
	}

	public static function render_counts_field() {
		$settings = self::get_settings();
		$options  = array(
			'all'       => __( 'Show to all visitors (Default)', 'clickreach-social-share-buttons' ),
			'logged_in' => __( 'Show only to logged-in users', 'clickreach-social-share-buttons' ),
			'none'      => __( 'Hide share counts', 'clickreach-social-share-buttons' ),
		);

		foreach ( $options as $value => $label ) {
			?>
			<label style="display:block; margin-bottom: 6px;">
				<input type="radio" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[show_counts]" value="<?php echo esc_attr( $value ); ?>" <?php checked( $settings['show_counts'], $value ); ?>> <?php echo esc_html( $label ); ?>
			</label>
			<?php
		}
	}

	public static function render_total_field() {
		$settings = self::get_settings();
		?>
		<label>
			<input type="checkbox" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[show_total]" value="1" <?php checked( 1, $settings['show_total'] ); ?>>
			<?php esc_html_e( 'Show a "Total Shares" counter badge alongside buttons', 'clickreach-social-share-buttons' ); ?>
		</label>
		<?php
	}

	public static function render_label_field() {
		$settings = self::get_settings();
		printf(
			'<input type="text" name="%1$s[share_label]" value="%2$s" class="regular-text">
			<p class="description">%3$s</p>',
			esc_attr( self::OPTION_NAME ),
			esc_attr( $settings['share_label'] ),
			esc_html__( 'Text displayed above the share buttons (leave empty to hide).', 'clickreach-social-share-buttons' )
		);
	}

	/**
	 * Render settings page.
	 */
	public static function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap ssbc-admin-wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<p class="description">
				<?php esc_html_e( 'Ultra-lightweight, privacy-friendly social sharing buttons with real-time click counters. Zero bloat, pure inline SVGs.', 'clickreach-social-share-buttons' ); ?>
			</p>
			<hr>

			<form action="options.php" method="post">
				<?php
				settings_fields( 'ssbc_settings_group' );
				do_settings_sections( 'clickreach-social-share-buttons' );
				submit_button( __( 'Save Settings', 'clickreach-social-share-buttons' ) );
				?>
			</form>

			<div class="ssbc-admin-card" style="margin-top: 30px; padding: 15px 20px; background: #fff; border: 1px solid #ccd0d4; border-radius: 4px; max-width: 800px;">
				<h3><?php esc_html_e( 'Shortcode Usage', 'clickreach-social-share-buttons' ); ?></h3>
				<p><?php esc_html_e( 'You can manually embed social buttons anywhere inside post content, page builders, or theme templates using this shortcode:', 'clickreach-social-share-buttons' ); ?></p>
				<code>[clickreach_social_share_buttons]</code>
			</div>
		</div>
		<?php
	}
}
