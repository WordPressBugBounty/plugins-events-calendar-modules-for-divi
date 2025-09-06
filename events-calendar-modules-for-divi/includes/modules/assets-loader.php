<?php
/**
 * Prevent direct access to the file
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ECMD_AssetsLoader {
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'ecmd_enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'ecmd_enqueue_scripts' ) ); // Load in Divi editor as well
	}

	public function ecmd_enqueue_scripts() {
		$css_path = ECMD_URL . 'assets/css/';
		wp_register_style( 'ecmd-icons-css', $css_path . 'ecmdicons.min.css', array(), ECMD_V, 'all' ); 
		wp_enqueue_style( 'ecmd-icons-css' );
	}
}

