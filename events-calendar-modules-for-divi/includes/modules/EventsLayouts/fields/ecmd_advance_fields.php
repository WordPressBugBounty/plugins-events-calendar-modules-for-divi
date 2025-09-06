<?php
// Initialize the advanced fields array with all fields set to false (hidden).
		$advanced_fields                   = array();
		$advanced_fields['background']     = false;
		$advanced_fields['box_shadow']     = false;
		$advanced_fields['button']         = false;
		$advanced_fields['filters']        = false;
		$advanced_fields['margin_padding'] = false;
		$advanced_fields['max_width']      = false;
		$advanced_fields['animation']      = false;
		$advanced_fields['transform']      = false;
		$advanced_fields['text']           = false;
		$advanced_fields['link_options']   = false;
		$advanced_fields['margin_padding'] = false;

		// Configure the borders style layout for the container.
		$advanced_fields['borders']['style_layout'] = array(
			// 'label_prefix' => esc_html__( 'Container', 'events-calendar-modules-for-divi' ),
			'css'          => array(
			'main' 		   => array(
					'border_styles' => '%%order_class%% .ecmd-list-post',
					'border_radii'  => '%%order_class%% .ecmd-list-post',
					// 'important'     => 'all',
				),
			),
			'defaults'      => array(
			'border_styles' => array(
					'width' => '0px',
					'color' => '#333333',
					'style' => 'none',
				),
				'border_radii'  => 'on|10px|10px|10px|10px',
			),
			'tab_slug'     => 'advanced',
			'toggle_slug'  => 'style_layout',

		);

		// Configure the margin and padding for the container.
		$advanced_fields['margin_padding'] = array(
			// 'label_prefix' => esc_html__( 'Container', 'events-calendar-modules-for-divi' ),
			'css'          => array(
				'main'      => '%%order_class%% .ecmd-list-post',
				'important' => true,

			),
			'toggle_slug'  => 'style_layout',
			'tab_slug'     => 'advanced',
			'use_margin' => false,
		);

		// Configure the font styles for various elements.
		$advanced_fields['fonts']['title'] = array(
			// 'label_prefix' => esc_html__( 'Font', 'events-calendar-modules-for-divi' ),
			'css'          => array(
				'main'      => '%%order_class%% .ecmd-event-title',
				'important' => 'all',
			),
			'tab_slug'     => 'advanced',
			'toggle_slug'  => 'title_style',


		);
		$advanced_fields['fonts']['date'] = array(
			// 'label_prefix'    => esc_html__( 'Font', 'events-calendar-modules-for-divi' ),
			'css'             => array(
				'main'      => '%%order_class%% .ecmd-event-schedule',
				'important' => 'all',
			),
			'tab_slug'        => 'advanced',
			'toggle_slug'     => 'date_style',
			'hide_text_align' => true,
			'hide_text_shadow' => true,
			'depends_show_if' => 'on',
		);
		$advanced_fields['fonts']['content'] = array(
			// 'label_prefix' => esc_html__( 'Font', 'events-calendar-modules-for-divi' ),
			'css'          => array(
				'main'      => '%%order_class%% .ecmd-event-content',
				'important' => true,
			),
			'tab_slug'     => 'advanced',
			'toggle_slug'  => 'content_style',
			'hide_text_shadow' => true,
			'depends_show_if' => 'on',
		);

		$advanced_fields['fonts']['venue'] = array(
			// 'label_prefix'    => esc_html__( 'Font', 'events-calendar-modules-for-divi' ),
			'css'             => array(
				'main'      => '%%order_class%% .ecmd-list-venue, %%order_class%% .ecmd-google a',
				'important' => 'all',
			),
			'tab_slug'        => 'advanced',
			'toggle_slug'     => 'venue_style',
			'hide_text_color' => true,
			'hide_text_shadow' => true,
			'hide_text_align' => true,
			'depends_show_if' => 'on',
		);
		// Configure the borders for the "Find Out More" element.
		$advanced_fields['fonts']['find_out_more'] = array(
			// 'label_prefix' => esc_html__( 'Font', 'events-calendar-modules-for-divi' ),
			'css'          => array(
				'main'      => '%%order_class%% .ecmd-event-readmore',
				'important' => 'all',
			),
			'tab_slug'     => 'advanced',
			'toggle_slug'  => 'find_out_more',
			'depends_show_if' => 'style2',
			'hide_text_shadow' => true,
		);
