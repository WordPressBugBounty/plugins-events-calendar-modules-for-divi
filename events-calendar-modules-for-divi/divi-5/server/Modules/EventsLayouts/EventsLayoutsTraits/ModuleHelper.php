<?php

declare(strict_types=1);

namespace ECMD\Modules\EventsLayouts\EventsLayoutsTraits;

if (! defined('ABSPATH')) {
    die('Direct access forbidden.');
}

class ModuleHelper
{
    /**
     * Retrieves the attribute value from the provided attributes array.
     *
     * @param array  $attrs   The attributes array.
     * @param string $key     The key to retrieve the value for.
     * @param mixed  $default The default value to return if the key does not exist.
     *
     * @return string|string[]|null The attribute value or the default value if not found.
     */
    public static function get_attr_value($attrs, $key, $default = null)
    {
        $value = ! empty( $attrs[ $key ]['desktop']['value'][ $key ] )
            ? $attrs[ $key ]['desktop']['value'][ $key ]
            : $default;

        if ( is_array( $value ) ) {
            // Divi 5 upload/image fields return an object (id, url, …), not a string URL.
            if ( 'default_image' === $key || isset( $value['url'] ) || isset( $value['id'] ) || isset( $value['src'] ) ) {
                return self::ecmd_resolve_image_url( $value, is_string( $default ) ? $default : '' );
            }

            return array_map( 'esc_html', $value );
        }

        return esc_html( $value );
    }

    public static function ecmd_resolve_image_url( $value, $default = '' ) {
        return self::ecmd_resolve_image_url_recursive( $value, $default, 0 );
    }
    /**
     * Normalize Divi 5 image upload values to a URL string.
     *
     * @param mixed  $value   Image attribute (string URL or Divi upload array).
     * @param string $default Fallback URL.
     * @return string
     */
    private static function ecmd_resolve_image_url_recursive( $value, $default = '', $depth = 0 ) {

        // Prevent excessive recursion.
        if ( $depth > 3 ) {
            return is_string( $default ) ? $default : '';
        }
    
        $fallback = is_string( $default ) ? $default : '';
    
        if ( empty( $value ) ) {
            return $fallback;
        }

        if ( is_string( $value ) ) {
            $url = esc_url_raw( $value );
            return $url ? $url : $fallback;
        }

        if ( ! is_array( $value ) ) {
            return $fallback;
        }

        if ( ! empty( $value['url'] ) && is_string( $value['url'] ) ) {
            $url = esc_url_raw( $value['url'] );
            return $url ? $url : $fallback;
        }

        if ( ! empty( $value['src'] ) && is_string( $value['src'] ) ) {
            $url = esc_url_raw( $value['src'] );
            return $url ? $url : $fallback;
        }

        if ( ! empty( $value['id'] ) ) {
            $attachment_url = wp_get_attachment_image_url( (int) $value['id'], 'full' );
            if ( $attachment_url ) {
                return esc_url_raw( $attachment_url );
            }
        }

        // Nested shape: [ 'default_image' => [ 'url' => … ] ].
        foreach ( $value as $nested ) {
            if ( is_array( $nested ) || is_string( $nested ) ) {
    
                $resolved = self::ecmd_resolve_image_url(
                    $nested,
                    '',
                    $depth + 1
                );
    
                if ( $resolved ) {
                    return $resolved;
                }
            }
        }

        return $fallback;
    }


    /**
     * Retrieves events data based on the provided arguments.
     *
     * @param array  $args            The arguments for retrieving events.
     *
     * @return array An array containing events and query arguments.
     */
    public static function ecmd_get_events_data($args)
    {
        $posts_number                          = $args['posts_per_page'];
        $order                                 = $args['order'];
        $featured_events                       = ('on' === $args['featured_events']) ? true : '';
        $ecmd_args                             = '';
        // Set default query arguments for retrieving events.
        $ecmd_args = array(
            'posts_per_page' => $posts_number,
            'post_status'    => 'publish',
            'order'          => $order,
            'featured'       => $featured_events,
            'has_password'   => false,
        );
        // Determine meta query based on time attribute.
        $meta_date_compare = '>=';
        if (isset($args['time'])) {
            if ('past' === $args['time']) {
                $meta_date_compare = '<';
            } elseif ('all' === $args['time']) {
                $meta_date_compare = '';
            }
        }
        // Initialize and set meta query attributes if applicable.
        $attribute = array();
        if ('' !== $meta_date_compare) {
            $meta_date_date         = current_time('Y-m-d H:i:s');
            $attribute['key']       = '_EventStartDate';
            $attribute['meta_date'] = array(
                array(
                    'key'     => '_EventEndDate',
                    'value'   => $meta_date_date,
                    'compare' => $meta_date_compare,
                    'type'    => 'DATETIME',
                ),
            );

            // phpcs:disable WordPress.DB.SlowDBQuery.slow_db_query_meta_key, WordPress.DB.SlowDBQuery.slow_db_query_meta_query
            $ecmd_args['meta_key']   = $attribute['key'];
            $ecmd_args['meta_query'] = $attribute['meta_date'];
        }
        // Retrieve events using Tribe Events Calendar function.
        $events = tribe_get_events($ecmd_args);
        return array('events' => $events);
    }

