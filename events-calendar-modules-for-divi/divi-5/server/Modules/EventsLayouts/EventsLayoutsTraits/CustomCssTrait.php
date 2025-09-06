<?php

namespace ECMD\Modules\EventsLayouts\EventsLayoutsTraits;

if ( ! defined( 'ABSPATH' ) ) {
  die( 'Direct access forbidden.' );
}

trait CustomCssTrait {
  public static function custom_css() {
    return \WP_Block_Type_Registry::get_instance()->get_registered( 'ecmd/events-layouts' )->customCssFields;
  }
}