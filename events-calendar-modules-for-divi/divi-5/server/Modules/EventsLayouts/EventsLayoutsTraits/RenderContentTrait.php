<?php

namespace ECMD\Modules\EventsLayouts\EventsLayoutsTraits;

use ECMD\Modules\EventsLayouts\EventsLayoutsTraits\ModuleHelper;

if (! defined('ABSPATH')) {
	die('Direct access forbidden.');
}

trait RenderContentTrait
{
	static function ecmd_get_events($args)
	{
		$layout_style          				= $args['layout_style'] ?? 'style1';
		$ecmd_events_data 					= ModuleHelper::ecmd_get_events_data($args);
		$events 							= $ecmd_events_data['events'];
		$output 							= '';
		// Start output buffering.
		ob_start();
		$events_html = '';
		// Render events if available.
		if ($events) {
			$template_data = ModuleHelper::ecmd_get_events_template($args, $events);
			$events_html .= '<div id="ecmd-list-wrapper" class="ecmd-event-list-wrapper ' . esc_attr($layout_style) . ' ">';
			$events_html .= $template_data['events_html'];
			$events_html .= '</div>';
			$output .= $events_html;
		} else {
			echo '<p>No events found.</p>'; // Output message if no events found.
		}
		// Get buffered output and clean buffer.
		$output .= ob_get_clean();

		// Return output or fallback message.
		if (! $output) {
			$output = self::get_no_results_template(); // Assuming you have a method to get a no results template.
		}
		return $output;
	}


	public static function render_events($props)
	{

		return sprintf(
			'<div id="ecmd-main-wrapper" class="ecmd-main-wrapper" >%s</div>',
			self::ecmd_get_events($props)
		);
	}
}
