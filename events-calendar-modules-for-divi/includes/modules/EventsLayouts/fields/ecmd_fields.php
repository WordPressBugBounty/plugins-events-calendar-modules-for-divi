<?php
if (!defined('ABSPATH')) exit;
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
$option             = array( 'future', 'past', 'all' );
$order_option       = array( 'ASC', 'DESC' );
$event_style        = array(
    'style1' => 'Style 1',
    'style2' => 'Style 2',
);
$date_format_option = array(
    'dFY'   => 'dFY (01 January 2025)',
    'MDY'  => 'MdY (Jan 01 2025)',
    'FD,Y'  => 'Fd,Y (January 01, 2025)',
    'DM'    => 'dM (01 Jan)',
    'DF'    => 'dF (01 January)',
    'MD'    => 'Md (Jan 01)',
    'FD'    => 'Fd (January 01)',
    'jMl'   => 'jMl (1 Jan Monday)',
    'd.FY'  => 'd.FY (01. January 2025)',
    'd.F'   => 'd.F (01. January)',
    'ldF'   => 'ldF (Monday 01 January)',
    'd.Ml'  => 'd.Ml (01. Jan Monday)',
    'Mdl'   => 'Mdl (Jan 01 Monday)',
    'sed'   => 'SED (01 Jan - 02 Feb 2025 )',
    'sedt'  => 'SEDT (01 - 02 Jan 2025 8:00am-5:00pm )',
    'D.j.F' => 'D.,j.F (Wed., 15. Jan)',
    'time'  => 'Time (8:00am -5:00pm)',
);

$highlight_date_format = array(
    'MDY'  => 'MdY (Jan 01 2025)',
    'DM'    => 'dM (01 Jan)',
    'MD'    => 'Md (Jan 01)',
);

$layout_options = array(
    'list' => 'List Layout',
);

$descripton_count = array(
    'short' => 'Short',
    'full' => 'Full',
);

