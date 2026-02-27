<?php
/*
Plugin Name: Events Calendar Modules For Divi
Plugin URI:  https://eventscalendaraddons.com/divi/?utm_source=ecmd_plugin&utm_medium=readme&utm_campaign=demo&utm_content=top_view_demo
Description: A divi module to show your events in beautiful designs
Version:     1.1.5
Author:      Cool Plugins
Author URI:  https://coolplugins.net/?utm_source=ecmd_plugin&utm_medium=inside&utm_campaign=author_page&utm_content=plugins_list
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: events-calendar-modules-for-divi
Domain Path: /languages
Requires Plugins: the-events-calendar
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ECMD_V', '1.1.5' );
define( 'ECMD_DIR', plugin_dir_path( __FILE__ ) );
define( 'ECMD_URL', plugin_dir_url( __FILE__ ) );
define( 'ECMD_MODULE_URL', plugin_dir_url( __FILE__ ) . 'includes/modules' );
define( 'ECMD_MODULE_DIR', plugin_dir_path( __FILE__ ) . 'includes/modules' );
define('ECMD_FEEDBACK_API',"https://feedback.coolplugins.net/");

class ECMD_Events_Calendar_Modules_For_Divi {

	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'ecmd_check_pro_plugin' ), 1 );
		add_action( 'init', array( $this, 'ecmd_init' ) );
	}

	public function ecmd_check_pro_plugin() {
		if ( $this->ecmd_is_pro_plugin_active() ) {
			add_action( 'admin_notices', array( $this, 'ecmd_pro_plugin_active_notice' ) );
			deactivate_plugins( 'events-calendar-modules-for-divi/events-calendar-modules-for-divi.php' );
			return;
		}

		// Load Free Plugin If Pro Not Active
		self::includes();
		add_action( 'divi_extensions_init', array( $this, 'ecmd_initialize_extension' ) );
		add_action( 'admin_init', array( $this, 'ecmd_is_divi_theme_exist' ) );
		register_activation_hook( __FILE__, array( 'ECMD_Events_Calendar_Modules_For_Divi', 'ecmd_activate' ) );
	}

	public function ecmd_is_pro_plugin_active() {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		return class_exists( 'ECMD_Events_Calendar_Modules_For_Divi_Pro' )
			|| is_plugin_active( 'events-calendar-modules-for-divi-pro/events-calendar-modules-for-divi-pro.php' );
	}

	public function ecmd_pro_plugin_active_notice() {
		?>
		<div class="notice notice-warning is-dismissible">
			<p><strong>Events Calendar Modules For Divi Pro:</strong> is already active so no need to activate free version.</p>
		</div>
		<?php
	}

	public static function ecmd_activate() {
		update_option( 'ecmd-v', ECMD_V );
		update_option( 'ecmd-type', 'free' );
		update_option( 'ecmd-installDate', gmdate( 'Y-m-d h:i:s' ) );

		if (!get_option( 'ecmd_initial_save_version' ) ) {
			add_option( 'ecmd_initial_save_version', ECMD_V );
		}

		if(!get_option( 'ecmd_install_date' ) ) {
			add_option( 'ecmd_install_date', gmdate('Y-m-d h:i:s') );
		}
	}

	public function ecmd_is_divi_theme_exist() {
		if ( ! self::ecmd_is_theme_activate( 'Divi' ) ) {
			add_action( 'admin_notices', array( $this, 'ecmd_admin_notice_missing_divi_theme' ) );
		}
		if ( is_admin() ) {
			require_once ECMD_DIR . 'admin/feedback/admin-feedback-form.php';
		}
	}

	public function ecmd_init() {
		if (!get_option( 'ecmd_initial_save_version' ) ) {
			add_option( 'ecmd_initial_save_version', ECMD_V );
		}

		if(!get_option( 'ecmd_install_date' ) ) {
			add_option( 'ecmd_install_date', gmdate('Y-m-d h:i:s') );
		}
	}

	public function ecmd_initialize_extension() {
		require_once ECMD_DIR . '/includes/EventsCalendarModulesForDivi.php';
	}

	public static function ecmd_is_theme_activate( $target ) {
		$theme = wp_get_theme();
		if ( $theme->name === $target || stripos( $theme->parent_theme, $target ) !== false ) {
			return true;
		}
		return false;
	}

	public function ecmd_admin_notice_missing_divi_theme() {
		$message = esc_html__(
			'Events Calendar Modules For Divi requires Divi (Theme) to be installed and activated.',
			'events-calendar-modules-for-divi'
		);
		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', esc_html( $message ) );
	}

	public static function includes() {
		require_once ECMD_MODULE_DIR . '/assets-loader.php';
		new ECMD_AssetsLoader();
		if ( wp_get_theme()->get( 'Version' ) >= 5 ) {
			require_once ECMD_DIR . 'divi-5/divi-5.php';
			new ECMD_AssetsLoader_divi_5();
		} 
	}
}

new ECMD_Events_Calendar_Modules_For_Divi();
