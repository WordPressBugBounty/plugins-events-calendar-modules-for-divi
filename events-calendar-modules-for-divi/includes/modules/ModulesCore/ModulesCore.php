<?php
/**
 * Prevent direct access to the file
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class ECMD_Builder_Module
 *
 * Custom builder module for Divi, including advanced field configuration and
 * methods for rendering properties and enqueuing scripts/styles.
 */

class ECMD_Builder_Module extends ET_Builder_Module {
	/**
	 * Get the path to an icon.
	 *
	 * @param string $icon Icon filename.
	 * @return string Path to the icon file.
	 */

	public function ecmd_icon_path( $icon ) {
		return sprintf( '%1$s/assets/images/%2$s.svg', ECMD_DIR, $icon );
	}

	 /**
	  * Configure advanced fields for the Divi builder.
	  *
	  * @return array Configuration for the advanced fields.
	  */

	public function get_advanced_fields_config() {
		return array(
			'text'           => false,
			'fonts'          => array(),
			'max_width'      => false,
			'margin_padding' => false,
			'border'         => false,
			'box_shadow'     => false,
			'filters'        => false,
			'transform'      => false,
			'animation'      => false,
			'background'     => false,
		);
	}


	/**
	 * Module credit information for the Divi module.
	 *
	 * @var array
	 */

	protected $module_credits = array(
		'module_uri' => 'https://eventscalendaraddons.com/divi/modern-carousel/?utm_source=ecmd_plugin&utm_medium=inside&utm_campaign=demo&utm_content=dashboard',
		'author'     => 'Cool Plugins',
		'author_uri' => 'https://coolplugins.net/?utm_source=ecmd_plugin&utm_medium=inside&utm_campaign=author_page&utm_content=dashboard',
	);
}
