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

// For WordPress multisite networks.
if ( is_multisite() ) {
	delete_site_option( 'ssbc_settings' );
}
