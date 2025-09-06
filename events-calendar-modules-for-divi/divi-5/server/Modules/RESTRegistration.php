<?php

/**
 * REST Registration.
 *
 * @package ECMD\Modules
 */

namespace ECMD\Modules;

if (! defined('ABSPATH')) {
    die('Direct access forbidden.');
}

use ET\Builder\Framework\Route\RESTRoute;
use ECMD\Modules\EventsLayouts\EventsLayoutsController;

/**
 * Class RESTRegistration
 *
 * Handles registration of REST API routes.
 */
class RESTRegistration
{
    /**
     * Register REST routes for modules.
     */
    public function register_routes()
    {
        $route = new RESTRoute('ecmd/v1');
        $route->prefix('/module-data')->get('/events-layouts', EventsLayoutsController::class);
    }
}
