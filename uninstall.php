<?php
/**
 * Uninstallation cleanup script for ClickReach – Social Share Buttons & Counter.
 *
 * Triggered automatically when the plugin is deleted from the WordPress admin.
 * Removes stored plugin options and metadata without leaving orphan database rows.
 *
 * @package ClickReach_Social_Share_Buttons
 * @since   1.0.0
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

/**
 * Delete plugin settings, per-post click counters and display overrides for the current site.
 */
function ssbc_uninstall_site() {
	delete_option( 'ssbc_settings' );

	$meta_keys = array( '_ssbc_status' );
	foreach ( array( 'x', 'facebook', 'linkedin', 'whatsapp', 'pinterest', 'reddit', 'copy' ) as $network ) {
		$meta_keys[] = '_ssbc_clicks_' . $network;
	}
	foreach ( $meta_keys as $meta_key ) {
		delete_post_meta_by_key( $meta_key );
	}
}

// For WordPress multisite networks, clean up every site.
if ( is_multisite() ) {
	foreach ( get_sites( array( 'fields' => 'ids', 'number' => 0 ) ) as $ssbc_site_id ) {
		switch_to_blog( $ssbc_site_id );
		ssbc_uninstall_site();
		restore_current_blog();
	}
} else {
	ssbc_uninstall_site();
}