    /**
     * Generates the event schedule HTML based on the provided date format.
     *
     * @param int    $event_id    The ID of the event.
     * @param string $date_format The format of the date to display.
     * @param string $template    The template type to use for the schedule.
     *
     * @return string The HTML output for the event schedule.
     */

    public static function ecmd_event_schedule($event_id, $date_format, $template)
    {
        /*Date Format START*/
        $event_schedule = '';
        $ev_time        = self::ecmd_tribe_event_time($event_id, false);
        if ($date_format == 'DM') {
            $event_schedule = '<div class="ecmd-date-area ' . $template . '-schedule"  >
							<span class="ev-day">' . esc_html(tribe_get_start_date($event_id, false, 'd')) . '</span>
							<span class="ev-mo">' . esc_html(tribe_get_start_date($event_id, false, 'M')) . '</span>
							</div>';
        } elseif ($date_format == 'MD') {
            $event_schedule = '<div class="ecmd-date-area ' . $template . '-schedule"  >
							
							<span class="ev-mo">' . esc_html(tribe_get_start_date($event_id, false, 'M')) . '</span>
							<span class="ev-day">' . esc_html(tribe_get_start_date($event_id, false, 'd')) . '</span>
							</div>';
        } elseif ($date_format == 'FD') {
            $event_schedule = '<div class="ecmd-date-area ' . $template . '-schedule"  >
							
							<span class="ev-mo">' . esc_html(tribe_get_start_date($event_id, false, 'F')) . '</span>
							<span class="ev-day">' . esc_html(tribe_get_start_date($event_id, false, 'd')) . '</span>
							</div>';
        } elseif ($date_format == 'DF') {
            $event_schedule = '<div class="ecmd-date-area ' . $template . '-schedule"  >
							
							<span class="ev-day">' . esc_html(tribe_get_start_date($event_id, false, 'd')) . '</span>
							<span class="ev-mo">' . esc_html(tribe_get_start_date($event_id, false, 'F')) . '</span>
							</div>';
        } elseif ($date_format == 'FD,Y') {
            $event_schedule = '<div class="ecmd-date-area ' . $template . '-schedule"  >
							
							<span class="ev-mo">' . esc_html(tribe_get_start_date($event_id, false, 'F')) . '</span>
							<span class="ev-day">' . esc_html(tribe_get_start_date($event_id, false, 'd')) . ', </span>
							<span class="ev-yr">' . esc_html(tribe_get_start_date($event_id, false, 'Y')) . '</span>
							</div>';
        } elseif ($date_format == 'MDY') {
            $event_schedule = '<div class="ecmd-date-area ' . $template . '-schedule"  >
							
							<span class="ev-mo">' . esc_html(tribe_get_start_date($event_id, false, 'M')) . '</span>
							<span class="ev-day">' . esc_html(tribe_get_start_date($event_id, false, 'd')) . '</span>
							<span class="ev-yr">' . esc_html(tribe_get_start_date($event_id, false, 'Y')) . '</span>
							</div>';
        } elseif ($date_format == 'MD,Y') {
            $event_schedule = '<div class="ecmd-date-area ' . $template . '-schedule" >
							
							<span class="ev-mo">' . esc_html(tribe_get_start_date($event_id, false, 'M')) . '</span>
							<span class="ev-day">' . esc_html(tribe_get_start_date($event_id, false, 'd')) . ', </span>
							<span class="ev-yr">' . esc_html(tribe_get_start_date($event_id, false, 'Y')) . '</span>
							</div>';
        } elseif ($date_format == 'DML') {
            $event_schedule = '<div class="ecmd-date-area ' . $template . '-schedule" >
							
							<span class="ev-day">' . esc_html(tribe_get_start_date($event_id, false, 'd')) . '</span>
							<span class="ev-mo">' . esc_html(tribe_get_start_date($event_id, false, 'M')) . '</span>
							<span class="ev-weekday">' . esc_html(tribe_get_start_date($event_id, false, 'l')) . '</span>
							</div>';
        } elseif ($date_format == 'jMl') {
            $event_schedule = '<div class="ecmd-date-area ' . $template . '-schedule" >
							
							<span class="ev-day">' . esc_html(tribe_get_start_date($event_id, false, 'j')) . '</span>
							<span class="ev-mo">' . esc_html(tribe_get_start_date($event_id, false, 'M')) . '</span>
							<span class="ev-weekday">' . esc_html(tribe_get_start_date($event_id, false, 'l')) . '</span>
							</div>';
        } elseif ($date_format == 'full') {
            $event_schedule = '<div class="ecmd-date-area ' . esc_attr($template) . '-schedule" >
							
							<span class="ev-day">' . esc_html(tribe_get_start_date($event_id, false, 'd')) . '</span>
							<span class="ev-mo">' . esc_html(tribe_get_start_date($event_id, false, 'F')) . '</span>
							<span class="ev-yr">' . esc_html(tribe_get_start_date($event_id, false, 'Y')) . '</span>
							<span class="ev-time"><span class="ecmd-icon"> <i class="ecmd-icons-clock" aria-hidden="true"></i></span> ' . $ev_time . '</span>
							</div>';
        } elseif ($date_format == 'd.FY') {
            $event_schedule = '<div class="ecmd-date-area ' . esc_attr($template) . '-schedule" >
							
							<span class="ev-day">' . esc_html(tribe_get_start_date($event_id, false, 'd')) . '. </span>
							<span class="ev-mo">' . esc_html(tribe_get_start_date($event_id, false, 'F')) . '</span>
							<span class="ev-yr">' . esc_html(tribe_get_start_date($event_id, false, 'Y')) . '</span>
							</div>';
        } elseif ($date_format == 'd.F') {
            $event_schedule = '<div class="ecmd-date-area ' . esc_attr($template) . '-schedule" >
							
							<span class="ev-day">' . esc_html(tribe_get_start_date($event_id, false, 'd')) . '. </span>
							<span class="ev-mo">' . esc_html(tribe_get_start_date($event_id, false, 'F')) . '</span>
							</div>';
        } elseif ($date_format == 'd.Ml') {
            $event_schedule = '<div class="ecmd-date-area ' . esc_attr($template) . '-schedule" >
								
							<span class="ev-day">' . esc_html(tribe_get_start_date($event_id, false, 'd')) . '. </span>
							<span class="ev-mo">' . esc_html(tribe_get_start_date($event_id, false, 'M')) . '</span>
							<span class="ev-weekday">' . esc_html(tribe_get_start_date($event_id, false, 'l')) . '</span>
							</div>';
        } elseif ($date_format == 'ldF') {
            $event_schedule = '<div class="ecmd-date-area ' . esc_attr($template) . '-schedule" >
							
							<span class="ev-weekday">' . esc_html(tribe_get_start_date($event_id, false, 'l')) . '</span>
							<span class="ev-mo">' . esc_html(tribe_get_start_date($event_id, false, 'd')) . '</span>
							<span class="ev-yr">' . esc_html(tribe_get_start_date($event_id, false, 'F')) . '</span>
							</div>';
        } elseif ($date_format == 'Mdl') {
            $event_schedule = '<div class="ecmd-date-area ' . esc_attr($template) . '-schedule" >
							
							<span class="ev-day">' . esc_html(tribe_get_start_date($event_id, false, 'M')) . '</span>
							<span class="ev-mo">' . esc_html(tribe_get_start_date($event_id, false, 'd')) . '</span>
							<span class="ev-weekday">' . esc_html(tribe_get_start_date($event_id, false, 'l')) . '</span>
							</div>';
        } elseif ($date_format == 'dFY') {
            $event_schedule = '<div class="ecmd-date-area ' . esc_attr($template) . '-schedule" >
							
							<span class="ev-day">' . esc_html(tribe_get_start_date($event_id, false, 'd')) . '</span>
							<span class="ev-mo">' . esc_html(tribe_get_start_date($event_id, false, 'F')) . '</span>
							<span class="ev-yr">' . esc_html(tribe_get_start_date($event_id, false, 'Y')) . '</span>
							</div>';
        } elseif ($date_format == 'sed') {
            $time           = false;
            $event_schedule = self::ecmd_startend_date_html($event_id, $template, $time, $ev_time);
        } elseif ($date_format == 'sedt') {
            $time           = true;
            $event_schedule = self::ecmd_startend_date_html($event_id, $template, $time, $ev_time);
        } elseif ($date_format == 'time') {
            $event_schedule = '<div class="ecmd-date-area ' . esc_attr($template) . '-schedule" >
							<span class="ev-time"><span class="ecmd-icon"> <i class="ecmd-icons-clock" aria-hidden="true"></i></span> ' . $ev_time . '</span>
							</div>';
        } elseif ($date_format == 'D.j.F') {
            $event_schedule = '<div class="ecmd-date-area ' . $template . '-schedule"  >
						<span class="ev-weekday">' . esc_html(tribe_get_start_date($event_id, false, 'D')) . '.,</span>
						<span class="ev-day">' . esc_html(tribe_get_start_date($event_id, false, 'j')) . '.</span>
						<span class="ev-mo">' . esc_html(tribe_get_start_date($event_id, false, 'F')) . '</span>
						</div>';
        } elseif ($date_format == 'custom') {
            $event_schedule = '<span class="ecmd-custom-schedule">' . tribe_events_event_schedule_details($event_id) . '</span>';
        } else {
            $event_schedule = '<div class="ecmd-date-area ' . esc_attr($template) . '-schedule" >
								<span class="ev-day">' . esc_html(tribe_get_start_date($event_id, false, 'd')) . '</span>
								<span class="ev-mo">' . esc_html(tribe_get_start_date($event_id, false, 'F')) . '</span>
								<span class="ev-yr">' . esc_html(tribe_get_start_date($event_id, false, 'Y')) . '</span>
								</div>';
        }
        /*Date Format END*/
        return $event_schedule;
    }


