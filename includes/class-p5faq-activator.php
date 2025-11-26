<?php
/**
 * Fired during plugin activation
 *
 * @package    P5FAQ
 * @subpackage P5FAQ/includes
 */

class P5FAQ_Activator {

    /**
     * Plugin activation logic
     *
     * Registers the FAQ post type and flushes rewrite rules.
     */
    public static function activate() {
        // Register the post type temporarily for flush to work
        self::register_post_type();
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Register FAQ post type (temporary for activation)
     */
    private static function register_post_type() {
        $labels = array(
            'name'                  => _x('FAQs', 'Post Type General Name', 'p5faq'),
            'singular_name'         => _x('FAQ', 'Post Type Singular Name', 'p5faq'),
            'menu_name'             => __('FAQs', 'p5faq'),
        );

        $args = array(
            'label'                 => __('FAQ', 'p5faq'),
            'description'           => __('Frequently Asked Questions', 'p5faq'),
            'labels'                => $labels,
            'supports'              => array('title'),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 20,
            'menu_icon'             => 'dashicons-editor-help',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => false,
            'can_export'            => true,
            'has_archive'           => false,
            'exclude_from_search'   => true,
            'publicly_queryable'    => false,
            'capability_type'       => 'post',
            'show_in_rest'          => false,
        );

        register_post_type('faq', $args);
    }
}
