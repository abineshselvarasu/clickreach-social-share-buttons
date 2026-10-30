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

// Delete plugin settings option.
delete_option( 'ssbc_settings' );

// Delete per-post click counters and display overrides.
$ssbc_meta_keys = array( '_ssbc_status', '_ssbc_platforms' );
foreach ( array( 'x', 'facebook', 'linkedin', 'whatsapp', 'pinterest', 'reddit', 'copy' ) as $ssbc_network ) {
	$ssbc_meta_keys[] = '_ssbc_clicks_' . $ssbc_network;
}
foreach ( $ssbc_meta_keys as $ssbc_meta_key ) {
	delete_post_meta_by_key( $ssbc_meta_key );
}

// For WordPress multisite networks.
if ( is_multisite() ) {
	delete_site_option( 'ssbc_settings' );
}