    /**
     * Generates HTML for start and end date of the event.
     *
     * @param int $event_id The ID of the event.
     *
     * @return string HTML output for the start and end date of the event.
     */

    public static function ecmd_startend_date_html($event_id, $template, $time, $ev_time)
    {
        $event_schedule = '';
        if (tribe_get_start_date($event_id, false, 'Y') === tribe_get_end_date($event_id, false, 'Y')) {
            if (tribe_get_start_date($event_id, false, 'F') === tribe_get_end_date($event_id, false, 'F')) {
                if (tribe_get_start_date($event_id, false, 'd') === tribe_get_end_date($event_id, false, 'd')) {
                    $event_schedule = '	<div class="ecmd-date-area ' . esc_attr($template) . '-schedule" >
					<span class="ev-day">' . esc_html(tribe_get_start_date($event_id, false, 'd')) . '</span>
					<span class="ev-mo">' . esc_html(tribe_get_start_date($event_id, false, 'M')) . '</span>
					<span class="ev-yr">' . esc_html(tribe_get_start_date($event_id, false, 'Y')) . '</span>';
                    if ($time) {
                        $event_schedule .= '<span class="ev-time"><span class="ecmd-icon"> <i class="ecmd-icons-clock" aria-hidden="true"></i></span> ' . $ev_time . '</span>';
                    }
                    $event_schedule .= '</div>';
                } else {
                    $event_schedule = '	<div class="ecmd-date-area ' . esc_attr($template) . '-schedule" >
					<span class="ev-day">' . esc_html(tribe_get_start_date($event_id, false, 'd')) . ' - ' . esc_html(tribe_get_end_date($event_id, false, 'd')) . ' </span>
					<span class="ev-mo">' . esc_html(tribe_get_start_date($event_id, false, 'M')) . '</span>
					<span class="ev-yr">' . esc_html(tribe_get_start_date($event_id, false, 'Y')) . '</span>';
                    if ($time) {
                        $event_schedule .= '<span class="ev-time"> <span class="ecmd-icon"><i class="ecmd-icons-clock" aria-hidden="true"></i></span> ' . $ev_time . '</span>';
                    }
                    $event_schedule .= '</div>';
                }
            } else {
                $event_schedule = '	<div class="ecmd-date-area ' . esc_attr($template) . '-schedule" >
				<span class="ecmd-sed-main">
				<span class="ecmd-sed-startdate">
				<span class="ev-day">' . esc_html(tribe_get_start_date($event_id, false, 'd')) . '</span>
				<span class="ev-mo">' . esc_html(tribe_get_start_date($event_id, false, 'M')) . ' </span>
				</span>
				<span>-</span>
				<span class="ecmd-sed-enddate">
				<span class="ev-day">' . esc_html(tribe_get_end_date($event_id, false, 'd')) . '</span>
				<span class="ev-mo">' . esc_html(tribe_get_end_date($event_id, false, 'M')) . '</span>
				</span>
				</span>
				<span class="ev-yr">' . esc_html(tribe_get_start_date($event_id, false, 'Y')) . '</span>';
                if ($time) {
                    $event_schedule .= '<span class="ev-time"><span class="ecmd-icon"> <i class="ecmd-icons-clock" aria-hidden="true"></i></span> ' . $ev_time . '</span>';
                }
                $event_schedule .= '</div>';
            }
        } else {
            $event_schedule = '	<div class="ecmd-date-area  ecmd-startend-date ' . esc_attr($template) . '-schedule" >
			<span class="ecmd-start-date">
			<span class="ev-day">' . esc_html(tribe_get_start_date($event_id, false, 'd')) . '</span>
			<span class="ev-mo">' . esc_html(tribe_get_start_date($event_id, false, 'M')) . ' </span>
			<span class="ev-yr">' . esc_html(tribe_get_start_date($event_id, false, 'Y')) . ' </span>
			</span>
			<span class="ecmd-sed-seperator">-</span>
			<span class="ecmd-end-date">
			<span class="ev-day">' . esc_html(tribe_get_end_date($event_id, false, 'd')) . '</span>
			<span class="ev-mo">' . esc_html(tribe_get_end_date($event_id, false, 'M')) . '</span>
			<span class="ev-yr">' . esc_html(tribe_get_end_date($event_id, false, 'Y')) . '</span>
			</span>';
            if ($time) {
                $event_schedule .= '<span class="ev-time"><span class="ecmd-icon"> <i class="ecmd-icons-clock" aria-hidden="true"></i></span> ' . $ev_time . '</span>';
            }
            $event_schedule .= '</div>';
        }
        return $event_schedule;
    }

