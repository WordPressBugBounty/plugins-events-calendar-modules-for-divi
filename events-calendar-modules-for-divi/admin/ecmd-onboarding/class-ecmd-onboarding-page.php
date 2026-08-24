<?php
/**
 * ECMD onboarding wizard admin page.
 *
 * @package Events_Calendar_Modules_For_Divi
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ECMD_Onboarding_Page' ) ) {

	/**
	 * Getting Started wizard for Events Calendar Modules for Divi.
	 */
	final class ECMD_Onboarding_Page {

		const PAGE_SLUG          = 'ecmd-onboarding';
		const LEGACY_PAGE_SLUG   = 'ecmd-getting-started';
		const COMPLETED_OPTION   = 'ecmd_onboarding_completed';
		const REDIRECT_TRANSIENT = 'ecmd_onboarding_redirect';
		const PAGE_ID_OPTION     = 'ecmd_onboarding_page_id';
		const PREFS_OPTION       = 'ecmd_onboarding_preferences';

		/**
		 * Register hooks.
		 */
		public static function init() {
			require_once __DIR__ . '/includes/class-ecmd-onboarding-draft-page.php';

			add_action( 'admin_menu', array( __CLASS__, 'register_menu' ), 15 );
			add_action( 'admin_init', array( __CLASS__, 'maybe_redirect' ), 20 );
			add_filter( 'admin_body_class', array( __CLASS__, 'admin_body_class' ) );
			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue' ) );
			add_action( 'admin_notices', array( __CLASS__, 'suppress_foreign_notices' ), PHP_INT_MIN );
			add_action( 'all_admin_notices', array( __CLASS__, 'suppress_foreign_notices' ), PHP_INT_MIN );
			add_action( 'wp_ajax_ecmd_onboarding_complete', array( __CLASS__, 'ajax_complete' ) );
			add_action( 'wp_ajax_ecmd_onboarding_create_page', array( __CLASS__, 'ajax_create_page' ) );
			add_action( 'wp_ajax_ecmd_onboarding_save_preferences', array( __CLASS__, 'ajax_save_preferences' ) );
		}

		/**
		 * Schedule a one-shot post-activation redirect.
		 * Call from the activation hook BEFORE writing install options.
		 *
		 * Fresh install  → Getting Started (onboarding).
		 * Reactivation   → shared Events Addons dashboard.
		 *
		 * @return void
		 */
		public static function maybe_schedule_redirect() {
			if ( ! self::is_divi_theme_active() ) {
				return;
			}

			// Programmatic activate (dashboard / free-wizard AJAX) — UI owns navigation.
			if ( wp_doing_ajax() ) {
				return;
			}

			$is_fresh_install = ( false === get_option( 'ecmd-installDate', false ) )
				&& ( false === get_option( 'ecmd-v', false ) );

			$target = $is_fresh_install ? 'onboarding' : 'dashboard';
			// Short TTL: WP reloads admin immediately after Activate, then maybe_redirect()
			// consumes this. If it expires, the user stays on the dashboard (onboarding
			// remains available from the menu).
			set_transient( self::REDIRECT_TRANSIENT, $target, MINUTE_IN_SECONDS );
		}

		/**
		 * Consume the post-activation redirect transient (one shot).
		 *
		 * @return void
		 */
		public static function maybe_redirect() {
			$target = get_transient( self::REDIRECT_TRANSIENT );
			if ( ! $target ) {
				return;
			}

			if ( ! self::is_divi_theme_active() ) {
				delete_transient( self::REDIRECT_TRANSIENT );
				return;
			}

			delete_transient( self::REDIRECT_TRANSIENT );

			if ( wp_doing_ajax() || wp_doing_cron() || is_network_admin() ) {
				return;
			}
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- bulk-activation marker only.
			if ( isset( $_GET['activate-multi'] ) ) {
				return;
			}
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			// Already opening Getting Started (e.g. Continue after Activate Pro) — don't bounce away.
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- admin page slug only.
			$page_requested = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';
			if ( self::PAGE_SLUG === $page_requested || self::LEGACY_PAGE_SLUG === $page_requested ) {
				return;
			}

			// Fresh installs that somehow already finished onboarding still land on the dashboard.
			if ( 'onboarding' === $target && get_option( self::COMPLETED_OPTION ) ) {
				$target = 'dashboard';
			}

			$page = ( 'onboarding' === $target )
				? self::PAGE_SLUG
				: ( class_exists( 'ECA_Dashboard_Page' ) ? ECA_Dashboard_Page::PAGE_SLUG : 'cool-plugins-events-addon' );

			wp_safe_redirect( admin_url( 'admin.php?page=' . $page ) );
			exit;
		}

		/**
		 * @param string $classes Space-separated admin body classes.
		 * @return string
		 */
		public static function admin_body_class( $classes ) {
			if ( ! self::is_onboarding_screen() ) {
				return $classes;
			}

			return $classes . ' ecmd-onboarding-page';
		}

		/**
		 * @return bool
		 */
		private static function is_onboarding_screen() {
			if ( ! is_admin() ) {
				return false;
			}

			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only screen detection.
			$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';

			return self::PAGE_SLUG === $page || self::LEGACY_PAGE_SLUG === $page;
		}

		/**
		 * Strip third-party admin notices on the fullscreen onboarding screen.
		 */
		public static function suppress_foreign_notices() {
			if ( ! self::is_onboarding_screen() ) {
				return;
			}

			global $wp_filter;

			$current_hook = current_action();
			if ( empty( $current_hook ) || empty( $wp_filter[ $current_hook ] ) || ! ( $wp_filter[ $current_hook ] instanceof WP_Hook ) ) {
				return;
			}

			foreach ( $wp_filter[ $current_hook ]->callbacks as $priority => $callbacks ) {
				foreach ( $callbacks as $callback ) {
					$function = $callback['function'] ?? null;

					if ( is_array( $function ) && isset( $function[0], $function[1] )
						&& $function[0] === __CLASS__ && 'suppress_foreign_notices' === $function[1] ) {
						continue;
					}

					if ( self::is_owned_notice_callback( $function ) ) {
						continue;
					}

					remove_action( $current_hook, $function, $priority );
				}
			}
		}

		/**
		 * @param callable|array|string|null $function Registered notice callback.
		 * @return bool
		 */
		private static function is_owned_notice_callback( $function ) {
			if ( ! is_array( $function ) || ! isset( $function[0] ) ) {
				return false;
			}

			$owner = $function[0];
			$class = is_object( $owner ) ? get_class( $owner ) : (string) $owner;

			return 0 === strpos( $class, 'ECMD_' )
				|| 0 === strpos( $class, 'ECA_' )
				|| 0 === strpos( $class, 'ECMD_Events_Calendar_Modules_For_Divi' );
		}

		/**
		 * Register a hidden admin page.
		 */
		public static function register_menu() {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			$hook = add_submenu_page(
				null,
				__( 'Setup — Events Calendar Modules for Divi', 'events-calendar-modules-for-divi' ),
				__( 'Getting Started', 'events-calendar-modules-for-divi' ),
				'manage_options',
				self::PAGE_SLUG,
				array( __CLASS__, 'render' )
			);
			if ( $hook ) {
				add_action( 'load-' . $hook, array( __CLASS__, 'set_admin_title' ) );
			}

			add_submenu_page(
				null,
				__( 'Getting Started', 'events-calendar-modules-for-divi' ),
				__( 'Getting Started', 'events-calendar-modules-for-divi' ),
				'manage_options',
				self::LEGACY_PAGE_SLUG,
				array( __CLASS__, 'render_legacy_alias' )
			);
		}

		/**
		 * Give the hidden wizard page a real title before admin-header.php runs
		 * (avoids PHP 8.1+ strip_tags(null) deprecation).
		 *
		 * @return void
		 */
		public static function set_admin_title() {
			// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Sets admin title for hidden wizard screen (avoids PHP 8.1+ strip_tags(null)).
			$GLOBALS['title'] = __( 'Getting Started', 'events-calendar-modules-for-divi' );
		}

		/**
		 * Redirect legacy slug to the current onboarding page.
		 *
		 * @return void
		 */
		public static function render_legacy_alias() {
			wp_safe_redirect( admin_url( 'admin.php?page=' . self::PAGE_SLUG ) );
			exit;
		}

		/**
		 * Render wizard markup.
		 */
		public static function render() {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'You do not have permission to access this page.', 'events-calendar-modules-for-divi' ) );
			}

			$markup = self::get_wizard_markup();

			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted admin template from plugin package.
			echo $markup;
		}

		/**
		 * @param string $hook_suffix Admin hook.
		 */
		public static function enqueue( $hook_suffix ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- screen gate only.
			$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';
			if ( self::PAGE_SLUG !== $page && false === strpos( (string) $hook_suffix, self::PAGE_SLUG ) ) {
				return;
			}

			$base = ECMD_URL . 'admin/ecmd-onboarding/assets/';
			$ver  = defined( 'ECA_DASHBOARD_VERSION' ) ? ECA_DASHBOARD_VERSION : ECMD_V;

			wp_enqueue_style( 'dashicons' );
			wp_enqueue_style( 'ecmd-onboarding-base', $base . 'css/ecmd-base.css', array(), $ver );
			wp_enqueue_style( 'ecmd-onboarding-wizard', $base . 'css/ecmd-wizard.css', array( 'ecmd-onboarding-base' ), $ver );
			wp_enqueue_style( 'ecmd-onboarding-fullscreen', $base . 'css/ecmd-admin-fullscreen.css', array( 'ecmd-onboarding-wizard' ), $ver );

			wp_enqueue_script( 'ecmd-onboarding-wizard', $base . 'js/ecmd-wizard.js', array(), $ver, true );
			wp_enqueue_script( 'ecmd-onboarding-logic', $base . 'js/ecmd-onboarding.js', array( 'ecmd-onboarding-wizard' ), $ver, true );

			$config = array(
				'slug'            => 'events-modules-divi',
				'steps'           => array(
					array( 'id' => 'mode', 'label' => 'Setup' ),
					array( 'id' => 'layout-query', 'label' => 'Layout & Query' ),
					array( 'id' => 'success', 'label' => 'Done' ),
				),
				'defaultTelemetry' => true,
				'assetBase'       => ECMD_URL . 'admin/ecmd-onboarding/assets/images/',
				'siblingIconBase' => ECMD_URL . 'admin/ecmd-onboarding/assets/images/',
				'summaryLabels'   => array(
					'mode'   => 'Setup',
					'layout' => 'Layout',
					'time'   => 'Which events',
				),
			);

			$preview_state = self::get_preview_state();

			wp_add_inline_script(
				'ecmd-onboarding-wizard',
				'window.ECMD_WIZARD = ' . wp_json_encode( $config ) . ';' . "\n" . self::bootstrap_script( $preview_state ),
				'before'
			);

			wp_localize_script(
				'ecmd-onboarding-logic',
				'ECMD_ONBOARDING',
				array(
					'ajaxUrl'         => admin_url( 'admin-ajax.php' ),
					'nonceComplete'   => wp_create_nonce( 'ecmd_onboarding_complete' ),
					'nonceCreatePage' => wp_create_nonce( 'ecmd_onboarding_create_page' ),
					'nonceSavePrefs'  => wp_create_nonce( 'ecmd_onboarding_save_preferences' ),
					'dashboardUrl'    => admin_url( 'admin.php?page=' . ECA_Dashboard_Page::PAGE_SLUG ),
					'previewState'    => $preview_state,
					'proActive'       => self::is_pro_active(),
					'proInit'         => class_exists( 'ECA_Addon_Map' ) ? ECA_Addon_Map::tier_init( 'divi', 'pro' ) : '',
					'nonceActivate'   => wp_create_nonce( 'eca_dashboard_plugin' ),
					'telemetry'       => class_exists( 'ECMD_Onboarding_Cpfm_Data' )
						? ECMD_Onboarding_Cpfm_Data::get_telemetry_localize()
						: array(
							'show'    => true,
							'checked' => true,
							'choice'  => null,
						),
					'page'            => self::get_page_state(),
					'i18n'            => array(
						'creating'    => __( 'Creating page…', 'events-calendar-modules-for-divi' ),
						'updating'    => __( 'Updating page…', 'events-calendar-modules-for-divi' ),
						'createDraft' => __( 'Create Draft Page', 'events-calendar-modules-for-divi' ),
						'updateDraft' => __( 'Update Draft Page', 'events-calendar-modules-for-divi' ),
					),
				)
			);
		}

		/**
		 * Draft page state so Step 2 can label Create / Update.
		 *
		 * @return array{exists: bool, isDraft: bool, isPublished: bool, id: int}
		 */
		private static function get_page_state() {
			$id     = (int) get_option( self::PAGE_ID_OPTION, 0 );
			$status = $id ? get_post_status( $id ) : false;

			return array(
				'exists'      => (bool) $status,
				'isDraft'     => ( 'draft' === $status ),
				'isPublished' => ( $status && 'draft' !== $status && 'trash' !== $status ),
				'id'          => $id,
			);
		}

		/**
		 * @param string $preview_state Server-detected preview state.
		 * @return string
		 */
		private static function bootstrap_script( $preview_state ) {
			$ps = esc_js( $preview_state );

			return "(function(){\n" .
				"document.body.setAttribute('data-preview-state','{$ps}');\n" .
				"try{localStorage.removeItem('ecmd-preview-state:wizard-events-modules-divi');}catch(e){}\n" .
				"var slug=(window.ECMD_WIZARD&&window.ECMD_WIZARD.slug)||'events-modules-divi';\n" .
				"try{var raw=localStorage.getItem('ecmd:wizard:'+slug+':state');if(raw){var s=JSON.parse(raw);var editor=s&&s.selections&&s.selections.editor;if(editor){document.body.setAttribute('data-editor-selected',editor);var opt=document.querySelector('.ecmd-editor-option[data-value=\"'+editor+'\"]');if(opt){document.querySelectorAll('.ecmd-editor-option.is-selected').forEach(function(el){el.classList.remove('is-selected');});opt.classList.add('is-selected');}}}var dm=localStorage.getItem('ecmd:wizard:'+slug+':displayMode');if(dm)document.body.setAttribute('data-display-mode',dm);var pc=localStorage.getItem('ecmd:wizard:'+slug+':pageCreated');if(pc)document.body.setAttribute('data-page-created','true');}catch(e){}\n" .
				"}());";
		}

		/**
		 * Load wizard markup from the PHP view (dynamic URLs / preview state).
		 *
		 * @return string
		 */
		private static function get_wizard_markup() {
			$path = __DIR__ . '/views/wizard-markup.php';
			if ( ! file_exists( $path ) ) {
				return '<div class="wrap"><p>' . esc_html__( 'Onboarding markup missing.', 'events-calendar-modules-for-divi' ) . '</p></div>';
			}

			$dashboard_url = admin_url(
				'admin.php?page=' . ( class_exists( 'ECA_Dashboard_Page' ) ? ECA_Dashboard_Page::PAGE_SLUG : 'cool-plugins-events-addon' )
			);
			$images        = ECMD_URL . 'admin/ecmd-onboarding/assets/images/';
			$plugin_icon   = $images . 'events-calendar-modules-for-divi.svg';
			$divi_icon     = $images . 'divi-icon.png';
			$preview_state  = self::get_preview_state();
			$show_telemetry = ! ( class_exists( 'ECMD_Onboarding_Cpfm_Data' ) && ! ECMD_Onboarding_Cpfm_Data::should_show_telemetry() );

			ob_start();
			include $path;
			return (string) ob_get_clean();
		}

		/**
		 * @return bool
		 */
		private static function is_pro_active() {
			if ( ! function_exists( 'is_plugin_active' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			return class_exists( 'ECMD_Events_Calendar_Modules_For_Divi_Pro' )
				|| is_plugin_active( 'cp-events-calendar-modules-for-divi-pro/cp-events-calendar-modules-for-divi-pro.php' )
				|| is_plugin_active( 'events-calendar-modules-for-divi-pro/events-calendar-modules-for-divi-pro.php' );
		}

		/**
		 * @return string default|pro-inactive|pro-installed
		 */
		private static function get_preview_state() {
			if ( self::is_pro_active() ) {
				return 'pro-installed';
			}

			if ( class_exists( 'ECA_Addon_Map' ) && 'inactive' === ECA_Addon_Map::tier_status( 'divi', 'pro' ) ) {
				return 'pro-inactive';
			}

			return 'default';
		}

		/**
		 * AJAX: persist wizard completion.
		 */
		public static function ajax_complete() {
			check_ajax_referer( 'ecmd_onboarding_complete', 'nonce' );
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( array( 'message' => __( 'Permission denied.', 'events-calendar-modules-for-divi' ) ) );
			}

			update_option( self::COMPLETED_OPTION, true, false );

			wp_send_json_success( array( 'redirect' => admin_url( 'admin.php?page=' . ECA_Dashboard_Page::PAGE_SLUG ) ) );
		}

		/**
		 * AJAX: save telemetry choice and selections into shared Cool Events prefs + cron.
		 */
		public static function ajax_save_preferences() {
			check_ajax_referer( 'ecmd_onboarding_save_preferences', 'nonce' );
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( array( 'message' => __( 'Permission denied.', 'events-calendar-modules-for-divi' ) ) );
			}

			$telemetry_raw = isset( $_POST['telemetry'] ) ? sanitize_text_field( wp_unslash( $_POST['telemetry'] ) ) : '0';
			$telemetry     = in_array( $telemetry_raw, array( '1', 'yes', 'true' ), true );
			$selections    = self::posted_selections();

			if ( ! class_exists( 'ECMD_Onboarding_Cpfm_Data', false ) ) {
				require_once ECMD_DIR . 'admin/ecmd-onboarding/includes/class-ecmd-onboarding-cpfm-data.php';
			}

			$sanitized = class_exists( 'ECMD_Onboarding_Cpfm_Data' )
				? ECMD_Onboarding_Cpfm_Data::sanitize_selections( $selections )
				: self::sanitize_selections( $selections );

			ECMD_Onboarding_Cpfm_Data::save_choice( $telemetry ? 'yes' : 'no' );
			ECMD_Onboarding_Cpfm_Data::save_preferences( $telemetry, $selections );

			update_option(
				self::PREFS_OPTION,
				array(
					'telemetry'  => $telemetry ? 'yes' : 'no',
					'selections' => $sanitized,
					'saved_at'   => current_time( 'mysql', true ),
				),
				false
			);

			if ( $telemetry ) {
				do_action( 'cpfm_after_opt_in_ecmd', 'cool_events' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
			}

			wp_send_json_success(
				array(
					'choice'    => $telemetry ? 'yes' : 'no',
					'telemetry' => ECMD_Onboarding_Cpfm_Data::get_telemetry_localize(),
				)
			);
		}

		/**
		 * AJAX: create a draft Divi page.
		 */
		public static function ajax_create_page() {
			check_ajax_referer( 'ecmd_onboarding_create_page', 'nonce' );
			if ( ! current_user_can( 'edit_posts' ) ) {
				wp_send_json_error( array( 'message' => __( 'Permission denied.', 'events-calendar-modules-for-divi' ) ) );
			}

			$selections = self::sanitize_selections( self::posted_selections() );
			$title      = self::draft_title( $selections );

			$result = ECMD_Onboarding_Draft_Page::create(
				array(
					'title'      => $title,
					'selections' => $selections,
				)
			);

			if ( is_wp_error( $result ) ) {
				wp_send_json_error( array( 'message' => $result->get_error_message() ) );
			}

			wp_send_json_success( $result );
		}

		/**
		 * @return array<string, mixed>
		 */
		private static function posted_selections() {
			// phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce verified by AJAX callers before this helper runs.
			if ( ! isset( $_POST['selections'] ) ) {
				return array();
			}
			$raw = map_deep( wp_unslash( $_POST['selections'] ), 'sanitize_text_field' );
			// phpcs:enable WordPress.Security.NonceVerification.Missing

			if ( is_string( $raw ) ) {
				$decoded = json_decode( $raw, true );
				return is_array( $decoded ) ? $decoded : array();
			}

			return is_array( $raw ) ? $raw : array();
		}

		/**
		 * @param array<string, mixed> $selections Raw selections.
		 * @return array<string, string>
		 */
		private static function sanitize_selections( $selections ) {
			$clean = array();
			foreach ( $selections as $key => $value ) {
				if ( is_scalar( $value ) ) {
					$clean[ sanitize_key( (string) $key ) ] = sanitize_text_field( (string) $value );
				}
			}

			return $clean;
		}

		/**
		 * @param array<string, string> $selections Sanitized selections.
		 * @return string
		 */
		private static function draft_title( $selections ) {
			$layout = ! empty( $selections['layout'] ) ? $selections['layout'] : 'list';

			return sprintf(
				/* translators: %s: layout name */
				__( 'Events — %s', 'events-calendar-modules-for-divi' ),
				ucwords( str_replace( '-', ' ', $layout ) )
			);
		}

		/**
		 * Whether Divi (parent or child) is the active theme.
		 *
		 * @return bool
		 */
		private static function is_divi_theme_active() {
			if ( class_exists( 'ECMD_Events_Calendar_Modules_For_Divi' ) ) {
				return ECMD_Events_Calendar_Modules_For_Divi::ecmd_is_theme_activate( 'Divi' );
			}

			$theme = wp_get_theme();

			return ( 'Divi' === $theme->name || ( $theme->parent_theme && false !== stripos( $theme->parent_theme, 'Divi' ) ) );
		}
	}
}
