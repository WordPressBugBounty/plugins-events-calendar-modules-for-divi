<?php

/**
 * Dynamic Module Controller.
 *
 * @package ECMD\Modules\EventsLayouts;
 */

namespace ECMD\Modules\EventsLayouts;

if (! defined('ABSPATH')) {
    die('Direct access forbidden.');
}

use ET\Builder\Framework\Controllers\RESTController;
use ECMD\Modules\EventsLayouts\EventsLayoutsTraits\ModuleHelper;
use WP_REST_Request;
use WP_REST_Response;

/**
 * Class EventsLayoutsController
 *
 * @package ECMD\Modules\EventsLayouts
 */
class EventsLayoutsController extends RESTController
{

    /**
     * Return data for the Dynamic Module.
     *
     * @param WP_REST_Request $request REST request object.
     *
     * @return WP_REST_Response|WP_Error
     */
    public static function index(WP_REST_Request $request): WP_REST_Response
    {

        $args = [
            'posts_per_page' => $request->get_param('posts_per_page'),
            'time' => $request->get_param('time'),
            'order' => $request->get_param('order'),
            'featured_events' => $request->get_param('featured_events'),
        ];
        $response = array(
            'events' => ModuleHelper::ecmd_edit_events_data($args),
        );
        return rest_ensure_response($response);
    }

    /**
     * Index action arguments.
     * Endpoint arguments as used in `register_rest_route()`.
     */
    public static function index_args(): array
    {
        return [
            'posts_per_page' => [
                'type'              => 'number',
                'required'          => true,
                'sanitize_callback' => function ($number) {
                    return intval($number);
                },
                'validate_callback' => 'esc_html',
            ],
            'order' => [
                'type'              => 'string',
                'required'          => true,
                'sanitize_callback' => 'sanitize_text_field',
                'validate_callback' => 'esc_html',
            ],
            'time' => [
                'type'              => 'string',
                'required'          => true,
                'sanitize_callback' => 'sanitize_text_field',
                'validate_callback' => 'esc_html',
            ],
            'featured_events' => [
                'type'              => 'string',
                'required'          => true,
                'sanitize_callback' => 'sanitize_text_field',
                'validate_callback' => 'esc_html',
            ]
        ];
    }

    /**
     * Index action permission.
     * Endpoint permission callback as used in `register_rest_route()`.
     */
    public static function index_permission(): bool
    {
        return current_user_can('edit_posts');
    }
}
