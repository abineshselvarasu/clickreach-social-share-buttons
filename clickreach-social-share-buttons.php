<?php
/**
 * Plugin Name:       ClickReach – Social Share Buttons & Counter
 * Plugin URI:        https://wordpress.org/plugins/clickreach-social-share-buttons/
 * Description:       Lightweight, privacy-friendly social sharing buttons with live click counters. Maximize your social reach with zero bloat, responsive inline SVGs, and Core Web Vitals friendly performance.
 * Version:           1.0.1
 * Requires at least: 5.6
 * Requires PHP:      7.4
 * Author:            abineshselvarasu
 * Author URI:        https://profiles.wordpress.org/abineshselvarasu/
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       clickreach-social-share-buttons
 *
 * @package           ClickReach_Social_Share_Buttons
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define Plugin Constants
define( 'SSBC_VERSION', '1.0.1' );
define( 'SSBC_PLUGIN_FILE', __FILE__ );
define( 'SSBC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SSBC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'SSBC_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Check PHP Version Compatibility.
 */
if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
	add_action(
		'admin_notices',
		function () {
			?>
			<div class="notice notice-error">
				<p>
					<?php
					echo esc_html(
						sprintf(
							/* translators: %s: Minimum required PHP version */
							__( 'ClickReach – Social Share Buttons & Counter requires PHP version %s or higher to run securely. Please upgrade your PHP version.', 'clickreach-social-share-buttons' ),
							'7.4'
						)
					);
					?>
				</p>
			</div>
			<?php
		}
	);
	return;
}

/**
 * Autoload / Require core classes.
 */
require_once SSBC_PLUGIN_DIR . 'includes/class-ssbc-icons.php';
require_once SSBC_PLUGIN_DIR . 'includes/class-ssbc-settings.php';
require_once SSBC_PLUGIN_DIR . 'includes/class-ssbc-metabox.php';
require_once SSBC_PLUGIN_DIR . 'includes/class-ssbc-frontend.php';
require_once SSBC_PLUGIN_DIR . 'includes/class-ssbc-ajax.php';

/**
 * Main Plugin Class
 */
final class Social_Share_Buttons_Counter {

	/**
	 * Single instance of the class.
	 *
	 * @var Social_Share_Buttons_Counter|null
	 */
	private static $instance = null;

	/**
	 * Main instance accessor.
	 *
	 * @return Social_Share_Buttons_Counter
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->init_hooks();
	}

	/**
	 * Initialize WordPress hooks.
	 */
	private function init_hooks() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		add_filter( 'plugin_action_links_' . SSBC_PLUGIN_BASENAME, array( $this, 'add_plugin_action_links' ) );

		// Initialize components
		SSBC_Settings::init();
		SSBC_Metabox::init();
		SSBC_Frontend::init();
		SSBC_Ajax::init();
	}

	/**
	 * Enqueue admin scripts & styles.
	 *
	 * @param string $hook_suffix Current admin page hook.
	 */
	public function enqueue_admin_assets( $hook_suffix ) {
		// Only enqueue on post edit screens and plugin settings page
		if ( in_array( $hook_suffix, array( 'post.php', 'post-new.php', 'settings_page_clickreach-social-share-buttons' ), true ) ) {
			wp_enqueue_style(
				'ssbc-admin-css',
				SSBC_PLUGIN_URL . 'assets/css/ssbc-admin.css',
				array(),
				SSBC_VERSION
			);

			wp_enqueue_script(
				'ssbc-admin-js',
				SSBC_PLUGIN_URL . 'assets/js/ssbc-admin.js',
				array( 'jquery' ),
				SSBC_VERSION,
				true
			);
		}
	}

	/**
	 * Add "Settings" shortcut link in Plugins list table.
	 *
	 * @param array $links Existing action links.
	 * @return array Modified action links.
	 */
	public function add_plugin_action_links( $links ) {
		$settings_url = admin_url( 'options-general.php?page=clickreach-social-share-buttons' );
		$settings_link = '<a href="' . esc_url( $settings_url ) . '">' . esc_html__( 'Settings', 'clickreach-social-share-buttons' ) . '</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}
}

/**
 * Activation callback.
 */
function ssbc_activate_plugin() {
	// Set default settings if not already present
	if ( false === get_option( SSBC_Settings::OPTION_NAME ) ) {
		update_option( SSBC_Settings::OPTION_NAME, SSBC_Settings::get_defaults() );
	}
}
register_activation_hook( __FILE__, 'ssbc_activate_plugin' );

/**
 * Launch plugin instance.
 */
function ssbc_run_plugin() {
	return Social_Share_Buttons_Counter::get_instance();
}
add_action( 'plugins_loaded', 'ssbc_run_plugin' );
