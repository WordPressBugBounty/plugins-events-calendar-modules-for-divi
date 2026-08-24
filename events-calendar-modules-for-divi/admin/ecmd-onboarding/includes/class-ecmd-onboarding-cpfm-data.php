<?php
/**
 * Events Calendar Modules for Divi (Free) — onboarding data for the shared CPFM payload.
 *
 * Plugin-specific configuration only. CPFM keeps the common consent / cron logic.
 *
 * @package Events_Calendar_Modules_For_Divi
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ECMD_Onboarding_Cpfm_Data' ) ) {

	/**
	 * Divi Free onboarding data appended to the shared CPFM snapshot.
	 */
	final class ECMD_Onboarding_Cpfm_Data {

		const CHOICE_OPTION      = 'cpfm_opt_in_choice_cool_events';
		const PREFERENCES_OPTION = 'cpfm_onboarding_preferences_cool_events';
		const PLUGIN_KEY         = 'divi_free';
		const PLUGIN_SLUG        = 'events-calendar-modules-for-divi';

		/**
		 * @return string|null 'yes'|'no'|null when unset.
		 */
		public static function get_choice() {
			$choice = get_option( self::CHOICE_OPTION, null );
			if ( 'yes' === $choice || 'no' === $choice ) {
				return $choice;
			}
			return null;
		}

		/**
		 * @return bool
		 */
		public static function should_show_telemetry() {
			return 'yes' !== self::get_choice();
		}

		/**
		 * @return array{show: bool, checked: bool, choice: string|null}
		 */
		public static function get_telemetry_localize() {
			return array(
				'show'    => self::should_show_telemetry(),
				'checked' => true,
				'choice'  => self::get_choice(),
			);
		}

		/**
		 * @param string $yes_or_no 'yes' or 'no'.
		 * @return void
		 */
		public static function save_choice( $yes_or_no ) {
			$choice = ( 'yes' === $yes_or_no ) ? 'yes' : 'no';
			update_option( self::CHOICE_OPTION, $choice, false );
		}

		/**
		 * @param bool                 $telemetry  Whether the user accepted sharing.
		 * @param array<string, mixed> $selections Raw wizard selections.
		 * @return array<string, mixed>
		 */
		public static function save_preferences( $telemetry, $selections ) {
			$row = array(
				'plugin'     => self::PLUGIN_SLUG,
				'updated_at' => gmdate( 'Y-m-d H:i:s' ),
				'telemetry'  => (bool) $telemetry,
				'selections' => self::sanitize_selections( $selections ),
			);

			$all = get_option( self::PREFERENCES_OPTION, array() );
			if ( ! is_array( $all ) ) {
				$all = array();
			}

			$all[ self::PLUGIN_KEY ] = $row;
			update_option( self::PREFERENCES_OPTION, $all, false );

			return $row;
		}

		/**
		 * @return array<string, mixed>
		 */
		public static function get_preferences_for_extra_details() {
			if ( 'yes' !== self::get_choice() ) {
				return array();
			}

			$all = get_option( self::PREFERENCES_OPTION, array() );
			return is_array( $all ) ? $all : array();
		}

		/**
		 * @return string[]
		 */
		public static function selection_allowlist() {
			return array(
				'editor',
				'mode',
				'layout',
				'time',
			);
		}

		/**
		 * @param mixed $raw Decoded JSON / array.
		 * @return array<string, string|bool|array>
		 */
		public static function sanitize_selections( $raw ) {
			if ( ! is_array( $raw ) ) {
				return array();
			}

			$out       = array();
			$allowlist = self::selection_allowlist();

			foreach ( $allowlist as $key ) {
				if ( ! array_key_exists( $key, $raw ) ) {
					continue;
				}
				$value = $raw[ $key ];

				if ( is_array( $value ) ) {
					$clean = array();
					if ( isset( $value['value'] ) ) {
						$clean['value'] = sanitize_text_field( (string) $value['value'] );
					}
					if ( isset( $value['label'] ) ) {
						$clean['label'] = sanitize_text_field( (string) $value['label'] );
					}
					if ( ! empty( $clean ) ) {
						$out[ $key ] = $clean;
					}
					continue;
				}

				if ( is_bool( $value ) ) {
					$out[ $key ] = $value;
					continue;
				}

				$out[ $key ] = sanitize_text_field( (string) $value );
			}

			return $out;
		}
	}
}
