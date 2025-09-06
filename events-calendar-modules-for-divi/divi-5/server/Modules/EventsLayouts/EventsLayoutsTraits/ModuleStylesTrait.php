<?php

namespace ECMD\Modules\EventsLayouts\EventsLayoutsTraits;

if (! defined('ABSPATH')) {
    die('Direct access forbidden.');
}

use ET\Builder\FrontEnd\Module\Style;
use ET\Builder\Packages\Module\Layout\Components\StyleCommon\CommonStyle;
use VARIANT;

trait ModuleStylesTrait
{

    use CustomCssTrait;
    public static function module_styles($args)
    {
        $attrs        = $args['attrs'] ?? [];
        $order_class  = $args['orderClass'];
        $elements     = $args['elements'];
        $settings     = $args['settings'] ?? [];
        Style::add(
            [
                'id'            => $args['id'],
                'name'          => $args['name'],
                'orderIndex'    => $args['orderIndex'],
                'storeInstance' => $args['storeInstance'],
                'styles'        => [
                    // Module.
                    $elements->style(
                        [
                            'attrName'   => 'module',
                            'styleProps' => [
                                'disabledOn' => [
                                    'disabledModuleVisibility' => $settings['disabledModuleVisibility'] ?? null,
                                ],
                            ],
                        ]
                    ),
                    $elements->style(
                        [
                            'attrName'   => 'container_style',
                        ]
                    ),
                    $elements->style(
                        [
                            'attrName'   => 'title_style',
                        ]
                    ),
                    $elements->style(
                        [
                            'attrName'   => 'date_style',
                        ]
                    ),
                    $elements->style(
                        [
                            'attrName'   => 'date_highlight',
                        ]
                    ),
                    $elements->style(
                        [
                            'attrName'   => 'content_style',
                        ]
                    ),
                    $elements->style(
                        [
                            'attrName'   => 'venue_style',
                        ]
                    ),
                    $elements->style(
                        [
                            'attrName'   => 'find_out_more',
                        ]
                    ),
                    $elements->style(
                        [
                            'attrName'   => 'category_style',
                        ]
                    ),
                    $elements->style(
                        [
                            'attrName'   => 'cost_style',
                        ]
                    ),
                    CommonStyle::style(
                        [
                            'selector'            => $order_class . ' .ecmd-list-post',
                            'attr'                => $attrs['container_style']['decoration']['backgroundColor'] ?? [],
                            'declarationFunction' => function ($declaration_function_args) {
                                $attr_value = $declaration_function_args['attrValue']['container_style'] ?? [];
                                return "background-color: {$attr_value} !important;";
                            },
                        ]
                    ),
                    CommonStyle::style(
                        [
                            'selector'            => $order_class . ' .ecmd-date-highlight .ecmd-date-area',
                            'attr'                => $attrs['date_highlight']['decoration']['backgroundColor'] ?? [],
                            'declarationFunction' => function ($declaration_function_args) {
                                $attr_value = $declaration_function_args['attrValue']['date_highlight'] ?? [];
                                return "background-color:{$attr_value} !important;";
                            },
                        ]
                    ),
                    CommonStyle::style(
                        [
                            'selector'            => $order_class . ' .ecmd-event-readmore',
                            'attr'                => $attrs['find_out_more']['decoration']['backgroundColor'] ?? [],
                            'declarationFunction' => function ($declaration_function_args) {
                                $attr_value = $declaration_function_args['attrValue']['find_out_more'] ?? [];
                                return "background-color:{$attr_value} !important;";
                            },
                        ]
                    ),

                    CommonStyle::style(
                        [
                            'selector'            => $order_class . ' .ecmd-category ul li a',
                            'attr'                => $attrs['category_style']['decoration']['backgroundColor'] ?? [],
                            'declarationFunction' => function ($declaration_function_args) {
                                $attr_value = $declaration_function_args['attrValue']['category_style'] ?? [];
                                return "background-color:{$attr_value} !important;";
                            },
                        ]
                    ),
                ],

            ]
        );
    }
}
