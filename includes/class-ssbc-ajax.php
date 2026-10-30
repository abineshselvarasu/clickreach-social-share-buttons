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
	 * Window in which repeat clicks from the same visitor are ignored.
	 *
	 * @var int
	 */
	const RATE_WINDOW = HOUR_IN_SECONDS;

	/**
	 * Maximum counted clicks per visitor (IP) within the rate window, across all posts.
	 *
	 * @var int
	 */
	const MAX_CLICKS_PER_WINDOW = 30;

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
		// Nonce validation for logged-in users only. Logged-out visitors all share the same
		// nonce and cached pages serve expired ones, so anonymous abuse is handled by the
		// per-visitor rate limit below instead.
		if ( is_user_logged_in() ) {
			check_ajax_referer( 'ssbc_nonce', 'nonce' );
		}

		// Input validation
		// phpcs:disable WordPress.Security.NonceVerification.Missing -- Public counter; protected by rate limiting.
		$raw_id   = isset( $_POST['post_id'] ) ? wp_unslash( $_POST['post_id'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Validated as digits below.
		$post_id  = ( is_string( $raw_id ) && ctype_digit( $raw_id ) ) ? absint( $raw_id ) : 0;
		$platform = ( isset( $_POST['platform'] ) && is_string( $_POST['platform'] ) ) ? sanitize_key( $_POST['platform'] ) : '';
		// phpcs:enable WordPress.Security.NonceVerification.Missing

		if ( ! $post_id || 'publish' !== get_post_status( $post_id ) || ! is_post_type_viewable( get_post_type( $post_id ) ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid Post ID.', 'clickreach-social-share-buttons' ) ), 400 );
		}

		$supported = array_keys( SSBC_Icons::get_supported_networks() );
		if ( ! in_array( $platform, $supported, true ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid platform.', 'clickreach-social-share-buttons' ) ), 400 );
		}

		$current_count = absint( get_post_meta( $post_id, '_ssbc_clicks_' . $platform, true ) );
		$counted       = self::register_visitor_click( $post_id, $platform );

		// Increment platform click count
		if ( $counted ) {
			$current_count++;
			update_post_meta( $post_id, '_ssbc_clicks_' . $platform, $current_count );
		}
		$new_count = $current_count;

		// Calculate total shares across all platforms
		$total = 0;
		foreach ( $supported as $key ) {
			$total += absint( get_post_meta( $post_id, '_ssbc_clicks_' . $key, true ) );
		}

		// Allow extensions/analytics hooks
		if ( $counted ) {
			do_action( 'ssbc_after_click_tracked', $post_id, $platform, $new_count, $total );
		}

		wp_send_json_success(
			array(
				'count'          => $new_count,
				'count_display'  => number_format_i18n( $new_count ),
				'total'          => $total,
				'total_display'  => number_format_i18n( $total ),
				'platform'       => $platform,
				'post_id'        => $post_id,
				'counted'        => $counted,
			)
		);
	}

	/**
	 * Record a click for the current visitor and report whether it should be counted.
	 *
	 * Visitors are identified by a salted hash of their IP address, stored only in
	 * short-lived transients, so no personal data is persisted.
	 *
	 * @param int    $post_id  Post ID.
	 * @param string $platform Network key.
	 * @return bool True if the click should increase the counter.
	 */
	private static function register_visitor_click( $post_id, $platform ) {
		$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';

		/**
		 * Filter the visitor IP used for rate limiting (e.g. to read a trusted proxy header).
		 *
		 * @param string $ip Visitor IP address.
		 */
		$ip = (string) apply_filters( 'ssbc_client_ip', $ip );

		if ( '' === $ip ) {
			return true;
		}

		$visitor = substr( hash_hmac( 'sha256', $ip, wp_salt( 'nonce' ) ), 0, 20 );

		// One counted click per visitor, post and network within the window.
		$click_key = 'ssbc_c_' . substr( md5( $visitor . '|' . $post_id . '|' . $platform ), 0, 20 );
		if ( false !== get_transient( $click_key ) ) {
			return false;
		}

		// Overall cap per visitor, so a single client cannot flood counts across many posts.
		$cap_key = 'ssbc_v_' . $visitor;
		$clicks  = absint( get_transient( $cap_key ) );
		if ( $clicks >= self::MAX_CLICKS_PER_WINDOW ) {
			return false;
		}

		set_transient( $cap_key, $clicks + 1, self::RATE_WINDOW );
		set_transient( $click_key, 1, self::RATE_WINDOW );

		return true;
	}
}
