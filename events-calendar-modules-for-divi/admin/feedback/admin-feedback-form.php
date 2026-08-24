<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; }

class ECMD_feedback {

		private $plugin_url     = ECMD_URL;
		private $plugin_version = ECMD_V;
		private $plugin_name    = 'Events Calendar Modules For Divi';
		private $plugin_slug    = 'events-calendar-modules-for-divi';
		private $installation_date_option = 'ecmd-installDate';
		private $review_option = 'ecmd-Boxes-ratingDiv';
		private $review_link = 'https://wordpress.org/plugins/events-calendar-modules-for-divi/#reviews';


	/*
	|-----------------------------------------------------------------|
	|   Use this constructor to fire all actions and filters          |
	|-----------------------------------------------------------------|
	*/
	public function __construct() {
		// $this->plugin_url = plugin_dir_url( $this->plugin_url );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_feedback_scripts' ) );
		add_action( 'admin_head', array( $this, 'show_deactivate_feedback_popup' ) );
		add_action( 'wp_ajax_' . esc_html( $this->plugin_slug ) . '_submit_deactivation_response', array( $this, 'submit_deactivation_response' ) );
		add_action( 'admin_notices', array( $this, 'ecmd_admin_notice_for_review' ) );
		add_action( 'wp_ajax_' . esc_html( $this->plugin_slug ) . '_dismiss_notice', array( $this, 'ecmd_dismiss_review_notice' ) );
		add_action('admin_enqueue_scripts', array( $this, 'ecmd_enqueue_scripts' ) );

	}

	/*
	|-----------------------------------------------------------------|
	|   Enqueue all scripts and styles to required page only          |
	|-----------------------------------------------------------------|
	*/
	function enqueue_feedback_scripts() {
		$screen = get_current_screen();
		if ( isset( $screen ) && $screen->id == 'plugins' ) {
			wp_enqueue_script('ecmd-feedback-script', esc_url( $this->plugin_url ) . 'admin/feedback/js/admin-feedback.min.js', array( 'jquery' ), esc_html( $this->plugin_version ), true);
			wp_enqueue_style('ecmd-feedback-css', esc_url( $this->plugin_url ) . 'admin/feedback/css/admin-feedback.min.css', null, esc_html( $this->plugin_version ) );
		}
	}

	/**
	 * Whether the admin review notice should be shown (and its assets loaded).
	 *
	 * @return bool
	 */
	private function ecmd_should_show_review_notice() {
		if ( ! current_user_can( 'update_plugins' ) ) {
			return false;
		}

		$installation_date = get_option( esc_html( $this->installation_date_option ) );
		if ( empty( $installation_date ) ) {
			return false;
		}

		$already_rated = get_option( esc_html( $this->review_option ) );
		if ( false !== $already_rated && 'yes' === $already_rated ) {
			return false;
		}

		try {
			$install_date = new DateTime( $installation_date );
			$current_date = new DateTime( gmdate( 'Y-m-d h:i:s' ) );
			$diff_days    = $install_date->diff( $current_date )->days;
		} catch ( Exception $e ) {
			return false;
		}

		return isset( $diff_days ) && $diff_days >= 3;
	}

