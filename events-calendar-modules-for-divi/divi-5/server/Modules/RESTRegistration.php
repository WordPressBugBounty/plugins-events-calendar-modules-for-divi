<?php

/**
 * REST Registration.
 *
 * @package ECMD\Modules
 */

namespace ECMD\Modules;

use ECMD\Modules\EventsLayouts\EventsLayoutsController;
use WP_REST_Server;

if (! defined('ABSPATH')) {
    die('Direct access forbidden.');
}

/**
 * Class RESTRegistration
 *
 * Handles registration of REST API routes via WordPress core API.
 */
class RESTRegistration
{
    /**
     * Register REST routes for modules.
     */
    public function register_routes(): void
    {
        register_rest_route(
            'ecmd/v1',
            '/module-data/events-layouts',
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [ EventsLayoutsController::class, 'index' ],
                'permission_callback' => [ EventsLayoutsController::class, 'index_permission' ],
                'args'                => EventsLayoutsController::index_args(),
            ]
        );
    }
}
