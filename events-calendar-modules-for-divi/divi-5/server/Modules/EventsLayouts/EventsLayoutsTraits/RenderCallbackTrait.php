<?php

namespace ECMD\Modules\EventsLayouts\EventsLayoutsTraits;

if (! defined('ABSPATH')) {
    die('Direct access forbidden.');
}

use ECMD\Modules\EventsLayouts\EventsLayouts;
use ET\Builder\Framework\Utility\HTMLUtility;
use ET\Builder\Packages\Module\Module;
use ECMD\Modules\EventsLayouts\EventsLayoutsTraits\ModuleHelper;

trait RenderCallbackTrait
{

    // use ModulesHelper;
    public static function render_callback($attrs, $content, $block, $elements)
    {
        // Attributes with default values
        $props = array(
            'posts_per_page' => ModuleHelper::get_attr_value($attrs, 'posts_per_page', 6),
            'order' => ModuleHelper::get_attr_value($attrs, 'order', 'ASC'),
            'select_layouts' => ModuleHelper::get_attr_value($attrs, 'select_layouts', 'list'),
            'time' => ModuleHelper::get_attr_value($attrs, 'time', 'future'),
            'layout_style' => ModuleHelper::get_attr_value($attrs, 'layout_style', 'style1'),
            'featured_events' => ModuleHelper::get_attr_value($attrs, 'featured_events', 'off'),
            'show_container_date' => ModuleHelper::get_attr_value($attrs, 'show_container_date', 'on'),
            'date_formats' => ModuleHelper::get_attr_value($attrs, 'date_formats', 'sedt'),
            'show_date_highlight' => ModuleHelper::get_attr_value($attrs, 'show_date_highlight', 'on'),
            'date_highlight_format' => ModuleHelper::get_attr_value($attrs, 'date_highlight_format', 'DM'),
            'show_event_image' => ModuleHelper::get_attr_value($attrs, 'show_event_image', 'on'),
            'default_image' => ModuleHelper::get_attr_value($attrs, 'default_image', ECMD_URL . 'assets/images/event-template-bg.png'),
            'show_find_out_more' => ModuleHelper::get_attr_value($attrs, 'show_find_out_more', 'on'),
            'find_out_more_text' => ModuleHelper::get_attr_value($attrs, 'find_out_more_text', 'Find Out More'),
            'show_cost' => ModuleHelper::get_attr_value($attrs, 'show_cost', 'on'),
            'show_venue' => ModuleHelper::get_attr_value($attrs, 'show_venue', 'on'),
            'show_description' => ModuleHelper::get_attr_value($attrs, 'show_description', 'off'), 
            'show_category' => ModuleHelper::get_attr_value($attrs, 'show_category', 'on'),
            'description_count_type' => ModuleHelper::get_attr_value($attrs, 'description_count_type', 'short'),
            'description_count' => ModuleHelper::get_attr_value($attrs, 'description_count', 20),
            'unknownAttributes' => $attrs['unknownAttributes'] ?? [],
        );
        // Render list of recent posts and its div wrapper.
        $module_inner = HTMLUtility::render(
            [
                'tag'               => 'div',
                'attributes'        => [
                    'class' => 'ecmd_event_container',
                ],
                'childrenSanitizer' => 'et_core_esc_previously',
                'children'          => self::render_events($props),
            ]
        );
        // This are the module elements that will be rendered in the frontend.
        $module_elements = $elements->style_components(
            [
                'attrName' => 'module'
            ]
        );

        // This are the children of the module container, which are the module elements and the module inner.
        $module_container_children = $module_elements . $module_inner;

        return Module::render(
            [
                // FE only.
                'orderIndex'          => $block->parsed_block['orderIndex'],
                'storeInstance'       => $block->parsed_block['storeInstance'],

                // VB equivalent.
                'attrs'               => $attrs,
                'elements'            => $elements,
                'id'                  => $block->parsed_block['id'],
                'moduleClassName'     => 'EventsLayouts',
                'name'                => $block->block_type->name,
                'classnamesFunction'  => [EventsLayouts::class, 'module_classnames'],
                'moduleCategory'      => $block->block_type->category,
                'stylesComponent'     => [EventsLayouts::class, 'module_styles'],
                'scriptDataComponent' => [EventsLayouts::class, 'module_script_data'],
                'children'            => $module_container_children,
            ]
        );
    }
}
