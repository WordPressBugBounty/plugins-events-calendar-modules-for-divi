<?php

/**
 * Prevent direct access to the file
 */
if (! defined('ABSPATH')) {
	exit;
}

/**
 * Class ECMD_ModulesHelper
 *
 * Helper class for handling various functionalities related to event modules.
 */

class ECMD_ModulesHelper extends ECMD_Builder_Module
{

	public function __construct()
	{
		// Constructor implementation (if needed)
	}

	/**
	 * Loads the CSS for the event module based on the provided properties.
	 *
	 * @param array  $props        An array of properties to set styles for.
	 * @param string $render_slug  The render slug used for the element.
	 */

	public static function EventStaticCssLoader($props, $render_slug)
	{
		// Retrieve properties
		$ecmd_layout_background_color        = $props['layout_background_color'];
		$ecmd_layout_background_color__hover  = $props['layout_background_color__hover'];
		$ecmd_list_venue_text_color          = $props['venue_text_color'];
		$ecmd_find_more_padding              = $props['find_more_padding'];
		$ecmd_find_more_backgroud_color      = $props['find_more_backgroud_color'];
		$ecmd_category_backgroud_color       = $props['category_backgroud_color'];
		$ecmd_category_text_color            = $props['category_text_color'];
		$ecmd_category_padding               = $props['category_padding'];
		$ecmd_date_highlight_backgroud_color = $props['date_highlight_backgroud_color'];
		$ecmd_date_highlight_text_color      = $props['date_highlight_text_color'];
		$ecmd_find_more_border_style         = $props['find_more_border_style'];
		$ecmd_find_more_border_color         = $props['find_more_border_color'];
		$ecmd_find_more_border_width         = $props['find_more_border_width'];
		$ecmd_find_more_border_radius        = $props['find_more_border_radius'];
		$ecmd_cost_text_color				 = $props['cost_text_color'];
		$ecmd_cost_font_size				 = $props['cost_font_size'];

		// Set styles based on properties
		if ($ecmd_layout_background_color != '') {
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%% .ecmd-list-post',
					'declaration' => sprintf('background-color: %1$s;', sanitize_hex_color($ecmd_layout_background_color)),
				),
			);
		}
		if ($ecmd_layout_background_color__hover != '') {
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%% .ecmd-list-post.style2:hover',
					'declaration' => sprintf('background-color: %1$s !important ;', sanitize_hex_color($ecmd_layout_background_color__hover)),
				),
			);
		}
		if ($ecmd_list_venue_text_color != '') {
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%% .ecmd-list-venue , %%order_class%% .ecmd-google a, %%order_class%% .ecmd-list-venue a',
					'declaration' => sprintf('color: %1$s !important ;', sanitize_hex_color($ecmd_list_venue_text_color)),
				),
			);
		}

		if ($ecmd_find_more_backgroud_color != '') {
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%% .ecmd-event-readmore',
					'declaration' => sprintf('--ecmd-readmore-bg-color: %1$s;', sanitize_hex_color($ecmd_find_more_backgroud_color)),
				),
			);
		}
		if ($ecmd_find_more_border_style != '') {
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%% .ecmd-event-readmore',
					'declaration' => sprintf('--ecmd-find-more-border-style: %1$s;', esc_attr($ecmd_find_more_border_style)),
				),
			);
		}
		if ($ecmd_find_more_border_color != '') {
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%% .ecmd-event-readmore',
					'declaration' => sprintf('--ecmd-find-more-border-color: %1$s;', sanitize_hex_color($ecmd_find_more_border_color)),
				),
			);
		}
		if ($ecmd_find_more_border_width != '') {
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%% .ecmd-event-readmore',
					'declaration' => sprintf('--ecmd-find-more-border-width: %1$s;', esc_attr($ecmd_find_more_border_width)),
				),
			);
		}
		if ($ecmd_find_more_border_radius != '') {
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%% .ecmd-event-readmore',
					'declaration' => sprintf('--ecmd-find-more-border-radius: %1$s;', esc_attr($ecmd_find_more_border_radius)),
				),
			);
		}
		if ($ecmd_find_more_padding != '') {
			$padding_size   = array_map( 'sanitize_text_field', explode( '|', $ecmd_find_more_padding ) );
	        $padding_top    = isset( $padding_size[0] ) ? esc_attr( $padding_size[0] ) : '0';
			$padding_right  = isset( $padding_size[1] ) ? esc_attr( $padding_size[1] ) : '0';
			$padding_bottom = isset( $padding_size[2] ) ? esc_attr( $padding_size[2] ) : '0';
			$padding_left   = isset( $padding_size[3] ) ? esc_attr( $padding_size[3] ) : '0';
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%% .ecmd-event-readmore',
					'declaration' => sprintf('padding-left: %1$s !important; 
												padding-right: %2$s !important; 
												padding-top: %3$s !important; 
												padding-bottom: %4$s !important;', $padding_left, $padding_right, $padding_top, $padding_bottom),
				),
			);
		}

		if ($ecmd_category_backgroud_color != '') {
			$bg_color = sanitize_hex_color( $ecmd_category_backgroud_color );
			if ( $bg_color ) {
				ET_Builder_Element::set_style(
					$render_slug,
					array(
						'selector'    => '%%order_class%% .ecmd-category ul li',
						'declaration' => sprintf( '--ecmd-bg-color: %1$s;', sanitize_hex_color( $bg_color ) ),
					),
				);
			}
		}
		if ($ecmd_category_text_color != '') {
			$text_color = sanitize_hex_color( $ecmd_category_text_color );
			if ( $text_color ) {
				ET_Builder_Element::set_style(
					$render_slug,
					array(
						'selector'    => '%%order_class%% .ecmd-category ul li',
						'declaration' => sprintf( '--ecmd-text-color: %1$s;', sanitize_hex_color( $text_color ) ),
					),
				);
			}
		}

		if ($ecmd_category_padding != '') {
			$padding_size   = array_map( 'sanitize_text_field', explode( '|', $ecmd_category_padding ) );
	        $padding_top    = isset( $padding_size[0] ) ? esc_attr( $padding_size[0] ) : '0';
	        $padding_right  = isset( $padding_size[1] ) ? esc_attr( $padding_size[1] ) : '0';
	        $padding_bottom = isset( $padding_size[2] ) ? esc_attr( $padding_size[2] ) : '0';
	        $padding_left   = isset( $padding_size[3] ) ? esc_attr( $padding_size[3] ) : '0';
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%% .ecmd-category ul li',
					'declaration' => sprintf('padding-left: %1$s !important; 
												padding-right: %2$s !important; 
												padding-top: %3$s !important; 
												padding-bottom: %4$s !important;', $padding_left, $padding_right, $padding_top, $padding_bottom),
				),
			);
		}

		if ($ecmd_date_highlight_backgroud_color != '') {
			$date_bg = sanitize_hex_color( $ecmd_date_highlight_backgroud_color );
			if ( $date_bg ) {
				ET_Builder_Element::set_style(
					$render_slug,
					array(
						'selector'    => '%%order_class%% .ecmd-date-highlight',
						'declaration' => sprintf( '--ecmd-bg-color: %1$s;', sanitize_hex_color( $date_bg ) ),
					),
				);
			}
		}
		if ($ecmd_date_highlight_text_color != '') {
			$date_color = sanitize_hex_color( $ecmd_date_highlight_text_color );
			if ( $date_color ) {
				ET_Builder_Element::set_style(
					$render_slug,
					array(
						'selector'    => '%%order_class%% .ecmd-date-highlight',
						'declaration' => sprintf( '--ecmd-text-color: %1$s;', sanitize_hex_color( $date_color ) ),
					),
				);
			}
		}
		if ($ecmd_cost_text_color !== '') {
			$cost_color = sanitize_hex_color( $ecmd_cost_text_color );
			if ( $cost_color ) {
				ET_Builder_Element::set_style(
					$render_slug,
					array(
						'selector'    => '%%order_class%% .ecmd-list-post.style2 .ecmd-readmore-cost .ecmd-rate-area',
						'declaration' => sprintf( '--ecmd-cost-color: %1$s;', sanitize_hex_color( $cost_color ) ),
					),
				);
			}
		}
		if ($ecmd_cost_font_size !== '') {
			$font_size = sanitize_text_field( $ecmd_cost_font_size );
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%% .ecmd-list-post.style2 .ecmd-readmore-cost .ecmd-rate-area',
					'declaration' => sprintf( '--ecmd-cost-size: %1$s;', esc_attr( $font_size ) ),
				),
			);
		}
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
			$event_schedule = '<div class="ecmd-date-area ' . esc_attr($template) . '-schedule"  >
							<span class="ev-day">' . esc_html(tribe_get_start_date($event_id, false, 'd')) . '</span>
							<span class="ev-mo">' . esc_html(tribe_get_start_date($event_id, false, 'M')) . '</span>
							</div>';
		} elseif ($date_format == 'MD') {
			$event_schedule = '<div class="ecmd-date-area ' . esc_attr($template) . '-schedule"  >
							
							<span class="ev-mo">' . esc_html(tribe_get_start_date($event_id, false, 'M')) . '</span>
							<span class="ev-day">' . esc_html(tribe_get_start_date($event_id, false, 'd')) . '</span>
							</div>';
		} elseif ($date_format == 'FD') {
			$event_schedule = '<div class="ecmd-date-area ' . esc_attr($template) . '-schedule"  >
							
							<span class="ev-mo">' . esc_html(tribe_get_start_date($event_id, false, 'F')) . '</span>
							<span class="ev-day">' . esc_html(tribe_get_start_date($event_id, false, 'd')) . '</span>
							</div>';
		} elseif ($date_format == 'DF') {
			$event_schedule = '<div class="ecmd-date-area ' . esc_attr($template) . '-schedule"  >
							
							<span class="ev-day">' . esc_html(tribe_get_start_date($event_id, false, 'd')) . '</span>
							<span class="ev-mo">' . esc_html(tribe_get_start_date($event_id, false, 'F')) . '</span>
							</div>';
		} elseif ($date_format == 'FD,Y') {
			$event_schedule = '<div class="ecmd-date-area ' . esc_attr($template) . '-schedule"  >
							
							<span class="ev-mo">' . esc_html(tribe_get_start_date($event_id, false, 'F')) . '</span>
							<span class="ev-day">' . esc_html(tribe_get_start_date($event_id, false, 'd')) . ', </span>
							<span class="ev-yr">' . esc_html(tribe_get_start_date($event_id, false, 'Y')) . '</span>
							</div>';
		} elseif ($date_format == 'MDY') {
			$event_schedule = '<div class="ecmd-date-area ' . esc_attr($template) . '-schedule"  >
							
							<span class="ev-mo">' . esc_html(tribe_get_start_date($event_id, false, 'M')) . '</span>
							<span class="ev-day">' . esc_html(tribe_get_start_date($event_id, false, 'd')) . '</span>
							<span class="ev-yr">' . esc_html(tribe_get_start_date($event_id, false, 'Y')) . '</span>
							</div>';
		} elseif ($date_format == 'MD,YT') {
			$event_schedule = '<div class="ecmd-date-area ' . esc_attr($template) . '-schedule" >
							
							<span class="ev-mo">' . esc_html(tribe_get_start_date($event_id, false, 'M')) . '</span>
							<span class="ev-day">' . esc_html(tribe_get_start_date($event_id, false, 'd')) . ', </span>
							<span class="ev-yr">' . esc_html(tribe_get_start_date($event_id, false, 'Y')) . '</span>
							<span class="ev-time"><span class="ecmd-icon"> <i class="ecmd-icons-clock" aria-hidden="true"></i></span> ' . $ev_time . '</span>
							</div>';
		} elseif ($date_format == 'DML') {
			$event_schedule = '<div class="ecmd-date-area ' . esc_attr($template) . '-schedule" >
							
							<span class="ev-day">' . esc_html(tribe_get_start_date($event_id, false, 'd')) . '</span>
							<span class="ev-mo">' . esc_html(tribe_get_start_date($event_id, false, 'M')) . '</span>
							<span class="ev-weekday">' . esc_html(tribe_get_start_date($event_id, false, 'l')) . '</span>
							</div>';
		} elseif ($date_format == 'jMl') {
			$event_schedule = '<div class="ecmd-date-area ' . esc_attr($template) . '-schedule" >
							
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
			$event_schedule = '<div class="ecmd-date-area ' . esc_attr($template) . '-schedule"  >
						<span class="ev-weekday">' . esc_html(tribe_get_start_date($event_id, false, 'D')) . '.,</span>
						<span class="ev-day">' . esc_html(tribe_get_start_date($event_id, false, 'j')) . '.</span>
						<span class="ev-mo">' . esc_html(tribe_get_start_date($event_id, false, 'F')) . '</span>
						</div>';
		} elseif ($date_format == 'custom') {
			$event_schedule = '<span class="ecmd-custom-schedule">' . tribe_events_event_schedule_details($event_id) . '</span>';
		} else {
			$event_schedule = '<div class="ecmd-date-area ' . esc_attr($template) . '-schedule" >
								<span class="ev-mo">' . esc_html(tribe_get_start_date($event_id, false, 'M')) . '</span>
								<span class="ev-day">' . esc_html(tribe_get_start_date($event_id, false, 'd')) . '</span>
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
		// $default_img  = ECT_PRO_PLUGIN_URL . 'assets/images/event-template-bg.png';
		$ev_post_img  = '';
		$feat_img_url = wp_get_attachment_image_src(get_post_thumbnail_id($event_id), $size);
		if (! empty($feat_img_url) && $feat_img_url[0] != false) {
			$ev_post_img = $feat_img_url[0];
		} else {
			$ev_post_img = $default_image;
		}
		return esc_url($ev_post_img);
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

	public static function remove_divi_shortcodes_from_description( $content ) {
        // Remove all Divi builder shortcodes
        return preg_replace('/\[\/?et_pb_[^\]]*\]/', '', $content);
    }
}
