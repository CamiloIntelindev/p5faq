<?php
/**
 * Register FAQ Custom Post Type
 *
 * @package    P5FAQ
 * @subpackage P5FAQ/includes
 */

class P5FAQ_Post_Type {

    /**
     * Initialize the class
     */
    public function __construct() {
        add_action('init', array($this, 'register_post_type'), 0);
    }

    /**
     * Register FAQ Custom Post Type
     */
    public function register_post_type() {
        $labels = array(
            'name'                  => _x('FAQs', 'Post Type General Name', 'p5faq'),
            'singular_name'         => _x('FAQ', 'Post Type Singular Name', 'p5faq'),
            'menu_name'             => __('FAQs', 'p5faq'),
            'name_admin_bar'        => __('FAQ', 'p5faq'),
            'archives'              => __('FAQ Archives', 'p5faq'),
            'attributes'            => __('FAQ Attributes', 'p5faq'),
            'parent_item_colon'     => __('Parent FAQ:', 'p5faq'),
            'all_items'             => __('All FAQs', 'p5faq'),
            'add_new_item'          => __('Add New FAQ', 'p5faq'),
            'add_new'               => __('Add New', 'p5faq'),
            'new_item'              => __('New FAQ', 'p5faq'),
            'edit_item'             => __('Edit FAQ', 'p5faq'),
            'update_item'           => __('Update FAQ', 'p5faq'),
            'view_item'             => __('View FAQ', 'p5faq'),
            'view_items'            => __('View FAQs', 'p5faq'),
            'search_items'          => __('Search FAQ', 'p5faq'),
            'not_found'             => __('Not found', 'p5faq'),
            'not_found_in_trash'    => __('Not found in Trash', 'p5faq'),
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
