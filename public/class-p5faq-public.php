<?php
/**
 * The public-facing functionality of the plugin
 *
 * @package    P5FAQ
 * @subpackage P5FAQ/public
 */

class P5FAQ_Public {

    /**
     * The ID of this plugin
     */
    private $plugin_name;

    /**
     * The version of this plugin
     */
    private $version;

    /**
     * Global store for schemas
     */
    private $schemas = array();

    /**
     * Initialize the class and set its properties
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;

        add_shortcode('faq', array($this, 'shortcode'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    /**
     * Enqueue frontend scripts and styles
     */
    public function enqueue_scripts() {
        wp_enqueue_style(
            $this->plugin_name . '-public',
            plugin_dir_url(dirname(__FILE__)) . 'assets/style.css',
            array(),
            $this->version
        );
        
        wp_enqueue_script(
            $this->plugin_name . '-public',
            plugin_dir_url(dirname(__FILE__)) . 'assets/script.js',
            array('jquery'),
            $this->version,
            true
        );
    }

    /**
     * FAQ Shortcode handler
     */
    public function shortcode($atts) {
        $atts = shortcode_atts(array(
            'id' => 0,
            'schema' => 'true',
            'title' => 'true',
        ), $atts);

        $post_id = intval($atts['id']);
        
        if (!$post_id || get_post_type($post_id) !== 'faq') {
            return '<p>' . __('FAQ not found.', 'p5faq') . '</p>';
        }

        $faq_items = get_post_meta($post_id, '_p5faq_items', true);
        
        if (empty($faq_items) || !is_array($faq_items)) {
            return '<p>' . __('No questions available.', 'p5faq') . '</p>';
        }

        $output = '<div class="p5faq-wrapper" data-faq-id="' . $post_id . '">';
        $output .= '<div class="p5faq-list">';
        
        // Build Schema.org FAQPage data
        $schema = array(
            '@context'   => 'https://schema.org',
            '@type'      => 'FAQPage',
            'mainEntity' => array(),
        );

        foreach ($faq_items as $index => $item) {
            if (empty($item['question'])) {
                continue;
            }
            
            $output .= '<div class="p5faq-item" data-index="' . $index . '">';
            $output .= '<div class="p5faq-question">';
            $output .= '<h3 class="p5faq-question-title">' . esc_html($item['question']) . '</h3>';
            $output .= '<span class="p5faq-toggle">+</span>';
            $output .= '</div>';
            $output .= '<div class="p5faq-answer" style="display: none;">';
            $output .= wpautop($item['answer']);
            $output .= '</div>';
            $output .= '</div>';

            // Add to JSON-LD
            $schema['mainEntity'][] = array(
                '@type' => 'Question',
                'name'  => wp_strip_all_tags($item['question']),
                'acceptedAnswer' => array(
                    '@type' => 'Answer',
                    'text'  => wp_strip_all_tags($item['answer'])
                ),
            );
        }
        
        $output .= '</div>';
        $output .= '</div>';

        // Accumulate JSON-LD for footer printing if schema != false
        $include_schema = filter_var($atts['schema'], FILTER_VALIDATE_BOOLEAN);
        if ($include_schema && !empty($schema['mainEntity'])) {
            // Optional: include CPT title in JSON-LD
            $include_title = filter_var($atts['title'], FILTER_VALIDATE_BOOLEAN);
            if ($include_title) {
                $schema['name'] = wp_strip_all_tags(get_the_title($post_id));
            }
            
            $key = 'faq_' . $post_id;
            $this->schemas[$key] = $schema;
            
            // Ensure hook for footer printing only once
            if (!has_action('wp_footer', array($this, 'print_schemas'))) {
                add_action('wp_footer', array($this, 'print_schemas'), 5);
            }
        }
        
        return $output;
    }

    /**
     * Print all accumulated JSON-LD schemas in footer
     */
    public function print_schemas() {
        if (empty($this->schemas) || !is_array($this->schemas)) {
            return;
        }
        
        foreach ($this->schemas as $schema) {
            if (empty($schema['mainEntity'])) {
                continue;
            }
            $json = wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            echo '<script type="application/ld+json">' . $json . '</script>';
        }
    }
}
