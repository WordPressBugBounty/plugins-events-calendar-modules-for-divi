<?php

/**
 * All modules.
 *
 * @package ECMD\Modules;
 */

namespace ECMD\Modules;

if (! defined('ABSPATH')) {
  die('Direct access forbidden.');
}

use ECMD\Modules\EventsLayouts\EventsLayouts;

add_action(
  'init',
  function () {
    $restApi = new RESTRegistration();
    $restApi->register_routes();
  }
);

add_action(
  'divi_module_library_modules_dependency_tree',
  function ($dependency_tree) {
    $dependency_tree->add_dependency(new EventsLayouts());
  }
);
