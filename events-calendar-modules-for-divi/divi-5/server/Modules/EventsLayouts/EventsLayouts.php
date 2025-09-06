<?php

/**
 * EventsLayouts Module class.
 *
 * @package ECMD\Modules\EventsLayouts ;
 */

namespace ECMD\Modules\EventsLayouts;

if ( ! defined( 'ABSPATH' ) ) {
  die( 'Direct access forbidden.' );
}

use ET\Builder\Framework\DependencyManagement\Interfaces\DependencyInterface;
use ET\Builder\Packages\ModuleLibrary\ModuleRegistration;
use ECMD\Modules\EventsLayouts\EventsLayoutsTraits;

/**
 * Class EventsLayouts 
 *
 * @package ECMD\Modules\EventsLayouts 
 */
class EventsLayouts implements DependencyInterface {
  use EventsLayoutsTraits\RenderCallbackTrait;
  use EventsLayoutsTraits\RenderContentTrait;
  use EventsLayoutsTraits\ModuleClassnamesTrait;
  use EventsLayoutsTraits\ModuleStylesTrait;
  use EventsLayoutsTraits\ModuleScriptDataTrait;

  /**
     * Register module.
     * `DependencyInterface` interface ensures class method name `load()` is executed for initialization.
     */
  public function load() {
      $module_json_folder_path = dirname( __DIR__, 3 ) . '/visual-builder/src/modules/eventslayouts';
      // Register module.
    add_action(
      'init',
      function() use ( $module_json_folder_path ) {
        ModuleRegistration::register_module(
          $module_json_folder_path,
          [
            'render_callback' => [ $this, 'render_callback' ],
          ]
        );
      }
    );
  }
}