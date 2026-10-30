<?php
/**
 * AJAX Click Tracking Handler for ClickReach – Social Share Buttons & Counter
 *
 * @package ClickReach_Social_Share_Buttons
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class SSBC_Ajax
 */
class SSBC_Ajax {

	/**
	 * Hook into WordPress AJAX actions.
	 */
	public static function init() {
		add_action( 'wp_ajax_ssbc_track_click', array( __CLASS__, 'handle_click_tracking' ) );
		add_action( 'wp_ajax_nopriv_ssbc_track_click', array( __CLASS__, 'handle_click_tracking' ) );
	}

	/**
	 * Handle incoming click tracking AJAX requests.
	 */
	public static function handle_click_tracking() {
		// Nonce validation
		check_ajax_referer( 'ssbc_nonce', 'nonce' );

		// Input validation
		$post_id  = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
		$platform = isset( $_POST['platform'] ) ? sanitize_key( $_POST['platform'] ) : '';

		if ( ! $post_id || ! get_post( $post_id ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid Post ID.', 'clickreach-social-share-buttons' ) ), 400 );
		}

		$supported = array_keys( SSBC_Icons::get_supported_networks() );
		if ( ! in_array( $platform, $supported, true ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid platform.', 'clickreach-social-share-buttons' ) ), 400 );
		}

		// Increment platform click count
		$current_count = absint( get_post_meta( $post_id, '_ssbc_clicks_' . $platform, true ) );
		$new_count     = $current_count + 1;
		update_post_meta( $post_id, '_ssbc_clicks_' . $platform, $new_count );

		// Calculate total shares across all platforms
		$total = 0;
		foreach ( $supported as $key ) {
			$total += absint( get_post_meta( $post_id, '_ssbc_clicks_' . $key, true ) );
		}

		// Allow extensions/analytics hooks
		do_action( 'ssbc_after_click_tracked', $post_id, $platform, $new_count, $total );

		wp_send_json_success(
			array(
				'count'          => $new_count,
				'count_display'  => number_format_i18n( $new_count ),
				'total'          => $total,
				'total_display'  => number_format_i18n( $total ),
				'platform'       => $platform,
				'post_id'        => $post_id,
			)
		);
	}
}