    /**
     * Retrieves the formatted time of the event.
     *
     * @param int  $event_id    The ID of the event.
     * @param bool $return_time Whether to return time only or not.
     *
     * @return string The formatted time of the event.
     */
    public static function ecmd_tribe_event_time($post_id, $display = true)
    {
        $event = $post_id;

        if (tribe_event_is_all_day($event)) { // all day event
            if ($display) {
                printf(esc_html__('All day', 'events-calendar-modules-for-divi'));
            } else {
                return sprintf(esc_html__('All day', 'events-calendar-modules-for-divi'));
            }
        } else {
            $time_format = get_option('time_format');
            $start_date  = tribe_get_start_date($event, false, $time_format);
            $end_date    = tribe_get_end_date($event, false, $time_format);
            if ($start_date !== $end_date) {
                if ($display) {
                    // Translators: %1$s is the start date, %2$s is the end date.
                    printf(esc_html__('%1$s - %2$s', 'events-calendar-modules-for-divi'), esc_html($start_date), esc_html($end_date));
                } else {
                    // Translators: %1$s is the start date, %2$s is the end date.
                    return sprintf(esc_html__('%1$s - %2$s', 'events-calendar-modules-for-divi'), esc_html($start_date), esc_html($end_date));
                }
            } else {
                if ($display) {
                    // Translators: %s is the start date.
                    printf(esc_html($start_date));
                } else {
                    // Translators: %s is the start date.
                    return sprintf(esc_html($start_date));
                }
            }
        }
    }

