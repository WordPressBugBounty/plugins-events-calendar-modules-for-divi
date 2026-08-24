<?php
/*
Plugin Name: Events Calendar Modules For Divi
Plugin URI:  https://eventscalendaraddons.com/divi/?utm_source=ecmd_plugin&utm_medium=readme&utm_campaign=demo&utm_content=top_view_demo
Description: A divi module to show your events in beautiful designs
Version:     1.2.0
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
define( 'ECMD_V', '1.2.0' );
define( 'ECMD_DIR', plugin_dir_path( __FILE__ ) );
define( 'ECMD_URL', plugin_dir_url( __FILE__ ) );
define( 'ECMD_PLUGIN_FILE', __FILE__ );
define( 'ECMD_MODULE_URL', plugin_dir_url( __FILE__ ) . 'includes/modules' );
define( 'ECMD_MODULE_DIR', plugin_dir_path( __FILE__ ) . 'includes/modules' );
define( 'ECMD_FEEDBACK_API', 'https://feedback.coolplugins.net/' );
register_activation_hook( ECMD_PLUGIN_FILE, array( 'ECMD_Events_Calendar_Modules_For_Divi', 'ecmd_activate' ) );
register_deactivation_hook( ECMD_PLUGIN_FILE, array( 'ECMD_Events_Calendar_Modules_For_Divi', 'ecmd_deactivate' ) );
class ECMD_Events_Calendar_Modules_For_Divi {
	public function __construct() {
		if ( is_admin() ) {
			require_once ECMD_DIR . 'admin/class-ecmd-eca-integration.php';
			ECMD_ECA_Integration::boot_admin();
		}
		$this->cpfm_feedback_cron_init();
		add_action( 'plugins_loaded', array( $this, 'ecmd_check_pro_plugin' ), 1 );
		add_action( 'init', array( $this, 'ecmd_init' ) );
	}
	public function ecmd_check_pro_plugin() {
		if ( $this->ecmd_is_pro_plugin_active() ) {
			add_action( 'admin_notices', array( $this, 'ecmd_pro_plugin_active_notice' ) );
			deactivate_plugins( 'events-calendar-modules-for-divi/events-calendar-modules-for-divi.php' );
			return;
		}
		self::includes();
		$this->cpfm_feedback_cron_init();
		add_action( 'init', array( $this, 'register_cpfm_notices' ), 999 );
		add_action( 'cpfm_after_opt_in_ecmd', array( $this, 'ecmd_handle_cpfm_opt_in' ) );
		add_action(
			'plugin_opt_in_events-calendar-modules-for-divi',
			array( $this, 'ecmd_plugin_opt_in' )
		);
		add_action( 'divi_extensions_init', array( $this, 'ecmd_initialize_extension' ) );
		add_action( 'admin_init', array( $this, 'ecmd_is_divi_theme_exist' ), 5 );
	}
	public function ecmd_is_pro_plugin_active() {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		return class_exists( 'ECMD_Events_Calendar_Modules_For_Divi_Pro' )
			|| is_plugin_active( 'cp-events-calendar-modules-for-divi-pro/cp-events-calendar-modules-for-divi-pro.php' )
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
		require_once ECMD_DIR . 'admin/ecmd-onboarding/class-ecmd-onboarding-page.php';
		if ( class_exists( 'ECMD_Onboarding_Page' ) && self::ecmd_is_theme_activate( 'Divi' ) ) {
			ECMD_Onboarding_Page::maybe_schedule_redirect();
		}
		update_option( 'ecmd-v', ECMD_V );
		update_option( 'ecmd-type', 'free' );
		update_option( 'ecmd-installDate', gmdate( 'Y-m-d h:i:s' ) );
		if ( ! get_option( 'ecmd_initial_save_version' ) ) {
			add_option( 'ecmd_initial_save_version', ECMD_V );
		}
		if ( ! get_option( 'ecmd_install_date' ) ) {
			add_option( 'ecmd_install_date', gmdate( 'Y-m-d h:i:s' ) );
		}
			// Re-schedule usage cron only when THIS plugin was already opted in.
		// Activation must never phone home — same contract as Template Events Calendar.
		if ( get_option( 'ecmd_cpfm_feedback_data' ) && ! wp_next_scheduled( 'ecmd_extra_data_update' ) ) {
			wp_schedule_event( time(), 'every_30_days', 'ecmd_extra_data_update' );
		}
	}
	public static function ecmd_deactivate() {
		// Deactivation must never phone home — clear cron only (ECT pattern).
		// Deactivation feedback is sent only when the user submits the CPFM modal.
		if ( wp_next_scheduled( 'ecmd_extra_data_update' ) ) {
			wp_clear_scheduled_hook( 'ecmd_extra_data_update' );
		}
	}
	public function ecmd_is_divi_theme_exist() {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		if ( ! self::ecmd_is_theme_activate( 'Divi' ) ) {
			delete_transient( 'ecmd_onboarding_redirect' );
			add_action( 'admin_notices', array( $this, 'ecmd_admin_notice_missing_divi_theme' ) );
			deactivate_plugins( plugin_basename( ECMD_PLUGIN_FILE ) );
			return;
		}
	}
	public function ecmd_init() {
		if ( ! get_option( 'ecmd_initial_save_version' ) ) {
			add_option( 'ecmd_initial_save_version', ECMD_V );
		}
		if ( ! get_option( 'ecmd_install_date' ) ) {
			add_option( 'ecmd_install_date', gmdate( 'Y-m-d h:i:s' ) );
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
		deactivate_plugins( plugin_basename( ECMD_PLUGIN_FILE ) );
	}
	/**
	 * Boot CPFM loader, usage cron registration, and onboarding data helpers.
	 * Loaded when the free tier runs so WP-Cron can execute without is_admin().
	 *
	 * @return void
	 */
	public function cpfm_feedback_cron_init() {
		if ( ! class_exists( 'CPFM_Loader' ) ) {
			$file = ECMD_DIR . 'admin/cpfm-feedback/class-cpfm-loader.php';
			if ( file_exists( $file ) ) {
				require_once $file;
			}
		}
		if ( class_exists( 'CPFM_Loader' ) ) {
			CPFM_Loader::load();
		}
		$this->ecmd_register_cpfm_usage_cron();
		if ( ! class_exists( 'ECMD_Onboarding_Cpfm_Data', false ) ) {
			$onboarding_file = ECMD_DIR . 'admin/ecmd-onboarding/includes/class-ecmd-onboarding-cpfm-data.php';
			if ( file_exists( $onboarding_file ) ) {
				require_once $onboarding_file;
			}
		}
	}
	/**
	 * Register the shared usage-data cron (admin + WP-Cron).
	 *
	 * @return void
	 */
	public function ecmd_register_cpfm_usage_cron() {
		static $usage_cron_registered = false;
		if ( $usage_cron_registered || ! class_exists( 'CPFM_Usage_Cron' ) ) {
			return;
		}
		$usage_cron_registered = true;
		CPFM_Usage_Cron::cpfm_register(
			array(
				'id'                     => 'ecmd',
				'plugin_name'            => 'Events Calendar Modules For Divi',
				'version'                => ECMD_V,
				'api'                    => ECMD_FEEDBACK_API,
				'cron_hook'              => 'ecmd_extra_data_update',
				'consent_master_option'  => 'cpfm_opt_in_choice_cool_events',
				'consent_callback'       => array( $this, 'ecmd_has_usage_tracking_consent' ),
				'install_date_option'    => 'ecmd-install-date',
				'initial_version_option' => 'ecmd_initial_save_version',
				'site_key'               => '99',
				'onboarding_data'        => 'cpfm_onboarding_preferences_cool_events',
			)
		);
	}
	/**
	 * Whether usage-data sharing is enabled for Divi Modules Free.
	 *
	 * @return bool
	 */
	public function ecmd_has_usage_tracking_consent() {
		if ( get_option( 'ecmd_cpfm_feedback_data' ) ) {
			return true;
		}
		return ( 'yes' === get_option( 'cpfm_opt_in_choice_cool_events' ) );
	}
	/**
	 * Schedule the usage tracking cron when it is not already scheduled.
	 *
	 * @return void
	 */
	public function ecmd_maybe_schedule_tracking_cron() {
		$this->ecmd_register_cpfm_usage_cron();
		if ( class_exists( 'CPFM_Usage_Cron' ) ) {
			CPFM_Usage_Cron::cpfm_schedule_event( 'ecmd_extra_data_update' );
		}
	}
	/**
	 * Opt-in handler: persist consent, send first payload, schedule cron.
	 *
	 * @param string $category Notice category key.
	 * @return void
	 */
	public function ecmd_handle_cpfm_opt_in( $category ) {
		if ( 'cool_events' !== $category ) {
			return;
		}
		update_option( 'ecmd_cpfm_feedback_data', true, false );
		do_action( 'ecmd_extra_data_update' );
		$this->ecmd_maybe_schedule_tracking_cron();
	}
	/**
	 * Settings opt-in toggle on the Events Addons dashboard.
	 *
	 * @return void
	 */
	public function ecmd_plugin_opt_in() {
		update_option( 'ecmd_cpfm_feedback_data', true, false );
		$this->ecmd_register_cpfm_usage_cron();
		if ( class_exists( 'CPFM_Usage_Cron' ) ) {
			CPFM_Usage_Cron::cpfm_schedule_event( 'ecmd_extra_data_update' );
		}
	}
	/**
	 * Register CPFM notices (opt-in panel, review prompt, deactivation survey).
	 *
	 * @return void
	 */
	public function register_cpfm_notices() {
		if ( ! is_admin() ) {
			return;
		}
		static $registered = false;
		if ( $registered ) {
			return;
		}
		$registered = true;
		$notice_pages = class_exists( 'ECMD_ECA_Integration' )
			? ECMD_ECA_Integration::admin_page_slugs()
			: array( 'cool-plugins-events-addon', 'ecmd-onboarding' );
		add_action(
			'cpfm_register_notice',
			function () use ( $notice_pages ) {
				if ( ! class_exists( 'CPFM_Feedback_Notice' ) || ! current_user_can( 'manage_options' ) ) {
					return;
				}
				$notice = array(
					'title'          => __( 'Events Addons By Cool Plugins', 'events-calendar-modules-for-divi' ),
					'message'        => __( 'Help us make this plugin more compatible with your site by sharing non-sensitive site data.', 'events-calendar-modules-for-divi' ),
					'pages'          => $notice_pages,
					'always_show_on' => $notice_pages,
					'plugin_name'    => 'ecmd',
				);
				CPFM_Feedback_Notice::cpfm_register_notice( 'cool_events', $notice );
				if ( ! isset( $GLOBALS['cool_plugins_feedback'] ) ) {
					$GLOBALS['cool_plugins_feedback'] = array(); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
				}
				$GLOBALS['cool_plugins_feedback']['cool_events'][] = $notice; // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
			}
		);
		if ( class_exists( 'CPFM_Deactivation_Feedback' ) ) {
			$name = 'Events Calendar Modules For Divi';
			CPFM_Deactivation_Feedback::cpfm_register(
				array(
					'id'                     => 'ecmd',
					'slug'                   => 'events-calendar-modules-for-divi',
					'plugin_name'            => $name,
					'version'                =>  ECMD_V,
					'api'                    => ECMD_FEEDBACK_API,
					'site_key'               => '99',
					'install_date_option'    => 'ecmd_install_date',
					'initial_version_option' => 'ecmd_initial_save_version',
					'onboarding_data'        => 'cpfm_onboarding_preferences_cool_events',
					'reasons'                => array(
						'not_working'  => array(
							'title'       => __( "The plugin isn't working", 'events-calendar-modules-for-divi' ),
							'placeholder' => __( 'Which problem did you run into? We read every reply.', 'events-calendar-modules-for-divi' ),
						),
						'not_expected' => array(
							'title'       => __( "It didn't do what I expected", 'events-calendar-modules-for-divi' ),
							'placeholder' => __( 'What were you hoping it would do?', 'events-calendar-modules-for-divi' ),
						),
						'found_better' => array(
							'title'       => __( 'I found a better plugin', 'events-calendar-modules-for-divi' ),
							'placeholder' => __( 'Mind sharing which one?', 'events-calendar-modules-for-divi' ),
						),
						'temporary'    => array(
							'title'       => __( "It's a temporary deactivation", 'events-calendar-modules-for-divi' ),
							'placeholder' => '',
						),
						'other'        => array(
							'title'       => __( 'Another reason', 'events-calendar-modules-for-divi' ),
							'placeholder' => __( 'Please tell us more', 'events-calendar-modules-for-divi' ),
						),
					),
					'i18n'                   => array(
						'title'        => __( 'Before you go…', 'events-calendar-modules-for-divi' ),
						/* translators: %s: plugin name (bold). */
						'intro'        => __( 'What made you deactivate %s? Your answer helps us fix it.', 'events-calendar-modules-for-divi' ),
						'submit'       => __( 'Submit & Deactivate', 'events-calendar-modules-for-divi' ),
						'skip'         => __( 'Skip & Deactivate', 'events-calendar-modules-for-divi' ),
						'deactivating' => __( 'Deactivating…', 'events-calendar-modules-for-divi' ),
						'pick_reason'  => __( 'Please choose a reason.', 'events-calendar-modules-for-divi' ),
						'close_label'  => __( 'Close', 'events-calendar-modules-for-divi' ),
						/* translators: %s: company name. */
						'byline'       => __( 'A plugin by %s', 'events-calendar-modules-for-divi' ),
						'consent'      => __( 'Submitting shares your reason plus your site URL, admin email and basic environment details (PHP, WordPress, active plugins). Skip & Deactivate sends nothing.', 'events-calendar-modules-for-divi' ),
					),
				)
			);
		}
		if ( ! class_exists( 'CPFM_Review' ) ) {
			$review = ECMD_DIR . 'admin/cpfm-feedback/class-cpfm-review.php';
			if ( file_exists( $review ) ) {
				require_once $review;
			}
		}
		if ( class_exists( 'CPFM_Review' ) ) {
			$name = 'Events Calendar Modules For Divi';
			CPFM_Review::cpfm_register(
				array(
					'id'          => 'ecmd',
					'plugin_file' => ECMD_PLUGIN_FILE,
					'plugin_name' => $name,
					'review_url'  => 'https://wordpress.org/support/plugin/events-calendar-modules-for-divi/reviews/#new-post',
					'capability'  => 'activate_plugins',
					'quiet_days'  => 0,
					'own_screens' => array(
						'toplevel_page_cool-plugins-events-addon',
						'events-addons_page_ecmd-onboarding',
						'cool-plugins-events-addon_page_ecmd-onboarding',
					),
					'trigger'     => array(
						'type'  => 'install_age',
						'hours' => 24,
					),
					'notice'      => array(
						'enabled'        => true,
						'template'       => 'two_step',
						'screens'        => array(
							'plugins',
							'toplevel_page_cool-plugins-events-addon',
							'events-addons_page_ecmd-onboarding',
							'cool-plugins-events-addon_page_ecmd-onboarding',
						),
						'inline_screens' => array(),
					),
					'row'         => array( 'enabled' => true ),
					'legacy'      => array(
						'done_options'  => array(
							'ecmd-Boxes-ratingDiv' => array( 'yes', 'done', 'dismissed' ),
						),
						'install_dates' => array( 'ecmd-installDate', 'ecmd_install_date' ),
						'mirror_write'  => array( 'ecmd-Boxes-ratingDiv' => 'yes' ),
					),
					'i18n'        => array(
						'like_question' => sprintf(
							/* translators: %s: plugin name. */
							__( 'Do you like the %s plugin?', 'events-calendar-modules-for-divi' ),
							$name
						),
						'yes_button'    => __( 'Yes, I like it', 'events-calendar-modules-for-divi' ),
						'dismiss_link'  => __( 'Not good, dismiss', 'events-calendar-modules-for-divi' ),
						'later_link'    => __( 'Ask me later', 'events-calendar-modules-for-divi' ),
						'thanks_line'   => __( 'That is great to hear! A quick review on WordPress.org would really help us.', 'events-calendar-modules-for-divi' ),
						'submit_button' => __( 'Submit review', 'events-calendar-modules-for-divi' ),
						'no_link'       => __( 'I do not like it, dismiss', 'events-calendar-modules-for-divi' ),
						'row_question'  => __( 'Do you like this plugin?', 'events-calendar-modules-for-divi' ),
						'inline_title'  => sprintf(
							/* translators: %s: plugin name. */
							__( 'Enjoying %s?', 'events-calendar-modules-for-divi' ),
							$name
						),
						'inline_text'   => __( 'A short review helps other event organisers find it.', 'events-calendar-modules-for-divi' ),
						'close_label'   => __( 'Close', 'events-calendar-modules-for-divi' ),
					),
				)
			);
		}
	}
	public static function includes() {
		require_once ECMD_MODULE_DIR . '/assets-loader.php';
		new ECMD_AssetsLoader();
		if ( wp_get_theme( 'Divi' )->get( 'Version' ) >= 5 ) {
			require_once ECMD_DIR . 'divi-5/divi-5.php';
			new ECMD_AssetsLoader_divi_5();
		}
		require_once ECMD_DIR . 'admin/marketing/marketing-contact-form-extender.php';
	}
}
new ECMD_Events_Calendar_Modules_For_Divi();
