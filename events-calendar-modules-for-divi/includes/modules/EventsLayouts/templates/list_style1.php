<?php
/**
 * Prevent direct access to the file
 */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}


$events_html .= '<div id="event-' . esc_attr( $event_id ) . '" class="ecmd-list-post ' . esc_attr( $layout_style ) . '">';
if ( $show_date_highlight === 'on' ) {
	$events_html .= '<div class="ecmd-date-highlight">';
	$events_html .= $event_scheudle_highlight;
	$events_html .= '</div>';
}
if ( $show_event_image === 'on' ) {
	$events_html .= '<div class="ecmd-image-div"><div class="ecmd-list-img"><a href="' . esc_url( tribe_get_event_link( $event_id ) ) . '" class="ecmd-list-img-link"><img src="' . esc_url( $ev_post_img ) . '">';
	$events_html .= '</a></div></div>';
}
$events_html .= '<div class="ecmd-event-details">';

$events_html .= '<div class="ecmd-event-schedule">';
if ( $date_format != 'time' ) {
	$events_html .= ' <i class="ecmd-icons-calendar"></i>';
}
$events_html .= '<span class="ecmd-minimal-list-time">' . wp_kses_post( $event_schedule ) . '</span></div>';
$events_html .= '<div class="ecmd-title-container">	<h2 class="ecmd-event-title">' . wp_kses_post( $event_title ) . '</h2></div>';

if ( $show_venue === 'on' ) {
	if ( tribe_has_venue( $event_id ) ) {
		$events_html .= $venue_details_html_style1;
	} else {
		$events_html .= '';
	}
}
$events_html .= '</div>';
$events_html .= '</div>';
