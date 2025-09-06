<?php
foreach ( $events as $event ) {
    $event_id = $event->ID;
    $ev_time  = ECMD_ModulesHelper::ecmd_tribe_event_time( $event_id, false );
    if ( $show_category === 'on' ) {
        $event_category = ECMD_ModulesHelper::ecmd_display_category( $event_id );
    }
    $event_schedule = ECMD_ModulesHelper::ecmd_event_schedule( $event_id, $date_format, 'list' );
    if ( $show_date_highlight === 'on' ) {
        $event_scheudle_highlight = ECMD_ModulesHelper::ecmd_event_schedule( $event_id, $date_highlight_format, 'list' );
    }
    if ( $show_event_image === 'on' ) {
        $size = $layout_style === 'style1' ? 'thumbnail': 'large';
        $ev_post_img = ECMD_ModulesHelper::ecmd_get_event_image( $event_id, $size, $default_image );
    }
    $event_title = '<a class="ecmd-event-url" href="' . esc_url( tribe_get_event_link( $event_id ) ) . '" rel="bookmark">' . get_the_title( $event_id ) . '</a>';

    /*** Event venue details */
    $venue_details_html        = '';
    $venue_details_html_style1 = '';
    $venue_details             = tribe_get_venue_details( $event_id );
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
                $venue_details_html .= '<!-- Event Venue Info -->
                <span class="ecmd-venue-details ecmd-address">';
                $venue_details_html .= '<div class="ecmd-venue-detail" >';
                if (! empty($venue_details['address']) && isset($venue_details['linked_name'])) {
                    $venue_details_html .= '<span class="ecmd-icons"><i class="ecmd-icons-location"></i></span>';
                }
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
    $ecmd_excerpt = ECMD_ModulesHelper::remove_divi_shortcodes_from_description( get_post_field( 'post_excerpt', $event_id ));
    $ecmd_desc = ECMD_ModulesHelper::remove_divi_shortcodes_from_description( tribe_get_the_content( '', true, $event_id ));
    $ecmd_content_data = $description_count_type === 'full'?  wp_kses_post( $ecmd_excerpt ?: $ecmd_desc ) : wp_trim_words( (wp_kses_post( $ecmd_excerpt ?: $ecmd_desc )), $description_count, '...' );
    $event_content .= '<p>' . ECMD_ModulesHelper::remove_divi_shortcodes_from_description($ecmd_content_data) . '</p>';
    $event_content .= '</div>';

    /** Event Cost */
    $event_cost = '<!-- Event Ticket Price Info -->
    <div class="ecmd-rate-area">
    <span class="ecmd-icon"><i class="ecmd-icons-ticket"></i></span>
    <span class="ecmd-rate">' . esc_html( tribe_get_cost( $event_id, true ) ) . '</span>
    </div>';

    if ( $layout_style === 'style1' ) {
        require 'list_style1.php';
    } elseif ( $layout_style === 'style2' ) {
        require 'list_style2.php';
    }
}