    /**
     * Retrieves the event image or returns a default image if none is found.
     *
     * @param int $event_id The ID of the event.
     *
     * @return string The URL of the event image or a default image.
     */

    public static function ecmd_get_event_image($event_id, $size, $default_image = null)
    {
        $default_url = self::ecmd_resolve_image_url(
            $default_image,
            ECMD_URL . 'assets/images/event-template-bg.png'
        );

        $feat_img_url = wp_get_attachment_image_src( get_post_thumbnail_id( $event_id ), $size );
        if ( ! empty( $feat_img_url ) && ! empty( $feat_img_url[0] ) ) {
            return esc_url( $feat_img_url[0] );
        }

        return esc_url( $default_url );
    }


    /**
     * Displays event categories (currently a placeholder).
     *
     * @param int $event_id The ID of the event.
     *
     * @return string HTML output for the event categories.
     */

    public static function ecmd_display_category($event_id)
    {
        // Placeholder function to display categories

        $ecmd_cate = '';
        $ecmd_cate = get_the_term_list($event_id, 'tribe_events_cat', '<ul class="tribe_events_cat"><li>', '</li><li>', '</li></ul>');
        return $ecmd_cate;
    }

    public static function ecmd_get_events_template($args, $events)
    {
        $layout_style = isset($args['layout_style']) ? $args['layout_style'] : 'style1';
        $date_format = isset($args['date_formats']) ? $args['date_formats'] : 'sedt';
        $events_more_info_text = isset($args['find_out_more_text']) ? $args['find_out_more_text'] : 'Find Out More';
        $show_venue = isset($args['show_venue']) ? $args['show_venue'] : 'on';
        $show_description = isset($args['show_description']) ? $args['show_description'] : 'off';
        $default_image = isset( $args['default_image'] ) ? $args['default_image'] : ECMD_URL . 'assets/images/event-template-bg.png';
        $show_date_highlight = isset($args['show_date_highlight']) ? $args['show_date_highlight'] : 'off';
        $date_highlight_format = isset($args['date_highlight_format']) ? $args['date_highlight_format'] : 'DM';
        $show_event_image = isset($args['show_event_image']) ? $args['show_event_image'] : 'on';
        $show_find_out_more = isset($args['show_find_out_more']) ? $args['show_find_out_more'] : 'on';
        $show_category = isset($args['show_category']) ? $args['show_category'] : 'on';
        $show_cost = isset($args['show_cost']) ? $args['show_cost'] : 'on';
        $description_count_type = isset($args['description_count_type']) ? $args['description_count_type'] : 'short';
        $description_count = isset($args['description_count']) ? $args['description_count'] : 20;
        $category_text_color = isset($args['unknownAttributes']['category_text_color']) ? $args['unknownAttributes']['category_text_color'] : '';
        $cost_text_color = isset($args['unknownAttributes']['cost_text_color']) ? $args['unknownAttributes']['cost_text_color'] : '';
        $cost_font_size = isset($args['unknownAttributes']['cost_font_size']) ? $args['unknownAttributes']['cost_font_size'] : '';
        $find_more_border_color = isset($args['unknownAttributes']['find_more_border_color']) ? $args['unknownAttributes']['find_more_border_color'] : '';
        $find_more_border_width = isset($args['unknownAttributes']['find_more_border_width']) ? $args['unknownAttributes']['find_more_border_width'] : '';
        $find_more_border_style = isset($args['unknownAttributes']['find_more_border_style']) ? $args['unknownAttributes']['find_more_border_style'] : '';
        $find_more_border_radius = isset($args['unknownAttributes']['find_more_border_radius']) ? $args['unknownAttributes']['find_more_border_radius'] : '';

        $events_html = '';
        foreach ($events as $event) {
            $event_id = $event->ID;
            $ev_time  = self::ecmd_tribe_event_time($event_id, false);
            $event_category = self::ecmd_display_category($event_id);
            $event_schedule = self::ecmd_event_schedule($event_id, $date_format, 'list');
            if ($show_date_highlight === 'on') {
                $event_scheudle_highlight = self::ecmd_event_schedule($event_id, $date_highlight_format, 'list');
            }
            if ($show_event_image === 'on') {
                $size = $layout_style === 'style1' ? 'thumbnail' : 'large';
                $ev_post_img = self::ecmd_get_event_image($event_id, $size, $default_image);
            }
            $event_title = '<a class="ecmd-event-url" href="' . esc_url(tribe_get_event_link($event_id)) . '" rel="bookmark">' . get_the_title($event_id) . '</a>';

            /*** Event venue details */
            $venue_details_html        = '';
            $venue_details_html_style1 = '';
            $venue_details             = tribe_get_venue_details($event_id);
            /*** Setup an array of venue details for use later in the template */
            
            if (tribe_has_venue($event_id)) :
                if ($layout_style === 'style1') {
                    $venue_details_html_style1 .= '<div class="ecmd-list-venue">';
                    if (isset($venue_details['linked_name'])) {
                        $venue_details_html_style1 .= '<span class="ecmd-icons"><i class="ecmd-icons-location" aria-hidden="true"></i></span>';
                        $venue_details_html_style1 .= '<span class="ecmd-venue-name">
                                ' . wp_kses_post($venue_details['linked_name']) . '</span>
                                ';
                        if (tribe_has_venue($event_id) && !empty(tribe_get_venue($event_id))) {
                            if (tribe_get_map_link($event_id)) {
                                $venue_details_html_style1 .= '<span class="ecmd-google">' . wp_kses_post(tribe_get_map_link_html($event_id)) . '</span>';
                            }
                        }
                    }
                    $venue_details_html_style1 .= '</div>';
                } else {
                    if (!empty(tribe_get_venue($event_id))) {
                        $venue_details_html .= '<div class="ecmd-list-venue">';
                        if (! empty($venue_details['address']) && isset($venue_details['linked_name'])) {
                            $venue_details_html .= '<span class="ecmd-icons"><i class="ecmd-icons-location"></i></span>';
                        }
                        $venue_details_html .= '<!-- Event Venue Info -->
                                    <span class="ecmd-venue-details ecmd-address">
                                    <div class="ecmd-venue-detail" >';
                        $escaped_details = array_map( 'wp_kses_post', $venue_details );
                        $venue_details_html .= implode( ', ', $escaped_details );
                        $venue_details_html .= '</div>';

                        if (tribe_get_map_link($event_id)) {
                            $venue_details_html .= '<span class="ecmd-google">' . wp_kses_post(tribe_get_map_link_html($event_id)) . '</span>';
                        }

                        $venue_details_html .= '</span>';
                        $venue_details_html .= '</div>';
                    }
                }

            endif;


            /*** Event description - content */
            $event_content  = '<!-- Event Content --><div class="ecmd-event-content">';
            $ecmd_content_data = $description_count_type === 'full' ?  wp_kses_post(get_post_field('post_excerpt', $event_id) ?: tribe_get_the_content('', true, $event_id)) : wp_trim_words((wp_kses_post(get_post_field('post_excerpt', $event_id) ?: tribe_get_the_content('', true, $event_id))), $description_count, '...');
            $event_content .= '<p>' . $ecmd_content_data . '</p>';
            $event_content .= '</div>';

            /** Event Cost */
            $event_cost = '<!-- Event Ticket Price Info -->
            <div class="ecmd-rate-area">
            <span class="ecmd-icon"><i class="ecmd-icons-ticket-2"></i></span>
            <span class="ecmd-rate">' . esc_html(tribe_get_cost($event_id, true)) . '</span>
            </div>';
            if ($layout_style === 'style1') {
                $events_html .= '<div id="event-' . esc_attr($event_id) . '" class="ecmd-list-post ' . esc_attr($layout_style) . '">';
                if ($show_date_highlight === 'on') {
                    $events_html .= '<div class="ecmd-date-highlight">';
                    $events_html .= $event_scheudle_highlight;
                    $events_html .= '</div>';
                }
                if ($show_event_image === 'on') {
                    $events_html .= '<div class="ecmd-image-div"><div class="ecmd-list-img"><a href="' . esc_url(tribe_get_event_link($event_id)) . '" class="ecmd-list-img-link"><img src="' . esc_url($ev_post_img) . '">';
                    $events_html .= '</a></div></div>';
                }
                $events_html .= '<div class="ecmd-event-details">';

                $events_html .= '<div class="ecmd-event-schedule">';
                if ($date_format != 'time') {
                    $events_html .= ' <i class="ecmd-icons-calendar"></i>';
                }
                $events_html .= '<span class="ecmd-minimal-list-time">' . wp_kses_post($event_schedule) . '</span></div>';
                $events_html .= '<div class="ecmd-title-container">	<h2 class="ecmd-event-title">' . wp_kses_post($event_title) . '</h2></div>';

                if ($show_venue === 'on') {
                    if (tribe_has_venue($event_id)) {
                        $events_html .= $venue_details_html_style1;
                    } else {
                        $events_html .= '';
                    }
                }
                $events_html .= '</div>';
                $events_html .= '</div>';
            } elseif ($layout_style === 'style2') {
                /**
                 * This file is used to generate the HTML for the event list layout.
                 *
                 * It includes the event image, date highlight, category, schedule, title,
                 * description, venue, "find out more" link, and cost.
                 */

                // checking the event type.
                $event_type = tribe('tec.featured_events')->is_featured($event_id) ? sanitize_text_field('ecmd-featured-event') : sanitize_text_field('ecmd-simple-event');

                // Start generating the main HTML for the event list.
                $events_html      .= '<div id="event-' . esc_attr($event_id) . '" class="ecmd-list-post ' . esc_attr($event_type) . ' ' . esc_attr($layout_style) . '">';
                $event_single_link = esc_url(tribe_get_event_link($event_id));
                $event_title_att   = get_the_title($event_id);
                $no_image_cls      = '';
                $valid_css_size    = '/^\d+(?:\.\d+)?(?:px|em|rem|%)$/i';

                $allowed_border_styles = array(
                    'none',
                    'solid',
                    'dashed',
                    'dotted',
                    'double',
                    'groove',
                    'ridge',
                    'inset',
                    'outset',
                );

                if(isset($args['unknownAttributes'])) {
                $events_html .= '<style>';
                if($category_text_color !== '') {
                $events_html .= '.ecmd-category a {
                    --ecmd-text-color: ' . sanitize_hex_color($category_text_color) . ';
                }';
                }
                if($cost_text_color !== '' || $cost_font_size !== '') {
                $events_html .= '.ecmd-rate-area {';
                if($cost_text_color !== '') {
                    $events_html .= '--ecmd-cost-color: ' . sanitize_hex_color($cost_text_color) . ';';
                }
                if($cost_font_size !== '' && preg_match($valid_css_size, $cost_font_size)) {
                    $events_html .= '--ecmd-cost-size: ' . esc_attr($cost_font_size) . ';';
                }
                $events_html .= '}';
                }
                if($find_more_border_color !== '' || $find_more_border_width !== '' || $find_more_border_style !== '' || $find_more_border_radius !== '') {
                $events_html .= '.ecmd-events-readmore {';
                    if($find_more_border_color !== '') {
                    $events_html .= '--ecmd-find-more-border-color: ' . sanitize_hex_color($find_more_border_color) . ';';
                    }
                    if($find_more_border_width !== '' && preg_match( $valid_css_size, $find_more_border_width ) ) {
                    $events_html .= '--ecmd-find-more-border-width: ' . esc_attr($find_more_border_width) . ';';
                    }
                    if($find_more_border_style !== '' && in_array( strtolower( $find_more_border_style ), $allowed_border_styles, true )) {
                    $events_html .= '--ecmd-find-more-border-style: ' . esc_attr( strtolower( $find_more_border_style ) ) . ';';
                    }
                    if($find_more_border_radius !== '' && preg_match( $valid_css_size, $find_more_border_radius ) ) {
                    $events_html .= '--ecmd-find-more-border-radius: ' . esc_attr($find_more_border_radius) . ';';
                    }
                }
                $events_html .= '}';
                $events_html .= '  </style>';
                }
                // Add event image if enabled.
                if ($show_event_image === 'on') {
                    $events_html .= '<div class="ecmd-list-post-left">';

                    $events_html .= '<div class="ecmd-image-div"><div class="ecmd-list-img"><a href="' . esc_url(tribe_get_event_link($event_id)) . '" class="ecmd-list-img-link"><img src="' . esc_url($ev_post_img) . '">';
                    $events_html .= '</a></div></div>';
                } else {
                    $events_html .= '<div class="ecmd-list-post-left no-image">';
                }

                // Add date highlight if enabled.
                if ($show_date_highlight === 'on') {
                    $events_html .= '<div class="ecmd-date-highlight">' . wp_kses_post($event_scheudle_highlight) . '</div>';
                }
                // Close the left post div.
                $events_html .= '</div><!-- left-post close -->';

                // Start the right post div.
                $events_html .= '<div class="ecmd-list-post-right">
					<div class="ecmd-category-time">';

                // Add category if enabled.
                if ($show_category === 'on') {
                    $events_html .= '<div class="ecmd-category">
	                        ' . wp_kses_post($event_category) . '
	                            </div>';
                }

                // Add event schedule.
                $events_html .= '<div class="ecmd-event-schedule">';
                if ($date_format != 'time') {
                    $events_html .= ' <i class="ecmd-icons-calendar"></i>';
                }
                $events_html .= wp_kses_post($event_schedule) . '
						</div>
						</div>
						<div class="ecmd-title-container">
							<h2 class="ecmd-event-title">' . wp_kses_post($event_title) . '</h2>
						</div>';

                // Add description if enabled.
                if ($show_description === 'on') {
                    $events_html .= $event_content;
                }

                // Add venue details if enabled and available.
                if ($show_venue === 'on') {
                    if (tribe_has_venue($event_id)) {
                        $events_html .= $venue_details_html;
                    } else {
                        $events_html .= '';
                    }
                }

                // Add "find out more" link and cost.
                $events_html .= '<div class="ecmd-readmore-cost">';
                if ($show_find_out_more === 'on') {
                    $events_html .= '	<a href="' . esc_url(tribe_get_event_link($event_id)) . '" class="ecmd-events-readmore" rel="bookmark"><span class="ecmd-event-readmore">' . esc_html($events_more_info_text) . '</span></a>';
                }
                if ($show_cost === 'on') {
                    if (tribe_get_cost($event_id)) {
                        $events_html .= $event_cost;
                    }
                }
                $events_html .= '</div>';
                $events_html .= '</div>';
                $events_html .= '</div>';
            }
        }
        return array('events_html' => $events_html,);
    }

    public static function ecmd_edit_events_data($args)
    {
        // Fetch events data
        $exclude_event = '';
        $events_data = self::ecmd_get_events_data($args, $exclude_event, 'main');
        $ecmd_events = $events_data['events'];
        if ($ecmd_events) {
            $formatted_events = array();

            foreach ($ecmd_events as $event) {
                $event_id = $event->ID;
                $venue_id            = tribe_get_venue_id($event_id);
                $time_format = get_option('time_format');
                $categories = get_the_terms($event_id, 'tribe_events_cat');
                // Add event data to the formatted array
                $formatted_events[] = array(
                    'id'          => $event_id,
                    'title'       => wp_kses_post(get_the_title($event_id)),
                    'description' => wp_kses_post(get_the_content(null, false, $event_id)),
                    'all_day'     => tribe_event_is_all_day($event_id),
                    'start_date_details' => array(
                        'year'    => tribe_get_start_date($event_id, false, 'Y'),
                        'month'   => tribe_get_start_date($event_id, false, 'm'),
                        'day'     => tribe_get_start_date($event_id, false, 'd'),
                        'hour'    => tribe_get_start_date($event_id, false, 'H'),
                        'minutes' => tribe_get_start_date($event_id, false, 'i'),
                        'start_time' => tribe_get_start_date($event, false, $time_format)
                    ),
                    'end_date_details' => array(
                        'year'    => tribe_get_end_date($event_id, false, 'Y'),
                        'month'   => tribe_get_end_date($event_id, false, 'm'),
                        'day'     => tribe_get_end_date($event_id, false, 'd'),
                        'hour'    => tribe_get_end_date($event_id, false, 'H'),
                        'minutes' => tribe_get_end_date($event_id, false, 'i'),
                        'end_time' => tribe_get_end_date($event, false, $time_format)
                    ),
                    'categories'  => $categories,
                    'cost'        => tribe_get_cost($event_id, true),
                    'venue'       => !empty(tribe_get_venue($event_id)) ? array(
                        'venue_details'       => tribe_get_venue_details($event_id),
                        'venue'               => tribe_get_venue($event_id),
                        'venue_url'           => get_permalink($venue_id),
                        'city'                => tribe_get_city($event_id),
                        'state'               => get_post_meta($venue_id, '_VenueState', true),
                        'country'             => tribe_get_country($event_id),
                        'address'             => get_post_meta($venue_id, '_VenueAddress', true)
                    ) : '',
                    'image'       => array('url' =>  get_the_post_thumbnail_url($event_id, 'full')),
                    'url'         => get_permalink($event_id)
                );
            }
            return $formatted_events;
        } else {
            return array();
        }
    }
}
