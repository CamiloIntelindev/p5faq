<?php
/**
 * Plugin Name: P5 FAQ
 * Plugin URI: https://p5.com
 * Description: Plugin para gestionar FAQs con campos repetidores y shortcode para insertar en cualquier sección
 * Version: 1.0.0
 * Author: P5
 * Author URI: https://p5.com
 * License: GPL2
 * Text Domain: p5faq
 */

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Definir constantes
define('P5FAQ_VERSION', '1.0.0');
define('P5FAQ_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('P5FAQ_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Registrar Custom Post Type FAQ
 */
function p5faq_register_post_type() {
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
        'description'           => __('Preguntas Frecuentes', 'p5faq'),
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
add_action('init', 'p5faq_register_post_type', 0);

/**
 * Agregar Meta Box para preguntas y respuestas
 */
function p5faq_add_meta_boxes() {
    add_meta_box(
        'p5faq_questions',
        __('Preguntas y Respuestas', 'p5faq'),
        'p5faq_meta_box_callback',
        'faq',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'p5faq_add_meta_boxes');

/**
 * Renderizar Meta Box
 */
function p5faq_meta_box_callback($post) {
    wp_nonce_field('p5faq_save_meta_box', 'p5faq_nonce');
    
    $faq_items = get_post_meta($post->ID, '_p5faq_items', true);
    if (!is_array($faq_items)) {
        $faq_items = array();
    }
    ?>
    <div id="p5faq-container">
        <div id="p5faq-items">
            <?php
            if (!empty($faq_items)) {
                foreach ($faq_items as $index => $item) {
                    p5faq_render_item($index, $item);
                }
            }
            ?>
        </div>
        <button type="button" class="button button-primary" id="p5faq-add-item">
            <?php _e('Agregar Pregunta', 'p5faq'); ?>
        </button>
    </div>
    
    <script type="text/template" id="p5faq-item-template">
        <?php p5faq_render_item('{{INDEX}}', array('question' => '', 'answer' => '')); ?>
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
 * Renderizar un item individual de FAQ
 */
function p5faq_render_item($index, $item) {
    $question = isset($item['question']) ? esc_attr($item['question']) : '';
    $answer = isset($item['answer']) ? esc_textarea($item['answer']) : '';
    ?>
    <div class="p5faq-item" data-index="<?php echo $index; ?>">
        <div class="p5faq-item-header">
            <h4><?php printf(__('Pregunta #%s', 'p5faq'), '<span class="p5faq-number">' . ($index + 1) . '</span>'); ?></h4>
            <a href="#" class="p5faq-remove" data-index="<?php echo $index; ?>">
                <span class="dashicons dashicons-trash"></span> <?php _e('Eliminar', 'p5faq'); ?>
            </a>
        </div>
        
        <div class="p5faq-field">
            <label><?php _e('Pregunta:', 'p5faq'); ?></label>
            <input type="text" 
                   name="p5faq_items[<?php echo $index; ?>][question]" 
                   value="<?php echo $question; ?>" 
                   placeholder="<?php _e('Escribe la pregunta aquí', 'p5faq'); ?>">
        </div>
        
        <div class="p5faq-field">
            <label><?php _e('Respuesta:', 'p5faq'); ?></label>
            <textarea name="p5faq_items[<?php echo $index; ?>][answer]" 
                      placeholder="<?php _e('Escribe la respuesta aquí', 'p5faq'); ?>"><?php echo $answer; ?></textarea>
        </div>
    </div>
    <?php
}

/**
 * Guardar Meta Box
 */
function p5faq_save_meta_box($post_id) {
    // Verificar nonce
    if (!isset($_POST['p5faq_nonce']) || !wp_verify_nonce($_POST['p5faq_nonce'], 'p5faq_save_meta_box')) {
        return;
    }

    // Verificar autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Verificar permisos
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Guardar datos
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
add_action('save_post_faq', 'p5faq_save_meta_box');

/**
 * Enqueue scripts en admin
 */
function p5faq_admin_scripts($hook) {
    global $post_type;
    
    if (('post.php' === $hook || 'post-new.php' === $hook) && 'faq' === $post_type) {
        wp_enqueue_script('p5faq-admin', P5FAQ_PLUGIN_URL . 'assets/admin.js', array('jquery'), P5FAQ_VERSION, true);
    }
}
add_action('admin_enqueue_scripts', 'p5faq_admin_scripts');

/**
 * Registrar shortcode [faq id="123"]
 */
// Almacén global para esquemas acumulados y evitar duplicados
global $p5faq_schemas;
$p5faq_schemas = array();

function p5faq_shortcode($atts) {
    $atts = shortcode_atts(array(
        'id' => 0,
        'schema' => 'true',
        'title' => 'true',
    ), $atts);

    $post_id = intval($atts['id']);
    
    if (!$post_id || get_post_type($post_id) !== 'faq') {
        return '<p>' . __('FAQ no encontrado.', 'p5faq') . '</p>';
    }

    $faq_items = get_post_meta($post_id, '_p5faq_items', true);
    
    if (empty($faq_items) || !is_array($faq_items)) {
        return '<p>' . __('No hay preguntas disponibles.', 'p5faq') . '</p>';
    }

    $output = '<div class="p5faq-wrapper" data-faq-id="' . $post_id . '">';
    $output .= '<div class="p5faq-list">';
    
    // Construir datos para Schema.org FAQPage
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

        // Agregar al JSON-LD
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

    // Acumular JSON-LD para impresión en footer si schema != false
    $include_schema = filter_var($atts['schema'], FILTER_VALIDATE_BOOLEAN);
    if ($include_schema && !empty($schema['mainEntity'])) {
        // Opcional: incluir el título del CPT en el JSON-LD
        $include_title = filter_var($atts['title'], FILTER_VALIDATE_BOOLEAN);
        if ($include_title) {
            $schema['name'] = wp_strip_all_tags(get_the_title($post_id));
            // Alternativamente podríamos usar 'headline'
            // $schema['headline'] = wp_strip_all_tags(get_the_title($post_id));
        }
        $key = 'faq_' . $post_id;
        if (!isset($GLOBALS['p5faq_schemas'])) {
            $GLOBALS['p5faq_schemas'] = array();
        }
        // Evitar duplicados por ID
        $GLOBALS['p5faq_schemas'][$key] = $schema;
        // Asegurar hook para imprimir en footer solo una vez
        if (!has_action('wp_footer', 'p5faq_print_schemas')) {
            add_action('wp_footer', 'p5faq_print_schemas', 5);
        }
    }
    
    return $output;
}
add_shortcode('faq', 'p5faq_shortcode');

/**
 * Imprime todos los JSON-LD acumulados en el footer
 */
function p5faq_print_schemas() {
    if (empty($GLOBALS['p5faq_schemas']) || !is_array($GLOBALS['p5faq_schemas'])) {
        return;
    }
    foreach ($GLOBALS['p5faq_schemas'] as $schema) {
        if (empty($schema['mainEntity'])) {
            continue;
        }
        $json = wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        echo '<script type="application/ld+json">' . $json . '</script>';
    }
}

/**
 * Enqueue scripts y estilos en frontend
 */
function p5faq_enqueue_scripts() {
    wp_enqueue_style('p5faq-style', P5FAQ_PLUGIN_URL . 'assets/style.css', array(), P5FAQ_VERSION);
    wp_enqueue_script('p5faq-script', P5FAQ_PLUGIN_URL . 'assets/script.js', array('jquery'), P5FAQ_VERSION, true);
}
add_action('wp_enqueue_scripts', 'p5faq_enqueue_scripts');
