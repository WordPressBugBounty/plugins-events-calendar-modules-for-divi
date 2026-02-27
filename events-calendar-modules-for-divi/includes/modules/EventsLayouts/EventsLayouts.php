<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Event List Module for Events Calendar
 *
 * @package ECMD_EventsLayouts
 */
/**
 * Class ECMD_EventsLayouts
 *
 * This class handles the event list module for the Events Calendar.
 */
class ECMD_EventsLayouts extends ECMD_Builder_Module {

	/**
	 * Module slug
	 */
	public $slug = 'ecmd_events_layouts';

	/**
	 * Full Visual Builder support.
	 */
	public $vb_support = 'on';


	/**
	 * Initialize the module settings.
	 *
	 * This method sets up the module's name, icon, and settings modal toggles.
	 * The settings modal toggles are used to organize and prioritize various
	 * configuration options within the module.
	 *
	 * @return void
	 */
	public function init() {
		$this->name                   = esc_html__( 'Events Layouts', 'events-calendar-modules-for-divi' );
		$this->icon_path              = $this->ecmd_icon_path( 'Event_List' );
		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_query' => array(
						'title'    => esc_html__( 'Event Query Settings', 'events-calendar-modules-for-divi' ),
						'priority' => 50,
					),

				),
			),
			'advanced' => array(
				'toggles' => array(
					'style_layout'   => array(
						'title' => esc_html__( 'Event Container', 'events-calendar-modules-for-divi' ),
					),
					'title_style'    => array(
						'title' => esc_html__( 'Event Title', 'events-calendar-modules-for-divi' ),
					),
					'date_highlight' => array(
						'title' => esc_html__( 'Event Highlight Date', 'events-calendar-modules-for-divi' ),
					),
					'date_style'     => array(
						'title' => esc_html__( 'Event Date', 'events-calendar-modules-for-divi' ),
					),
					'content_style'  => array(
						'title' => esc_html__( 'Event Description', 'events-calendar-modules-for-divi' ),
					),
					'venue_style'    => array(
						'title' => esc_html__( 'Event Venue', 'events-calendar-modules-for-divi' ),
					),
					'find_out_more'  => array(
						'title' => esc_html__( 'Event Find Out More', 'events-calendar-modules-for-divi' ),
					),
					'category_style'       => array(
						'title' => esc_html__( 'Event Category', 'events-calendar-modules-for-divi' ),
					),
					'cost_style'           => array(
						'title' => esc_html__('Event Cost', 'events-calendar-modules-for-divi'),
					),
				),
			),

		);
	}

	/**
	 * Retrieve the fields for the events calendar module in Divi.
	 *
	 * This method defines and returns an array of configuration fields for the module,
	 * which includes settings for layout, style, number of events, date formats,
	 * and other display options.
	 *
	 * @return array An array of configuration fields for the module.
	 */
	public function get_fields() {

		require ECMD_DIR . 'includes/modules/EventsLayouts/fields/ecmd_fields.php';
		return array_merge( $content, $feilds );
	}

	/**
	 * Configure the advanced fields for the module.
	 *
	 * @return array The advanced fields configuration.
	 */

	public function get_advanced_fields_config() {
				
		require ECMD_DIR . 'includes/modules/EventsLayouts/fields/ecmd_advance_fields.php';
		return $advanced_fields;
	}

	/**
	 * Retrieve and render events based on the specified arguments.
	 *
	 * @param array $args Array of arguments for querying events.
	 * @param array $conditional_tags Array of conditional tags (optional).
	 * @param array $current_page Array of current page data (optional).
	 *
	 * @return string HTML output of the events.
	 */

	static function ecmd_get_events( $args = array(), $conditional_tags = array(), $current_page = array() ) {
		$posts_number          = $args['posts_number'];
		$order                 = $args['order'];
		$featured_events       = ( 'true' === $args['featured_events'] ) ? true : '';
		$date_format           = $args['date_formats'];
		$layout_style           = $args['layout_style'];
		$events_more_info_text = $args['find_out_more'];
		$show_venue            = $args['show_venue'];
		$show_description      = $args['show_description'];
		$default_image         = $args['default_image'];
		$show_date_highlight   = $args['show_date_highlight'];
		$date_highlight_format = $args['date_highlight_format'];
		$show_event_image      = $args['show_event_image'];
		$show_find_out_more    = $args['show_find_out_more'];
		$show_category         = $args['show_category'];
		$show_cost             = $args['show_cost'];
		$description_count_type = $args['description_count_type'];
		$description_count     = $args['description_count'];
		$ecmd_args             = '';

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
		if ( isset( $args['time'] ) ) {
			if ( 'past' === $args['time'] ) {
				$meta_date_compare = '<';
			} elseif ( 'all' === $args['time'] ) {
				$meta_date_compare = '';
			}
		}
		// Initialize and set meta query attributes if applicable.
		$attribute = array();
		if ( '' !== $meta_date_compare ) {
			$meta_date_date         = current_time( 'Y-m-d H:i:s' );
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
		$events = tribe_get_events( $ecmd_args );
		$output = '';
		// Start output buffering.
		ob_start();
		$events_html = '';
		// Render events if available.
		if ( $events ) {
			$events_html .= '<div id="ecmd-list-wrapper" class="ecmd-event-list-wrapper ' . esc_attr($layout_style) . ' ">';
			require ECMD_DIR. 'includes/modules/EventsLayouts/templates/ecmd_event_layout.php';
	
			$events_html .= '</div>';

			$output .= $events_html;
		} else {
			echo '<p>'.esc_html__( 'No events found.', 'events-calendar-modules-for-divi' ).'</p>'; // Output message if no events found.
		}

		// Get buffered output and clean buffer.
		$output .= ob_get_clean();

		// Return output or fallback message.
		if ( ! $output ) {
			$output = self::get_no_results_template(); // Assuming you have a method to get a no results template.
		}

		return $output;
	}

	/**
	 * Render module output
	 *
	 * @since 1.0.0
	 *
	 * @param array  $attrs       List of unprocessed attributes.
	 * @param string $content     Content being processed.
	 * @param string $render_slug Slug of module that is used for rendering output.
	 *
	 * @return string module's rendered output.
	 */
	public function render( $attrs, $content, $render_slug ) {
		// Extract necessary attributes.
		if ( function_exists( 'tribe_get_events' ) ) {
			$posts_number          = isset( $this->props['posts_number'] ) ? intval( $this->props['posts_number'] ) : -1;
			$order                 = isset( $this->props['order'] ) ? sanitize_text_field( $this->props['order'] ) : 'ASC';
			$time                  = isset( $this->props['time'] ) ? sanitize_text_field( $this->props['time'] ) : 'future';
			$date_format           = isset( $this->props['date_formats'] ) ? sanitize_text_field( $this->props['date_formats'] ) : 'sedt';
			$layout_style           = isset( $this->props['layout_style'] ) ? sanitize_html_class( $this->props['layout_style'] ) : 'style1';
			$findmore_text         = isset( $this->props['find_out_more'] ) ? sanitize_text_field( $this->props['find_out_more'] ) : esc_html__( 'Find out more', 'events-calendar-modules-for-divi' );
			$featured_events       = ( isset( $this->props['featured_events'] ) && 'true' === sanitize_text_field( $this->props['featured_events'] ) ) ? 'true' : '';
			$show_venue            = isset( $this->props['show_venue'] ) ? sanitize_text_field( $this->props['show_venue'] ) : 'off';
			$show_description      = isset( $this->props['show_description'] ) ? sanitize_text_field( $this->props['show_description'] ) : 'off';
			$default_image         = isset( $this->props['default_image'] ) ? esc_url( $this->props['default_image'] ) : '';
			$show_date_highlight   = isset( $this->props['show_date_highlight'] ) ? sanitize_text_field( $this->props['show_date_highlight'] ) : 'on';
			$date_highlight_format = isset( $this->props['date_highlight_format'] ) ? sanitize_text_field( $this->props['date_highlight_format'] ) : 'DM';
			$show_event_image      = isset( $this->props['show_event_image'] ) ? sanitize_text_field( $this->props['show_event_image'] ) : 'off';
			$show_find_out_more    = isset( $this->props['show_find_out_more'] ) ? sanitize_text_field( $this->props['show_find_out_more'] ) : 'off';
			$show_category         = isset( $this->props['show_category'] ) ? sanitize_text_field( $this->props['show_category'] ) : 'off';
			$show_cost             = isset( $this->props['show_cost'] ) ? sanitize_text_field( $this->props['show_cost'] ) : 'off';
			$description_count_type = isset( $this->props['description_count_type'] ) ? sanitize_text_field( $this->props['description_count_type'] ) : 'short';
			$description_count     = isset( $this->props['description_count'] ) ? intval( $this->props['description_count'] ) : 20;
			$query_args            = array(
				'posts_number'          => $posts_number,
				'order'                 => $order,
				'featured_events'       => $featured_events,
				'time'                  => $time,
				'date_formats'          => $date_format,
				'layout_style'           => $layout_style,
				'find_out_more'         => $findmore_text,
				'show_venue'            => $show_venue,
				'show_description'      => $show_description,
				'default_image'         => $default_image,
				'show_date_highlight'   => $show_date_highlight,
				'date_highlight_format' => $date_highlight_format,
				'show_event_image'      => $show_event_image,
				'show_find_out_more'    => $show_find_out_more,
				'show_category'         => $show_category,
				'show_cost'             => $show_cost,
				'description_count_type' => $description_count_type,
				'description_count'     => $description_count,
			);
			$props                 = $this->props;
			ECMD_ModulesHelper::EventStaticCssLoader( $props, $render_slug );

			return sprintf(
				'<div id="ecmd-main-wrapper" Class="ecmd-module ecmd-event-main-wrapper">%s</div>',
				self::ecmd_get_events($query_args)
			);
		}
	}

}

new ECMD_EventsLayouts();
