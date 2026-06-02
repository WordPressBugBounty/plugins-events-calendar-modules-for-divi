<?php
if ( ! defined( 'ABSPATH' ) ) exit;
// Require PHP files.
require_once ECMD_DIR . 'divi-5/vendor/autoload.php';
require_once ECMD_DIR . 'divi-5/server/Modules/Modules.php';

class ECMD_AssetsLoader_divi_5
{
	public function __construct()
	{
		add_action('divi_visual_builder_assets_before_enqueue_scripts', [$this, 'ecmd_divi5_enqueue_visual_builder_assets']);
		add_action('admin_enqueue_scripts', [$this, 'ecmd_enqueue_scripts_divi5'], 5);
		add_action('wp_enqueue_scripts', [$this, 'ecmd_enqueue_scripts_divi5'], 5);
	}

	/**
	 * Enqueue Divi 5 Visual Builder Assets
	 *
	 * @since 1.0.0
	 */
	public function ecmd_divi5_enqueue_visual_builder_assets()
	{
		if (et_core_is_fb_enabled() && et_builder_d5_enabled()) {
			wp_enqueue_script('ecmd-divi5-visual-builder-script', ECMD_URL . 'divi-5/visual-builder/build/ecmd-events-calendar-modules-for-divi.js', array('react', 'jquery-core', 'divi-module-library', 'wp-hooks', 'divi-rest'), ECMD_V, true);
			wp_localize_script(
				'ecmd-divi5-visual-builder-script',
				'ecmd_data',
				array(
					'default_image_url' => esc_url( ECMD_URL . 'assets/images/event-template-bg.png' ),
				)
			);
		}
	}

	/**
	 * Enqueue Scripts Divi 5
	 *
	 * @since 1.0.0
	 */
	public function ecmd_enqueue_scripts_divi5()
	{
		wp_enqueue_style('ecmd-divi5-visual-builder-style', ECMD_URL . 'styles/style.min.css', array(), ECMD_V);
		$css_path = ECMD_URL . 'assets/css/';
		wp_register_style( 'ecmd-icons-css', $css_path . 'ecmdicons.min.css', array(), ECMD_V, 'all' ); 
		wp_enqueue_style( 'ecmd-icons-css' );
		
	}
}
