<?php
/**
 * Prevent direct access to the file
 */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}
/**
 * This file is used to generate the HTML for the event list layout.
 *
 * It includes the event image, date highlight, category, schedule, title,
 * description, venue, "find out more" link, and cost.
 */

// checking the event type.
$event_type = tribe( 'tec.featured_events' )->is_featured( $event_id ) ? sanitize_text_field( 'ecmd-featured-event' ) : sanitize_text_field( 'ecmd-simple-event' );

// Start generating the main HTML for the event list.
$events_html      .= '<div id="event-' . esc_attr( $event_id ) . '" class="ecmd-list-post ' . esc_attr( $event_type ) . ' ' . esc_attr( $layout_style ) . '">';
$event_single_link = esc_url( tribe_get_event_link( $event_id ) );
$event_title_att   = get_the_title( $event_id );
$no_image_cls      = '';

// Add event image if enabled.
if ( $show_event_image === 'on' ) {
	$events_html .= '<div class="ecmd-list-post-left">';

	$events_html .= '<div class="ecmd-image-div"><div class="ecmd-list-img"><a href="' . esc_url( tribe_get_event_link( $event_id ) ) . '" class="ecmd-list-img-link"><img src="' . esc_url( $ev_post_img ) . '">';
	$events_html .= '</a></div></div>';
} else {
	$events_html .= '<div class="ecmd-list-post-left no-image">';
}

// Add date highlight if enabled.
if ( $show_date_highlight === 'on' ) {
	$events_html .= '<div class="ecmd-date-highlight">' . wp_kses_post( $event_scheudle_highlight ) . '</div>';
}
// Close the left post div.
$events_html .= '</div><!-- left-post close -->';

// Start the right post div.
$events_html .= '<div class="ecmd-list-post-right">
					<div class="ecmd-category-time">';

// Add category if enabled.
if( $show_category === 'on' ) {
	$events_html .= '<div class="ecmd-category">
	' . wp_kses_post( $event_category ) . '
	</div>';
}
	
// Add event schedule.
$events_html .= '<div class="ecmd-event-schedule">';
				if($date_format != 'time'){
					$events_html .= ' <i class="ecmd-icons-calendar"></i>';
				}
				$events_html .= wp_kses_post( $event_schedule ) . '
						</div>
						</div>
						<div class="ecmd-title-container">
							<h2 class="ecmd-event-title">' . wp_kses_post( $event_title ) . '</h2>
						</div>';

// Add description if enabled.
if ( $show_description === 'on' ) {
	$events_html .= $event_content;
}

// Add venue details if enabled and available.
if ( $show_venue === 'on' ) {
	if ( tribe_has_venue( $event_id ) ) {
		$events_html .= $venue_details_html;
	} else {
		$events_html .= '';
	}
}

// Add "find out more" link and cost.
$events_html .= '<div class="ecmd-readmore-cost">';
				if ( $show_find_out_more === 'on' ) {
					$events_html .= '	<a href="' . esc_url( tribe_get_event_link( $event_id ) ) . '" class="ecmd-events-readmore" rel="bookmark"><span class="ecmd-event-readmore">' . esc_html( $events_more_info_text, 'events-calendar-modules-for-divi' ) . '</span></a>';
				}
				if ( $show_cost === 'on' ) {
					if ( tribe_get_cost( $event_id ) ) {
						$events_html .= $event_cost;
					}
				}
				$events_html .= '</div>';

// Close the right post div and the main container

$events_html .= '</div>';
$events_html .= '</div>';