$border_style = array(
    'solid' => 'Solid',
    'dashed' => 'Dashed',
    'dotted' => 'Dotted',
    'double' => 'Double',
    'groove' => 'Groove',
    'ridge' => 'Ridge',
    'inset' => 'Inset',
    'outset' => 'Outset',
    'none' => 'None',
);
$content = array(
    'select_layouts'        => array(
        'label'            => esc_html__( 'Select Layout', 'events-calendar-modules-for-divi' ),
        'description'      => esc_html__( 'To select different layouts from the given list, change this setting to any style you want.', 'events-calendar-modules-for-divi' ),
        'type'             => 'select',
        'options'          => $layout_options,
        'default'          => 'list',
        'toggle_slug'      => 'main_query',
        'computed_affects' => array( '__events' ),
    ),

    'layout_style'           => array(
        'label'            => esc_html__( 'Select Style', 'events-calendar-modules-for-divi' ),
        'description'      => esc_html__( 'To showcase different styles, change this setting to any style you want.', 'events-calendar-modules-for-divi' ),
        'type'             => 'select',
        'options'          => $event_style,
        'default'          => 'style1',
        'toggle_slug'      => 'main_query',
        'computed_affects' => array( '__events' ),
        'affects'          => array(
            'find_out_more_font',
            'find_out_more_text_color',
            'find_out_more_line_height',
            'find_out_more_font_size',
            'find_out_more_all_caps',
            'find_out_more_letter_spacing',
            'find_out_more_text_shadow',
            'find_out_more_text_align',
            'find_out_more_text_shadow',

        ),
    ),
    'posts_number'          => array(
        'label'            => esc_html__( 'Number of Events', 'events-calendar-modules-for-divi' ),
        'description'      => esc_html__( 'Set The number of  events you want to display', 'events-calendar-modules-for-divi' ),
        'type'             => 'text',
        'option_category'  => 'configuration',
        'computed_affects' => array(
            '__events',
        ),
        'toggle_slug'      => 'main_query',
        'default'          => 6,
    ),

    'time'                  => array(
        'label'            => esc_html__( 'Event Time', 'events-calendar-modules-for-divi' ),
        'description'      => esc_html__( 'This setting will help you showcase future, past or both time events.', 'events-calendar-modules-for-divi' ),
        'type'             => 'select',
        'options'          => $option,
        'default'          => 'future',
        'toggle_slug'      => 'main_query',
        'computed_affects' => array( '__events' ),
    ),
    'date_formats'          => array(
        'label'            => esc_html__( 'Events Date Formats', 'events-calendar-modules-for-divi' ),
        'description'      => esc_html__( 'Choose a Date format from the list of given formats to display your date.', 'events-calendar-modules-for-divi' ),
        'type'             => 'select',
        'options'          => $date_format_option,
        'default'          => 'sedt',
        'toggle_slug'      => 'main_query',
        'computed_affects' => array( '__events' ),
    ),
    'featured_events'       => array(
        'label'            => esc_html__( 'Show Only Featured Events', 'events-calendar-modules-for-divi' ),
        'description'      => esc_html__( 'Enable this setting to show only featured events, by default the setting shows both fetaured and non-featured events', 'events-calendar-modules-for-divi' ),
        'type'             => 'select',
        'options'          => array(
            'true'  => esc_html__( 'Yes', 'events-calendar-modules-for-divi' ),
            'false' => esc_html__( 'No', 'events-calendar-modules-for-divi' ),
        ),
        'default'          => 'false',
        'toggle_slug'      => 'main_query',
        'computed_affects' => array( '__events' ),
    ),
    'order'                 => array(
        'label'            => esc_html__( 'Events Order', 'events-calendar-modules-for-divi' ),
        'description'      => esc_html__( 'This setting will help you to show Events in Ascending and Descending Order', 'events-calendar-modules-for-divi' ),
        'type'             => 'select',
        'options'          => $order_option,
        'default'          => 'ASC',
        'toggle_slug'      => 'main_query',
        'computed_affects' => array( '__events' ),
    ),
    'show_date_highlight'   => array(
        'label'            => esc_html__( ' Show Date Highlight', 'events-calendar-modules-for-divi' ),
        'description'      => esc_html__( 'To Show Highlight date', 'events-calendar-modules-for-divi' ),
        'type'             => 'yes_no_button',
        'option_category'  => 'basic_option',
        'toggle_slug'      => 'main_query',
        'options'          => array(
            'on'  => esc_html__( 'Yes', 'events-calendar-modules-for-divi' ),
            'off' => esc_html__( 'No', 'events-calendar-modules-for-divi' ),
        ),
        'default'          => 'on',
        'computed_affects' => array( '__events' ),
    ),
    'date_highlight_format' => array(
        'label'            => esc_html__( 'Select Highlight Date Format', 'events-calendar-modules-for-divi' ),
        'description'      => esc_html__( 'Choose a Date format from the list of given formats for Highlight Date', 'events-calendar-modules-for-divi' ),
        'type'             => 'select',
        'options'          => $highlight_date_format,
        'toggle_slug'      => 'main_query',
        'show_if'          => array(
            'show_date_highlight' => 'on',
        ),
        'default'		   => 'DM',
        'computed_affects' => array( '__events' ),
    ),
    'show_event_image'      => array(
        'label'           => esc_html__( 'Show Featured Image', 'events-calendar-modules-for-divi' ),
        'description'     => esc_html__( 'To add Event Featured Image', 'events-calendar-modules-for-divi' ),
        'type'            => 'yes_no_button',
        'option_category' => 'basic_option',
        'toggle_slug'     => 'main_query',
        'options'         => array(
            'on'  => esc_html__( 'Yes', 'events-calendar-modules-for-divi' ),
            'off' => esc_html__( 'No', 'events-calendar-modules-for-divi' ),
        ),
        'default'         => 'on',

    ),
    'default_image'         => array(
        'label'              => esc_html__( 'Default Image', 'events-calendar-modules-for-divi' ),
        'description'        => esc_html__( 'To add your default image to the events without image', 'events-calendar-modules-for-divi' ),
        'type'               => 'upload',
        'upload_button_text' => esc_attr__( 'Choose an Image', 'events-calendar-modules-for-divi' ),
        'update_text'        => esc_attr__( 'Set As Image', 'events-calendar-modules-for-divi' ),
        'toggle_slug'        => 'main_query',
        'default'            => ECMD_URL . '/assets/images/event-template-bg.png',
        'computed_affects'   => array( '__events' ),
        'show_if'            => array(
            'show_event_image' => 'on',
        ),
    ),
    'show_find_out_more'    => array(
        'label'            => esc_html__( ' Show Find Out More', 'events-calendar-modules-for-divi' ),
        'description'      => esc_html__( 'To Show Find Out More', 'events-calendar-modules-for-divi' ),
        'type'             => 'yes_no_button',
        'option_category'  => 'basic_option',
        'toggle_slug'      => 'main_query',
        'options'          => array(
            'on'  => esc_html__( 'Yes', 'events-calendar-modules-for-divi' ),
            'off' => esc_html__( 'No', 'events-calendar-modules-for-divi' ),
        ),
        'default'          => 'on',	
        'computed_affects' => array( '__events' ),
        'show_if'          => array(
            'layout_style' => 'style2',
        ),
    ),
    'find_out_more'         => array(
        'label'            => esc_html__( 'Find Out More Text', 'events-calendar-modules-for-divi' ),
        'description'      => esc_html__( 'This setting helps you to change the Find Out More text to anything you want.', 'events-calendar-modules-for-divi' ),
        'type'             => 'text',
        'show_if'          => array(
            'show_find_out_more' => 'on',
            'layout_style'        => 'style2',
        ),
        'default'          => __('Find Out More', 'events-calendar-modules-for-divi'),
        'toggle_slug'      => 'main_query',
        'computed_affects' => array( '__events' ),
    ),
    'show_venue'            => array(
        'label'            => esc_html__( 'Show Venue', 'events-calendar-modules-for-divi' ),
        'description'      => esc_html__( 'Here you can choose whether to show venue or not.', 'events-calendar-modules-for-divi' ),
        'type'             => 'yes_no_button',
        'option_category'  => 'configuration',
        'toggle_slug'      => 'main_query',
        'options'          => array(
            'on'  => esc_html__( 'Yes', 'events-calendar-modules-for-divi' ),
            'off' => esc_html__( 'No', 'events-calendar-modules-for-divi' ),
        ),
        'default'          => 'on',
        'affects'          => array(
            'venue_font',
            'venue_text_color',
            'venue_line_height',
            'venue_font_size',
            'venue_letter_spacing',
            'venue_text_shadow',
            'venue_text_align',
            'venue_text_shadow',
        ),
        'computed_affects' => array( '__events' ),

    ),
    'show_description'      => array(
        'label'            => esc_html__( 'Show Description', 'events-calendar-modules-for-divi' ),
        'description'      => esc_html__( 'Here you can choose whether to show description or not.', 'events-calendar-modules-for-divi' ),
        'type'             => 'yes_no_button',
        'option_category'  => 'configuration',
        'toggle_slug'      => 'main_query',
        'options'          => array(
            'on'  => esc_html__( 'Yes', 'events-calendar-modules-for-divi' ),
            'off' => esc_html__( 'No', 'events-calendar-modules-for-divi' ),
        ),
        'default'          => 'off',
        'computed_affects' => array( '__events' ),
        'show_if'          => array(
            'layout_style' => 'style2',
        ),
        'affects'          => array(
            'content_font',
            'content_text_color',
            'content_line_height',
            'content_font_size',
            'content_letter_spacing',
            'content_text_shadow',
            'content_text_align',
            'content_text_shadow',
        ),
    ),
    'description_count_type'        => array(
        'label'            => esc_html__( 'Description Type', 'events-calendar-modules-for-divi' ),
        'description'      => esc_html__( 'To select different description type from the.', 'events-calendar-modules-for-divi' ),
        'type'             => 'select',
        'options'          => $descripton_count,
        'default'          => 'short',
        'toggle_slug'      => 'main_query',
        'computed_affects' => array( '__events' ),
        'show_if'          => array(
            'layout_style' => 'style2',
            'show_description' => 'on',
        ),
    ),
    'description_count'        => array(
        'label'            => esc_html__( 'Description Count', 'events-calendar-modules-for-divi' ),
        'description'      => esc_html__( 'To select different description count from the.', 'events-calendar-modules-for-divi' ),
        'type'             => 'number',
        'default'          => '20',
        'toggle_slug'      => 'main_query',
        'computed_affects' => array( '__events' ),
        'show_if'          => array(
            'description_count_type' => 'short',
            'layout_style' => 'style2',
            'show_description' => 'on',
        ),
    ),
    'show_cost'             => array(
        'label'            => esc_html__( 'Show Cost', 'events-calendar-modules-for-divi' ),
        'description'      => esc_html__( 'Here you can choose whether to show cost or not.', 'events-calendar-modules-for-divi' ),
        'type'             => 'yes_no_button',
        'option_category'  => 'configuration',
        'toggle_slug'      => 'main_query',
        'options'          => array(
            'on'  => esc_html__( 'Yes', 'events-calendar-modules-for-divi' ),
            'off' => esc_html__( 'No', 'events-calendar-modules-for-divi' ),
        ),
        'default'          => 'on',
        'computed_affects' => array( '__events' ),
        'show_if'          => array(
            'layout_style' => 'style2',
        ),
    ),
    'show_category'         => array(
        'label'            => esc_html__( 'Show Event Category', 'events-calendar-modules-for-divi' ),
        'description'      => esc_html__( 'Here you can choose whether to show Category or not.', 'events-calendar-modules-for-divi' ),
        'type'             => 'yes_no_button',
        'option_category'  => 'configuration',
        'toggle_slug'      => 'main_query',
        'options'          => array(
            'on'  => esc_html__( 'Yes', 'events-calendar-modules-for-divi' ),
            'off' => esc_html__( 'No', 'events-calendar-modules-for-divi' ),
        ),
        'default'          => 'on',
        'computed_affects' => array( '__events' ),
        'show_if'          => array(
            'layout_style' => 'style2',
        ),
    ),
    'cost_text_color' => array(
		'label'           => esc_html__(' Text Color', 'events-calendar-modules-for-divi'),
		'description'     => esc_html__('To change the Text color Cost', 'events-calendar-modules-for-divi'),
		'type'            => 'color-alpha',
		'option_category' => 'basic_option',
		'toggle_slug'     => 'cost_style',
		'tab_slug'        => 'advanced',
		'show_if'         => array(
			'show_cost' => 'on',
			'layout_style' => 'style2',
		),
	),
	'cost_font_size' => array(
		'label'           => esc_html__(' Font Size', 'events-calendar-modules-for-divi'),
		'description'     => esc_html__('To change the font size Cost', 'events-calendar-modules-for-divi'),
		'type'            => 'range',
		'option_category' => 'basic_option',
		'toggle_slug'     => 'cost_style',
		'tab_slug'        => 'advanced',
		'default'         => '14px',
		'range_settings'  => array(
			'min'  => '10px',
			'max'  => '30px',
			'step' => '1px',
		),
		'show_if'         => array(
			'show_cost' => 'on',
			'layout_style' => 'style2',
		),
	),

);
$feilds = array(
    'layout_background_color'        => array(
        'label'           => esc_html__( 'Background Color', 'events-calendar-modules-for-divi' ),
        'description'     => esc_html__( 'To change the background color for the main container', 'events-calendar-modules-for-divi' ),
        'type'            => 'color-alpha',
        'option_category' => 'basic_option',
        'toggle_slug'     => 'style_layout',
        'tab_slug'        => 'advanced',
    ),
    'layout_background_color__hover'  => array(
        'label'           => esc_html__( 'Background Hover Color', 'events-calendar-modules-for-divi' ),
        'description'     => esc_html__( 'To change the background hover color for the main container', 'events-calendar-modules-for-divi' ),
        'type'            => 'color-alpha',
        'option_category' => 'basic_option',
        'toggle_slug'     => 'style_layout',
        'tab_slug'        => 'advanced',
        'show_if'		  => array(
            'layout_style' => 'style2'
        ),
    ),
    'venue_text_color'               => array(
        'label'           => esc_html__( 'Text Color', 'events-calendar-modules-for-divi' ),
        'description'     => esc_html__( 'To change the text color for the venue', 'events-calendar-modules-for-divi' ),
        'type'            => 'color-alpha',
        'option_category' => 'basic_option',
        'toggle_slug'     => 'venue_style',
        'tab_slug'        => 'advanced',
    ),
    'find_more_backgroud_color'      => array(
        'label'           => esc_html__( 'Background Color', 'events-calendar-modules-for-divi' ),
        'description'     => esc_html__( 'To change the background color find out more', 'events-calendar-modules-for-divi' ),
        'type'            => 'color-alpha',
        'option_category' => 'basic_option',
        'toggle_slug'     => 'find_out_more',
        'tab_slug'        => 'advanced',
        'show_if'     	  => array(
            'show_find_out_more' => 'on',
            'layout_style' => 'style2',
        ),

    ),
    'find_more_padding'              => array(
        'label'       => esc_html__( 'Padding', 'events-calendar-modules-for-divi' ),
        'type'        => 'custom_padding',
        'tab_slug'    => 'advanced',
        'toggle_slug' => 'find_out_more',
        'show_if'     => array(
            'show_find_out_more' => 'on',
            'layout_style' => 'style2',
        ),
    ),
    'find_more_border_color'         => array(
        'label'           => esc_html__( 'Border Color', 'events-calendar-modules-for-divi' ),
        'description'     => esc_html__( 'To change the border color find out more', 'events-calendar-modules-for-divi' ),
        'type'            => 'color-alpha',
        'option_category' => 'basic_option',
        'toggle_slug'     => 'find_out_more',
        'tab_slug'        => 'advanced',
        'show_if'         => array(
            'show_find_out_more' => 'on',
            'layout_style' => 'style2',
        ),
    ),
    'find_more_border_width'         => array(
        'label'           => esc_html__( 'Border Width', 'events-calendar-modules-for-divi' ),
        'description'     => esc_html__( 'To change the border width find out more', 'events-calendar-modules-for-divi' ),
        'type'            => 'range',
        'option_category' => 'basic_option',
        'toggle_slug'     => 'find_out_more',
        'tab_slug'        => 'advanced',
        'default'         => '0px',
        'range_settings'  => array(
            'min'  => '0px',
            'max'  => '10px',
            'step' => '1px',
        ),
        'show_if'         => array(
            'show_find_out_more' => 'on',
            'layout_style' => 'style2',
        ),
    ),
    'find_more_border_style'         => array(
        'label'           => esc_html__( 'Border Style', 'events-calendar-modules-for-divi' ),
        'description'     => esc_html__( 'To change the border style find out more', 'events-calendar-modules-for-divi' ),
        'type'            => 'select',
        'option_category' => 'basic_option',
        'options'         => $border_style,
        'toggle_slug'     => 'find_out_more',
        'tab_slug'        => 'advanced',
        'default'         => 'none',
        'show_if'         => array(
            'show_find_out_more' => 'on', 
            'layout_style' => 'style2',
        ),
    ),
    'find_more_border_radius'         => array(
        'label'           => esc_html__( 'Border Radius', 'events-calendar-modules-for-divi' ),
        'description'     => esc_html__( 'To change the border radius find out more', 'events-calendar-modules-for-divi' ),
        'type'            => 'range',
        'option_category' => 'basic_option',
        'toggle_slug'     => 'find_out_more',
        'tab_slug'        => 'advanced',
        'default'         => '4px',
        'range_settings'  => array(
            'min'  => '0px',
            'max'  => '100px',
            'step' => '1px',
        ),
        'show_if'         => array(
            'show_find_out_more' => 'on',
            'layout_style' => 'style2',
        ),
    ),
    'category_backgroud_color'       => array(
        'label'           => esc_html__( 'Background Color', 'events-calendar-modules-for-divi' ),
        'description'     => esc_html__( 'To change the background color category', 'events-calendar-modules-for-divi' ),
        'type'            => 'color-alpha',
        'option_category' => 'basic_option',
        'toggle_slug'     => 'category_style',
        'tab_slug'        => 'advanced',
        'show_if'         => array(
            'show_category' => 'on',
            'layout_style' => 'style2',
        ),
    ),
    'category_text_color'            => array(
        'label'           => esc_html__( 'Text Color', 'events-calendar-modules-for-divi' ),
        'description'     => esc_html__( 'To change the Text color category', 'events-calendar-modules-for-divi' ),
        'type'            => 'color-alpha',
        'option_category' => 'basic_option',
        'toggle_slug'     => 'category_style',
        'tab_slug'        => 'advanced',
        'show_if'         => array(
            'show_category' => 'on',
            'layout_style' => 'style2',
        ),
    ),
    'category_padding'               => array(
        'label'       => esc_html__( 'Padding', 'events-calendar-modules-for-divi' ),
        'type'        => 'custom_padding',
        'tab_slug'    => 'advanced',
        'toggle_slug' => 'category',
        'show_if'     => array(
            'show_category' => 'on',
            'layout_style' => 'style2',
        ),
    ),
    'date_highlight_text_color'      => array(
        'label'           => esc_html__( 'Text Color', 'events-calendar-modules-for-divi' ),
        'description'     => esc_html__( 'To change the Text color Date Highlight', 'events-calendar-modules-for-divi' ),
        'type'            => 'color-alpha',
        'option_category' => 'basic_option',
        'toggle_slug'     => 'date_highlight',
        'tab_slug'        => 'advanced',
        'show_if'         => array(
            'show_date_highlight' => 'on',
        ),
    ),
    'date_highlight_backgroud_color' => array(
        'label'           => esc_html__( 'Background Color', 'events-calendar-modules-for-divi' ),
        'description'     => esc_html__( 'To change the background color Date Highlight', 'events-calendar-modules-for-divi' ),
        'type'            => 'color-alpha',
        'option_category' => 'basic_option',
        'toggle_slug'     => 'date_highlight',
        'tab_slug'        => 'advanced',
        'show_if'         => array(
            'show_date_highlight' => 'on',
        ),
    ),

    '__events'                       => array(
        'type'                => 'computed',
        'computed_callback'   => array( 'ECMD_EventsLayouts', 'ecmd_get_events' ),
        'computed_depends_on' => array(
            'select_layouts',
            'time',
            'posts_number',
            'order',
            'date_formats',
            'featured_events',
            'layout_style',
            'find_out_more',
            'show_description',
            'show_venue',
            'show_event_image',
            'default_image',
            'show_cost',
            'show_find_out_more',
            'show_date_highlight',
            'date_highlight_format',
            'show_category',
            'description_count_type',
            'description_count'
        ),
    ),
);