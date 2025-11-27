<?php
/**
 * The admin-specific functionality of the plugin
 *
 * @package    P5FAQ
 * @subpackage P5FAQ/admin
 */

class P5FAQ_Admin {

    /**
     * The ID of this plugin
     */
    private $plugin_name;

    /**
     * The version of this plugin
     */
    private $version;

    /**
     * Initialize the class and set its properties
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;

        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post_faq', array($this, 'save_meta_box'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        // Register admin list table columns for CPT faq
        add_action('admin_init', array($this, 'register_list_table_columns'));
    }

    /**
     * Enqueue admin scripts
     */
    public function enqueue_scripts($hook) {
        global $post;
        
        if ($hook === 'post-new.php' || $hook === 'post.php') {
            if (isset($post) && 'faq' === $post->post_type) {
                wp_enqueue_script(
                    'p5faq-admin',
                    plugin_dir_url(dirname(__FILE__)) . 'assets/admin.js',
                    array('jquery'),
                    $this->version,
                    true
                );
            }
        }
    }

    /**
     * Add meta boxes
     */
    public function add_meta_boxes() {
        add_meta_box(
            'p5faq_questions',
            __('Questions & Answers', 'p5faq'),
            array($this, 'render_meta_box'),
            'faq',
            'normal',
            'high'
        );
    }

    /**
     * Add shortcode column to FAQ list table
     */
    public function register_list_table_columns() {
        add_filter('manage_edit-faq_columns', function($columns) {
            $columns['p5faq_shortcode'] = __('Shortcode', 'p5faq');
            return $columns;
        });

        add_action('manage_faq_posts_custom_column', function($column, $post_id) {
            if ($column === 'p5faq_shortcode') {
                $shortcode = '[faq id="' . intval($post_id) . '" schema="false" title="false"]';
                echo '<code>' . esc_html($shortcode) . '</code>';
            }
        }, 10, 2);
    }

    /**
     * Render the meta box
     */
    public function render_meta_box($post) {
        wp_nonce_field('p5faq_save_meta_box', 'p5faq_nonce');
        
        $faq_items = get_post_meta($post->ID, '_p5faq_items', true);
        if (!is_array($faq_items) || empty($faq_items)) {
            $faq_items = array(array('question' => '', 'answer' => ''));
        }
        ?>
        <div id="p5faq-container">
            <?php foreach ($faq_items as $index => $item): ?>
            <div class="p5faq-item">
                <input type="text" 
                       question-index-id="<?php echo esc_attr($index); ?>" 
                       name="p5faq_items[<?php echo esc_attr($index); ?>][question]"
                       class="question full-width" 
                       placeholder="<?php _e('Enter question here', 'p5faq'); ?>"
                       value="<?php echo esc_attr($item['question']); ?>">
                <input type="text" 
                       answer-index-id="<?php echo esc_attr($index); ?>"
                       name="p5faq_items[<?php echo esc_attr($index); ?>][answer]"
                       class="answer full-width" 
                       placeholder="<?php _e('Enter answer here', 'p5faq'); ?>"
                       value="<?php echo esc_attr($item['answer']); ?>">
                <input type="button" 
                       class="remove-faq-item button button-secondary" 
                       value="<?php _e('Remove', 'p5faq'); ?>">
            </div>
            <?php endforeach; ?>
            <button type="button" class="button button-primary" id="p5faq-add-item">
                <?php _e('Add Question', 'p5faq'); ?>
            </button>
        </div>
        <style>
            .p5faq-item{
                display: flex;
                flex-direction: column;
                gap: 15px;
                flex-wrap: wrap;
                align-items: end;
                padding: 20px;
                border: solid 1px #d2c2c2;
                border-radius: 10px;
                margin-bottom: 20px;
            }
            .p5faq-item > input[type="text"] {
                padding: 8px;
                font-size: 14px;
                width: 100%;
            }
            .p5faq-item > input[type="button"] {
                width: fit-content;
            }
        </style>
        <?php
    }

    /**
     * Save meta box data
     */
    public function save_meta_box($post_id) {
        // Verify nonce
        if (!isset($_POST['p5faq_nonce']) || !wp_verify_nonce($_POST['p5faq_nonce'], 'p5faq_save_meta_box')) {
            return;
        }

        // Check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Check permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Save data
        if (isset($_POST['p5faq_items'])) {
            $faq_items = array();
            foreach ($_POST['p5faq_items'] as $item) {
                if (!empty($item['question']) || !empty($item['answer'])) {
                    $faq_items[] = array(
                        'question' => sanitize_text_field($item['question']),
                        'answer'   => wp_kses_post($item['answer'])
                    );
                }
            }
            update_post_meta($post_id, '_p5faq_items', $faq_items);
        } else {
            delete_post_meta($post_id, '_p5faq_items');
        }
    }

}
