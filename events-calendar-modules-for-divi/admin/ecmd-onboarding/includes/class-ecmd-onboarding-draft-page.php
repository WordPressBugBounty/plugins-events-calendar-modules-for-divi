<?php
/**
 * Creates onboarding draft pages with the Events Layout module for Divi 4 / 5.
 *
 * @package Events_Calendar_Modules_For_Divi
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ECMD_Onboarding_Draft_Page' ) ) {

	/**
	 * Builds Divi Builder content from wizard selections and inserts a draft page.
	 */
	final class ECMD_Onboarding_Draft_Page {

		const META_KEY = '_ecmd_onboarding_draft';

		/**
		 * Free plugin layouts. Grid/carousel require Pro and are clamped here.
		 *
		 * @var array<int, string>
		 */
		const FREE_LAYOUTS = array( 'list' );

		/**
		 * @param array<string, mixed> $args {
		 *     @type string              $title      Page title.
		 *     @type array<string,mixed> $selections Wizard selections (layout, time).
		 * }
		 * @return array{postId:int,editUrl:string,viewUrl:string,previewUrl:string,format:string}|WP_Error
		 */
		public static function create( $args ) {
			$title      = isset( $args['title'] ) ? sanitize_text_field( $args['title'] ) : '';
			$selections = isset( $args['selections'] ) && is_array( $args['selections'] ) ? $args['selections'] : array();
			$module     = self::resolve_module_attrs( $selections );

			if ( '' === $title ) {
				$title = sprintf(
					/* translators: %s: layout name */
					__( 'Events — %s', 'events-calendar-modules-for-divi' ),
					ucwords( str_replace( '-', ' ', $module['select_layouts'] ) )
				);
			}

			$generation = self::get_divi_generation();
			if ( 5 === $generation ) {
				$format  = 'divi-5';
				$content = self::build_divi5_module_content( $module );
			} else {
				$format  = 'divi-4';
				$content = self::build_divi4_module_content( $module );
			}

			$existing = (int) get_option( ECMD_Onboarding_Page::PAGE_ID_OPTION, 0 );
			$status   = $existing ? get_post_status( $existing ) : false;
			$reuse    = ( $existing && 'draft' === $status && get_post_meta( $existing, self::META_KEY, true ) );

			if ( $reuse ) {
				$post_id = wp_update_post(
					array(
						'ID'           => $existing,
						'post_title'   => $title,
						'post_content' => $content,
					),
					true
				);
			} else {
				$post_id = wp_insert_post(
					array(
						'post_title'   => $title,
						'post_content' => $content,
						'post_status'  => 'draft',
						'post_type'    => 'page',
						'post_author'  => get_current_user_id(),
					),
					true
				);
			}

			if ( is_wp_error( $post_id ) ) {
				return $post_id;
			}

			update_option( ECMD_Onboarding_Page::PAGE_ID_OPTION, (int) $post_id, false );

			update_post_meta( $post_id, self::META_KEY, 1 );
			update_post_meta( $post_id, self::META_KEY . '_selections', $module );
			update_post_meta( $post_id, self::META_KEY . '_format', $format );
			self::apply_divi_builder_meta( $post_id );

			$edit_url = get_edit_post_link( $post_id, 'raw' );
			if ( ! $edit_url ) {
				$edit_url = admin_url( 'post.php?post=' . $post_id . '&action=edit' );
			}

			$permalink = get_permalink( $post_id );
			if ( $permalink ) {
				$edit_url = add_query_arg(
					array(
						'et_fb'     => '1',
						'PageSpeed' => 'off',
					),
					$permalink
				);
			}

			$view_url    = get_permalink( $post_id );
			$preview_url = get_preview_post_link( $post_id );

			return array(
				'postId'     => (int) $post_id,
				'editUrl'    => $edit_url,
				'viewUrl'    => $view_url ? $view_url : '',
				'previewUrl' => $preview_url ? $preview_url : ( $view_url ? $view_url : '' ),
				'format'     => $format,
				'updated'    => (bool) $reuse,
			);
		}

		/**
		 * Map wizard selections → module attribute values.
		 *
		 * @param array<string, mixed> $selections Wizard selections.
		 * @return array<string, string>
		 */
		private static function resolve_module_attrs( $selections ) {
			$layout = isset( $selections['layout'] ) ? sanitize_key( (string) $selections['layout'] ) : 'list';
			$time   = isset( $selections['time'] ) ? sanitize_key( (string) $selections['time'] ) : 'upcoming';

			if ( ! in_array( $layout, self::FREE_LAYOUTS, true ) ) {
				$layout = 'list';
			}

			$time_map = array(
				'upcoming' => 'future',
				'past'     => 'past',
				'both'     => 'all',
				'future'   => 'future',
				'all'      => 'all',
			);
			$module_time = isset( $time_map[ $time ] ) ? $time_map[ $time ] : 'future';

			return array(
				'select_layouts' => $layout,
				'layout_style'   => 'style1',
				'posts_number'   => '6',
				'time'           => $module_time,
				'order'          => 'ASC',
				'featured_events'=> 'false',
			);
		}

		/**
		 * Divi theme version string (child themes resolve to parent Divi).
		 *
		 * @return string
		 */
		public static function get_divi_version() {
			$theme = wp_get_theme();

			if ( $theme->parent() && 'divi' === strtolower( (string) $theme->get_template() ) ) {
				return (string) $theme->parent()->get( 'Version' );
			}

			if ( 'divi' === strtolower( (string) $theme->get_stylesheet() ) ) {
				return (string) $theme->get( 'Version' );
			}

			$divi_theme = wp_get_theme( 'Divi' );

			return $divi_theme->exists() ? (string) $divi_theme->get( 'Version' ) : '';
		}

		/**
		 * Major Divi builder generation for storage format.
		 *
		 * @return int 4 = shortcode builder, 5 = block builder
		 */
		public static function get_divi_generation() {
			// Prefer builder version (matches what Divi stores in post_content).
			$version = defined( 'ET_BUILDER_VERSION' ) ? (string) ET_BUILDER_VERSION : '';
			if ( '' === $version ) {
				$version = self::get_divi_version();
			}
			if ( '' === $version ) {
				return 4;
			}

			return version_compare( $version, '5.0.0', '>=' ) ? 5 : 4;
		}

		/**
		 * Enable Divi Builder on the draft page.
		 *
		 * @param int $post_id Page ID.
		 */
		private static function apply_divi_builder_meta( $post_id ) {
			$post_id = (int) $post_id;
			if ( $post_id <= 0 ) {
				return;
			}
			update_post_meta( $post_id, '_et_pb_use_builder', 'on' );
			update_post_meta( $post_id, '_et_pb_page_layout', 'et_full_width_page' );
		}

		/**
		 * Divi 4: section → row → column → ecmd_events_layouts module.
		 *
		 * @param array<string, string> $module Module attrs.
		 * @return string
		 */
		private static function build_divi4_module_content( $module ) {
			$builder_version = self::get_divi_version();
			if ( '' === $builder_version ) {
				$builder_version = defined( 'ET_BUILDER_VERSION' ) ? (string) ET_BUILDER_VERSION : '4.0';
			}

			$attrs = array(
				'select_layouts'  => $module['select_layouts'],
				'layout_style'    => $module['layout_style'],
				'posts_number'    => $module['posts_number'],
				'time'            => $module['time'],
				'order'           => $module['order'],
				'featured_events' => $module['featured_events'],
				'_builder_version'=> $builder_version,
			);

			$attr_string = '';
			foreach ( $attrs as $key => $value ) {
				$attr_string .= ' ' . $key . '="' . esc_attr( (string) $value ) . '"';
			}

			return '[et_pb_section fb_built="1"][et_pb_row][et_pb_column type="4_4" _builder_version="' . esc_attr( $builder_version ) . '"]'
				. '[ecmd_events_layouts' . $attr_string . '][/ecmd_events_layouts]'
				. '[/et_pb_column][/et_pb_row][/et_pb_section]';
		}

		/**
		 * Divi 5: native block module (ecmd/events-calendar-modules-for-divi).
		 * Must NOT use divi/shortcode-module — that is the D4 bridge and breaks the VB.
		 *
		 * @param array<string, string> $module Module attrs.
		 * @return string
		 */
		private static function build_divi5_module_content( $module ) {
			$builder_version = defined( 'ET_BUILDER_VERSION' ) ? (string) ET_BUILDER_VERSION : '';
			if ( '' === $builder_version ) {
				$builder_version = self::get_divi_version();
			}
			if ( '' === $builder_version ) {
				$builder_version = '5.0.0';
			}

			// Match the attr shape Divi 5 actually saves when the module is added
			// manually: { desktop: { value: { attrName: value } } }.
			$module_attrs = array(
				'select_layouts'  => self::divi5_scalar_attr( 'select_layouts', $module['select_layouts'] ),
				'layout_style'    => self::divi5_scalar_attr( 'layout_style', $module['layout_style'] ),
				// phpcs:ignore WordPress.WP.PostsPerPage.posts_per_page_posts_per_page -- Divi 5 module attribute; value is a small events count from posts_number, not a WP_Query limit.
				'posts_per_page'  => self::divi5_scalar_attr( 'posts_per_page', $module['posts_number'] ),
				'time'            => self::divi5_scalar_attr( 'time', $module['time'] ),
				'order'           => self::divi5_scalar_attr( 'order', $module['order'] ),
				'featured_events' => self::divi5_scalar_attr( 'featured_events', 'off' ),
				'builderVersion'  => $builder_version,
			);

			$section_attrs = array(
				'module'         => array(
					'decoration' => array(
						'layout' => array(
							'desktop' => array(
								'value' => array(
									'display' => 'block',
								),
							),
						),
					),
				),
				'builderVersion' => $builder_version,
			);
			$row_attrs     = array(
				'module'         => array(
					'advanced'   => array(
						'columnStructure'     => array(
							'desktop' => array(
								'value' => '4_4',
							),
						),
						'flexColumnStructure' => array(
							'desktop' => array(
								'value' => 'equal-columns_1',
							),
						),
					),
					'decoration' => array(
						'layout' => array(
							'desktop' => array(
								'value' => array(
									'flexWrap' => 'nowrap',
								),
							),
						),
					),
				),
				'builderVersion' => $builder_version,
			);
			$column_attrs  = array(
				'module'         => array(
					'advanced'   => array(
						'type' => array(
							'desktop' => array(
								'value' => '4_4',
							),
						),
					),
					'decoration' => array(
						'sizing' => array(
							'desktop' => array(
								'value' => array(
									'flexType' => '24_24',
								),
							),
						),
					),
				),
				'builderVersion' => $builder_version,
			);

			$section_json = self::divi5_json( $section_attrs );
			$row_json     = self::divi5_json( $row_attrs );
			$column_json  = self::divi5_json( $column_attrs );
			$module_json  = self::divi5_json( $module_attrs );

			if ( ! $section_json || ! $row_json || ! $column_json || ! $module_json ) {
				return self::build_divi4_module_content( $module );
			}

			return "<!-- wp:divi/placeholder -->\n"
				. '<!-- wp:divi/section ' . $section_json . " -->\n"
				. '<!-- wp:divi/row ' . $row_json . " -->\n"
				. '<!-- wp:divi/column ' . $column_json . " -->\n"
				. '<!-- wp:ecmd/events-calendar-modules-for-divi ' . $module_json . " /-->\n"
				. "<!-- /wp:divi/column -->\n"
				. "<!-- /wp:divi/row -->\n"
				. "<!-- /wp:divi/section -->\n"
				. '<!-- /wp:divi/placeholder -->';
		}

		/**
		 * @param string $label Admin label.
		 * @return array<string,mixed>
		 */
		private static function divi5_admin_label( $label ) {
			return array(
				'meta' => array(
					'adminLabel' => array(
						'desktop' => array(
							'value' => $label,
						),
					),
				),
			);
		}

		/**
		 * Nested D5 attr shape used by FieldContainer subName + get_attr_value().
		 *
		 * @param string $name  Attribute / subName.
		 * @param string $value Scalar value.
		 * @return array<string,mixed>
		 */
		private static function divi5_scalar_attr( $name, $value ) {
			return array(
				'desktop' => array(
					'value' => array(
						$name => (string) $value,
					),
				),
			);
		}

		/**
		 * @param array<string,mixed> $attrs Block attributes.
		 * @return string|false
		 */
		private static function divi5_json( $attrs ) {
			$json = wp_json_encode( $attrs );
			if ( ! is_string( $json ) || '' === $json ) {
				return false;
			}
			return str_replace( '\\\\u', '\\u', $json );
		}
	}
}