	/**
	 * Enqueue review-notice assets only when the notice can render.
	 *
	 * @param string $hook_suffix Current admin screen hook suffix.
	 */
	public function ecmd_enqueue_scripts( $hook_suffix ) {
		if ( ! $this->ecmd_should_show_review_notice() ) {
			return;
		}

		wp_enqueue_script( 'ecmd-admin-feedback-form', esc_url( $this->plugin_url ) . 'admin/feedback/js/admin-feedback-form.js', array( 'jquery' ), esc_html( $this->plugin_version ) , true );
		wp_localize_script( 'ecmd-admin-feedback-form', 'ecmd_admin_feedback_form', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'ajax_nonce' => wp_create_nonce( 'ecmd_admin_feedback_form' ),
		) );
		wp_enqueue_style( 'ecmd-admin-feedback-form', esc_url( $this->plugin_url ) . 'admin/feedback/css/admin-feedback-form.css', array(), esc_html( $this->plugin_version ) );
	}

	public function ecmd_dismiss_review_notice() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Forbidden', 403 );
		}
        check_ajax_referer( 'ecmd_dismiss_notice_nonce', 'security' );
        update_option( esc_html( $this->review_option ), 'yes' );
        wp_send_json_success();
    }

	public function ecmd_admin_notice_for_review(){
		if ( ! $this->ecmd_should_show_review_notice() ) {
			return;
		}

		echo wp_kses_post( $this->ecmd_create_notice_content() );
	}

	function ecmd_create_notice_content() {

		$html = '
		<div data-ajax-url="' . admin_url( 'admin-ajax.php' ) . '" data-nonce="' . wp_create_nonce( 'ecmd_dismiss_notice_nonce' ) . '" data-ajax-callback="' . esc_html( $this->plugin_slug ) . '_dismiss_notice'  . '" class="' . esc_html( $this->plugin_slug . '-review-notice-wrapper notice-info notice is-dismissible' ) . '">
			<div class="message_container">
				<p>Thanks for using <b>' . esc_html( $this->plugin_name ) . '</b> WordPress plugin. We hope it meets your expectations!<br/>Please give us a quick rating, it works as a boost for us to keep working on more <a href="https://coolplugins.net/?utm_source=ecmd_plugin&utm_medium=inside&utm_campaign=author_page&utm_content=review_notice" target="_blank"><strong>Cool Plugins</strong></a>!</p>
				<ul>
					<li><a href="' . esc_url( $this->review_link ) . '" class="rate-it-btn button button-primary" target="_blank" title="Submit A Review...">Rate Now! ★★★★★</a></li>
					<li><a href="javascript:void(0);" class="already-rated-btn button button-secondary ' . esc_html( $this->plugin_slug ) . '_dismiss_notice" title="Already Rated - Close This Notice!"> Already Reviewed</a></li>
					<li><a href="javascript:void(0);" class="already-rated-btn button button-secondary ' . esc_html( $this->plugin_slug ) . '_dismiss_notice" title="Not Interested - Close This Notice!"> Not Interested</a></li>
				</ul>
			</div>
		</div>
		';

		return $html;
	}
	/*
	|-----------------------------------------------------------------|
	|   HTML for creating feedback popup form                         |
	|-----------------------------------------------------------------|
	*/
	public function show_deactivate_feedback_popup() {
		$screen = get_current_screen();
		if ( ! isset( $screen ) || $screen->id != 'plugins' ) {
			return;
		}
		$deactivate_reasons = array(
			'didnt_work_as_expected'         => array(
				'title'             => __( 'The plugin didn\'t work as expected.', 'events-calendar-modules-for-divi' ),
				'input_placeholder' => 'What did you expect?',
			),
			'found_a_better_plugin'          => array(
				'title'             => __( 'I found a better plugin.', 'events-calendar-modules-for-divi' ),
				'input_placeholder' => __( 'Please share which plugin.', 'events-calendar-modules-for-divi' ),
			),
			'couldnt_get_the_plugin_to_work' => array(
				'title'             => __( 'The plugin is not working.', 'events-calendar-modules-for-divi' ),
				'input_placeholder' => 'Please share your issue. So we can fix that for other users.',
			),
			'temporary_deactivation'         => array(
				'title'             => __( 'It\'s a temporary deactivation.', 'events-calendar-modules-for-divi' ),
				'input_placeholder' => '',
			),
			'other'                          => array(
				'title'             => __( 'Other reason.', 'events-calendar-modules-for-divi' ),
				'input_placeholder' => __( 'Please share the reason.', 'events-calendar-modules-for-divi' ),
			),
		);

		?>
		<div id="cool-plugins-feedback-<?php echo esc_attr( $this->plugin_slug ); ?>" class="hide-feedback-popup">
						
			<div class="cp-feedback-wrapper">

			<div class="cp-feedback-header">
				<div class="cp-feedback-title"><?php echo esc_html__( 'Quick Feedback', 'events-calendar-modules-for-divi' ); ?></div>
				<div class="cp-feedback-title-link">A plugin by <a href="https://coolplugins.net/?utm_source=<?php echo esc_attr( $this->plugin_slug ); ?>_plugin&utm_medium=inside&utm_campaign=coolplugins&utm_content=deactivation_feedback" target="_blank">CoolPlugins.net</a></div>
			</div>

			<div class="cp-feedback-loader">
				<img src="<?php echo esc_url( $this->plugin_url ); ?>admin/feedback/images/cool-plugins-preloader.gif">
			</div>

			<div class="cp-feedback-form-wrapper">
				<div class="cp-feedback-form-title"><?php echo esc_html__( 'If you have a moment, please share the reason for deactivating this plugin.', 'events-calendar-modules-for-divi' ); ?></div>
				<form class="cp-feedback-form" method="post">
					<?php
					wp_nonce_field( '_cool-plugins_deactivate_feedback_nonce' );
					?>
					<input type="hidden" name="action" value="cool-plugins_deactivate_feedback" />
					
					<?php foreach ( $deactivate_reasons as $reason_key => $reason ) : ?>
						<div class="cp-feedback-input-wrapper">
							<input id="cp-feedback-reason-<?php echo esc_attr( $reason_key ); ?>" class="cp-feedback-input" type="radio" name="reason_key" value="<?php echo esc_attr( $reason_key ); ?>" />
							<label for="cp-feedback-reason-<?php echo esc_attr( $reason_key ); ?>" class="cp-feedback-reason-label"><?php echo esc_html( $reason['title'] ); ?></label>
							<?php if ( ! empty( $reason['input_placeholder'] ) ) : ?>
								<textarea class="cp-feedback-text" type="textarea" name="reason_<?php echo esc_attr( $reason_key ); ?>" placeholder="<?php echo esc_attr( $reason['input_placeholder'] ); ?>"></textarea>
							<?php endif; ?>
							<?php if ( ! empty( $reason['alert'] ) ) : ?>
								<div class="cp-feedback-text"><?php echo esc_html( $reason['alert'] ); ?></div>
							<?php endif; ?>	
						</div>
					<?php endforeach; ?>
					
					<div class="cp-feedback-terms">
					<input class="cp-feedback-terms-input" id="cp-feedback-terms-input" type="checkbox"><label for="cp-feedback-terms-input"><?php echo esc_html__( 'I agree to share my feedback with Cool Plugins, including site URL and admin email, to enable them to address my inquiry.', 'events-calendar-modules-for-divi' ); ?></label>
					</div>

					<div class="cp-feedback-button-wrapper">
						<a class="cp-feedback-button cp-submit" id="cool-plugin-submitNdeactivate">Submit and Deactivate</a>
						<a class="cp-feedback-button cp-skip" id="cool-plugin-skipNdeactivate">Skip and Deactivate</a>
					</div>
				</form>
			</div>


		   </div>
		</div>
		<?php
	}

	//  store the activate plugin version in the database
	function cpfm_get_user_info() {
		global $wpdb;
	
		// Server and WP environment details
		$server_info = [
			// phpcs:disable WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.DB.DirectDatabaseQuery.DirectQuery, 	WordPress.DB.DirectDatabaseQuery.NoCaching
			'server_software'        => isset($_SERVER['SERVER_SOFTWARE']) ? sanitize_text_field($_SERVER['SERVER_SOFTWARE']) : 'N/A',
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			'mysql_version'          => isset( $wpdb ) ? sanitize_text_field( $wpdb->db_version() ) : 'N/A',
			'php_version'            => sanitize_text_field(phpversion() ?: 'N/A'),
			'wp_version'             => sanitize_text_field(get_bloginfo('version') ?: 'N/A'),
			'wp_debug'               => (defined('WP_DEBUG') && WP_DEBUG) ? 'Enabled' : 'Disabled',
			'wp_memory_limit'        => sanitize_text_field(ini_get('memory_limit') ?: 'N/A'),
			'wp_max_upload_size'     => sanitize_text_field(ini_get('upload_max_filesize') ?: 'N/A'),
			'wp_permalink_structure' => sanitize_text_field(get_option('permalink_structure') ?: 'Default'),
			'wp_multisite'           => is_multisite() ? 'Enabled' : 'Disabled',
			'wp_language'            => sanitize_text_field(get_option('WPLANG') ?: get_locale()),
			'wp_prefix'              => isset($wpdb->prefix) ? sanitize_key($wpdb->prefix) : 'N/A',
		];
	
		// Theme details
		$theme = wp_get_theme();
		$theme_data = [
			'name'      => sanitize_text_field($theme->get('Name')),
			'version'   => sanitize_text_field($theme->get('Version')),
			'theme_uri' => esc_url($theme->get('ThemeURI')),
		];
	

		if (!function_exists('get_plugins')) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		if (!function_exists('get_plugin_data')) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
	

		$plugin_data = [];
		$active_plugins = get_option('active_plugins', []);
	
		foreach ( $active_plugins as $plugin_path ) {
			$plugin_info = get_plugin_data(WP_PLUGIN_DIR . '/' . sanitize_text_field($plugin_path));
			$author_url = ( isset( $plugin_info['AuthorURI'] ) && !empty( $plugin_info['AuthorURI'] ) ) ? esc_url( $plugin_info['AuthorURI'] ) : 'N/A';
			$plugin_url = ( isset( $plugin_info['PluginURI'] ) && !empty( $plugin_info['PluginURI'] ) ) ? esc_url( $plugin_info['PluginURI'] ) : 'N/A';
			$plugin_data[] = [
				'name'       => sanitize_text_field($plugin_info['Name']),
				'version'    => sanitize_text_field($plugin_info['Version']),
				'plugin_uri' => !empty($plugin_url) ? $plugin_url : $author_url,
			];
		}
	
		$extra_details = [
			'wp_theme'       => $theme_data,
			'active_plugins' => $plugin_data,
		];

		if ( ! class_exists( 'CPFM_Onboarding_Optin' ) ) {
			$optin = ECMD_DIR . 'admin/cpfm-feedback/class-cpfm-onboarding-optin.php';
			if ( file_exists( $optin ) ) {
				require_once $optin;
			}
		}

		if ( class_exists( 'CPFM_Onboarding_Optin' ) ) {
			$extra_details['user_preference_onboarding'] = CPFM_Onboarding_Optin::get_preferences_for_extra_details();
		}

		return [
			'server_info'   => $server_info,
			'extra_details' => $extra_details,
		];
	}


	function submit_deactivation_response() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			wp_send_json_error( 'Unauthorized' );
		}
		check_ajax_referer( '_cool-plugins_deactivate_feedback_nonce' );
			$reason             = isset( $_POST['reason'] ) ? sanitize_text_field( wp_unslash($_POST['reason']) ) : '';
			$deactivate_reasons = array(
				'didnt_work_as_expected'         => array(
					'title'             => __( 'The plugin didn\'t work as expected', 'events-calendar-modules-for-divi' ),
					'input_placeholder' => 'What did you expect?',
				),
				'found_a_better_plugin'          => array(
					'title'             => __( 'I found a better plugin', 'events-calendar-modules-for-divi' ),
					'input_placeholder' => __( 'Please share which plugin.', 'events-calendar-modules-for-divi' ),
				),
				'couldnt_get_the_plugin_to_work' => array(
					'title'             => __( 'The plugin is not working', 'events-calendar-modules-for-divi' ),
					'input_placeholder' => 'Please share your issue. So we can fix that for other users.',
				),
				'temporary_deactivation'         => array(
					'title'             => __( 'It\'s a temporary deactivation.', 'events-calendar-modules-for-divi' ),
					'input_placeholder' => '',
				),
				'other'                          => array(
					'title'             => __( 'Other', 'events-calendar-modules-for-divi' ),
					'input_placeholder' => __( 'Please share the reason.', 'events-calendar-modules-for-divi' ),
				),
			);

			$plugin_initial =  get_option( 'ecmd_initial_save_version' );
			$deactivation_reason = array_key_exists( $reason, $deactivate_reasons ) ? $reason : 'other';
			$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';
			$sanitized_message = '' === trim( $message ) ? 'N/A' : $message;
			$admin_email       = sanitize_email( get_option( 'admin_email' ) );
			$site_url          = esc_url_raw( site_url() );
			$install_date 		= get_option('ecmd_install_date');
			$unique_key     	= '54'; 
			$site_id        	= $site_url . '-' . $install_date . '-' . $unique_key;
			$feedback_url      = esc_url( ECMD_FEEDBACK_API . 'wp-json/coolplugins-feedback/v1/feedback' );
			$user_info         = $this->cpfm_get_user_info();
			$response          = wp_remote_post(
				$feedback_url,
				array(
					'timeout' => 30,
					'sslverify' => true,
					'body'    => array(
						'server_info'    => wp_json_encode($user_info['server_info']), 
						'extra_details'  => wp_json_encode($user_info['extra_details']),
						'plugin_initial' => isset($plugin_initial) ? sanitize_text_field($plugin_initial) : 'N/A',
						'plugin_version' => esc_html( $this->plugin_version ),
						'plugin_name'    => esc_html( $this->plugin_name ),
						'reason'         => sanitize_text_field($deactivation_reason),
						'review'         => sanitize_text_field($sanitized_message),
						'email'          => sanitize_email($admin_email),
						'domain'         => sanitize_text_field($site_url),
						'site_id'    	 => md5($site_id),
					),
				)
			);

			if ( is_wp_error( $response ) ) {
				wp_send_json_error( 'Feedback submission failed' );
			}

			wp_send_json_success( 'Feedback submitted successfully' );
	}
}
new ECMD_feedback();
