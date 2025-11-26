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
     * Render the meta box
     */
    public function render_meta_box($post) {
        wp_nonce_field('p5faq_save_meta_box', 'p5faq_nonce');
        
        $faq_items = get_post_meta($post->ID, '_p5faq_items', true);
        if (!is_array($faq_items)) {
            $faq_items = array();
        }
        
        // Debug info
        $script_url = plugin_dir_url(dirname(__FILE__)) . 'assets/admin.js';
        ?>
        <!-- DEBUG: Script URL = <?php echo esc_html($script_url); ?> -->
        <div id="p5faq-container">
            <div id="p5faq-items">
                <?php
                if (!empty($faq_items)) {
                    foreach ($faq_items as $index => $item) {
                        $this->render_faq_item($index, $item);
                    }
                } else {
                    // Render one empty item by default
                    $this->render_faq_item(0, array('question' => '', 'answer' => ''));
                }
                ?>
            </div>
            <button type="button" class="button button-primary" id="p5faq-add-item">
                <?php _e('Add Question', 'p5faq'); ?>
            </button>
        </div>
        
        <script type="text/template" id="p5faq-item-template">
            <?php $this->render_faq_item('{{INDEX}}', array('question' => '', 'answer' => '')); ?>
        </script>
        
        <style>
            .p5faq-item {
                background: #f9f9f9;
                border: 1px solid #ddd;
                padding: 15px;
                margin-bottom: 15px;
                position: relative;
            }
            .p5faq-item-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 10px;
            }
            .p5faq-item-header h4 {
                margin: 0;
            }
            .p5faq-remove {
                color: #a00;
                cursor: pointer;
                text-decoration: none;
            }
            .p5faq-remove:hover {
                color: #dc3232;
            }
            .p5faq-field {
                margin-bottom: 10px;
            }
            .p5faq-field label {
                display: block;
                font-weight: bold;
                margin-bottom: 5px;
            }
            .p5faq-field input,
            .p5faq-field textarea {
                width: 100%;
            }
            .p5faq-field textarea {
                min-height: 100px;
            }
        </style>
        <?php
    }

    /**
     * Render a single FAQ item
     */
    private function render_faq_item($index, $item) {
        $question = isset($item['question']) ? esc_attr($item['question']) : '';
        $answer = isset($item['answer']) ? esc_textarea($item['answer']) : '';
        ?>
        <div class="p5faq-item" data-index="<?php echo $index; ?>">
            <div class="p5faq-item-header">
                <h4><?php printf(__('Question #%s', 'p5faq'), '<span class="p5faq-number">' . ($index + 1) . '</span>'); ?></h4>
                <a href="#" class="p5faq-remove" data-index="<?php echo $index; ?>">
                    <span class="dashicons dashicons-trash"></span> <?php _e('Remove', 'p5faq'); ?>
                </a>
            </div>
            
            <div class="p5faq-field">
                <label><?php _e('Question:', 'p5faq'); ?></label>
                <input type="text" 
                       name="p5faq_items[<?php echo $index; ?>][question]" 
                       value="<?php echo $question; ?>" 
                       placeholder="<?php _e('Write the question here', 'p5faq'); ?>">
            </div>
            
            <div class="p5faq-field">
                <label><?php _e('Answer:', 'p5faq'); ?></label>
                <textarea name="p5faq_items[<?php echo $index; ?>][answer]" 
                          placeholder="<?php _e('Write the answer here', 'p5faq'); ?>"><?php echo $answer; ?></textarea>
            </div>
        </div>
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

    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_scripts($hook) {
        // Get current screen
        $screen = get_current_screen();
        
        // Debug: log the hook and screen
        if ($screen) {
            error_log('P5FAQ Debug - Hook: ' . $hook . ', Screen ID: ' . $screen->id . ', Post Type: ' . $screen->post_type);
        }
        
        // Only load on FAQ post edit screens
        if (('post.php' === $hook || 'post-new.php' === $hook) && $screen && 'faq' === $screen->post_type) {
            // Enqueue dashicons
            wp_enqueue_style('dashicons');
            
            $script_url = plugin_dir_url(dirname(__FILE__)) . 'assets/admin.js';
            error_log('P5FAQ Debug - Enqueuing script: ' . $script_url);
            
            wp_enqueue_script(
                $this->plugin_name . '-admin',
                $script_url,
                array('jquery'),
                $this->version,
                true
            );
        }
    }
}
