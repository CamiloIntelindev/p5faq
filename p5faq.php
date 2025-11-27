<?php
/**
 * Plugin Name: P5 FAQ
 * Plugin URI: https://p5.com
 * Description: Plugin to manage FAQs with repeater fields and shortcode to insert in any section
 * Version: 1.0.2
 * Author: P5
 * Author URI: https://p5.com
 * License: GPL2
 * Text Domain: p5faq
 * Domain Path: /languages
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define constants
define('P5FAQ_VERSION', '1.0.2');
define('P5FAQ_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('P5FAQ_PLUGIN_URL', plugin_dir_url(__FILE__));
define('P5FAQ_PLUGIN_BASENAME', plugin_basename(__FILE__));


/**
 * Load plugin textdomain for translations
 */
function p5faq_load_textdomain() {
    load_plugin_textdomain('p5faq', false, dirname(P5FAQ_PLUGIN_BASENAME) . '/languages/');
}
add_action('init', 'p5faq_load_textdomain');

/**
 * The code that runs during plugin activation
 */
function activate_p5faq() {
    require_once P5FAQ_PLUGIN_DIR . 'includes/class-p5faq-activator.php';
    P5FAQ_Activator::activate();
}
register_activation_hook(__FILE__, 'activate_p5faq');

/**
 * The code that runs during plugin deactivation
 */
function deactivate_p5faq() {
    require_once P5FAQ_PLUGIN_DIR . 'includes/class-p5faq-deactivator.php';
    P5FAQ_Deactivator::deactivate();
}
register_deactivation_hook(__FILE__, 'deactivate_p5faq');

/**
 * Load required files
 */
require_once P5FAQ_PLUGIN_DIR . 'includes/class-p5faq-post-type.php';
require_once P5FAQ_PLUGIN_DIR . 'admin/class-p5faq-admin.php';
require_once P5FAQ_PLUGIN_DIR . 'public/class-p5faq-public.php';


/**
 * Initialize the plugin
 */
function run_p5faq() {
    // Initialize Post Type
    new P5FAQ_Post_Type();
    
    // Initialize Admin
    if (is_admin()) {
        new P5FAQ_Admin('p5faq', P5FAQ_VERSION);
    }
    
    // Initialize Public
    new P5FAQ_Public('p5faq', P5FAQ_VERSION);
}
add_action('plugins_loaded', 'run_p5faq');
