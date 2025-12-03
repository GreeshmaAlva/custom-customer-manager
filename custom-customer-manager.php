<?php
/**
 * Plugin Name: Custom Customer Manager
 * Description: A custom plugin for managing customer data in a separate database table.
 * Version: 1.0
 * Author: Your Name
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// 1. Define constants for plugin paths and URLs
define( 'CCM_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'CCM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// 2. Include required files (Database Class)
require_once CCM_PLUGIN_PATH . 'includes/class-ccm-database.php';
require_once CCM_PLUGIN_PATH . 'includes/class-ccm-admin.php';
require_once CCM_PLUGIN_PATH . 'includes/class-ccm-list-table.php';
require_once CCM_PLUGIN_PATH . 'includes/class-ccm-shortcode.php';

/**
 * Plugin activation hook.
 * Calls the method to create the customer database table.
 */
function ccm_activate_plugin() {
    CCM_Customer_DB::create_table(); // ADDED
}
register_activation_hook( __FILE__, 'ccm_activate_plugin' ); // UNCOMMENTED


// 4. Initialization Placeholder
new CCM_Customer_Admin();
new CCM_Customer_Shortcode();
?>