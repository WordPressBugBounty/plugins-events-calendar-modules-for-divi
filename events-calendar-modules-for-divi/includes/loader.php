<?php
/**
 * Prevent direct access to the file
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if (!class_exists('ET_Builder_Element')) {
    return;
}

require_once plugin_dir_path(__FILE__) . 'modules/ModulesCore/ModulesCore.php';
require_once plugin_dir_path(__FILE__) . 'modules/ModulesCore/ModulesHelper.php';
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
$module_files = glob(__DIR__ . '/modules/*/*.php');

// Sanitize and validate module files
$module_files = array_filter($module_files, function($file) {
    return preg_match('/^.+\/modules\/[^\/]+\/[^\/]+\.php$/', $file); // Ensure valid file structure
});

// Load custom Divi Builder modules
foreach ($module_files as $module_file) {
    if (is_readable($module_file)) { // Check if the file is readable
        require_once $module_file;
    } else {
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
            error_log(esc_html__('Custom Divi module could not be loaded. File is not readable.', 'events-calendar-modules-for-divi'));
        }
    }
}
