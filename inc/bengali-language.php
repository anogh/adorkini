<?php
/**
 * Bengali Language Support
 * Product fields, category fields, language switching
 */

if (!defined('ABSPATH')) {
    exit;
}

// Add Bengali fields to product admin
add_action('woocommerce_product_options_general_product_data', 'warafy_add_bengali_product_fields');

function warafy_add_bengali_product_fields() {
    echo '<div class="options_group">';
    woocommerce_wp_text_input(array(
        'id' => '_bengali_title',
        'label' => __('Bengali Title', 'warafy-modern'),
        'placeholder' => __('Enter Bengali product title', 'warafy-modern'),
        'desc_tip' => true,
        'description' => __('Optional: Bengali translation of product title', 'warafy-modern'),
        'type' => 'text',
    ));
    echo '</div>';
    
    echo '<div class="options_group">';
    echo '<h4>' . __('Bengali Description', 'warafy-modern') . '</h4>';
    echo '<p class="description">' . __('Optional: Bengali translation of product description', 'warafy-modern') . '</p>';
    
    $bengali_description = get_post_meta(get_the_ID(), '_bengali_description', true);
    
    wp_editor(
        $bengali_description,
        '_bengali_description',
        array(
            'textarea_name' => '_bengali_description',
            'textarea_rows' => 10,
            'media_buttons' => true,
            'teeny' => false,
            'quicktags' => true,
            'editor_css' => '<style>#wp-_bengali_description-editor-container { border: 1px solid #ddd; margin-top: 10px; }</style>',
        )
    );
    echo '</div>';
    
    echo '<div class="options_group">';
    echo '<h4>' . __('Bengali Short Description', 'warafy-modern') . '</h4>';
    echo '<p class="description">' . __('Optional: Bengali translation of product short description', 'warafy-modern') . '</p>';
    
    $bengali_short_description = get_post_meta(get_the_ID(), '_bengali_short_description', true);
    
    wp_editor(
        $bengali_short_description,
        '_bengali_short_description',
        array(
            'textarea_name' => '_bengali_short_description',
            'textarea_rows' => 8,
            'media_buttons' => true,
            'teeny' => false,
            'quicktags' => true,
            'editor_css' => '<style>#wp-_bengali_short_description-editor-container { border: 1px solid #ddd; margin-top: 10px; }</style>',
        )
    );
    echo '</div>';
}

// Save Bengali product fields
add_action('woocommerce_process_product_meta', 'warafy_save_bengali_product_fields');

function warafy_save_bengali_product_fields($post_id) {
    $bengali_title = isset($_POST['_bengali_title']) ? sanitize_textarea_field($_POST['_bengali_title']) : '';
    $bengali_description = isset($_POST['_bengali_description']) ? wp_kses_post($_POST['_bengali_description']) : '';
    $bengali_short_description = isset($_POST['_bengali_short_description']) ? wp_kses_post($_POST['_bengali_short_description']) : '';
    
    update_post_meta($post_id, '_bengali_title', $bengali_title);
    update_post_meta($post_id, '_bengali_description', $bengali_description);
    update_post_meta($post_id, '_bengali_short_description', $bengali_short_description);
}

// Add Bengali fields to product category admin
add_action('product_cat_add_form_fields', 'warafy_add_bengali_category_fields', 10, 2);
add_action('product_cat_edit_form_fields', 'warafy_add_bengali_category_fields', 10, 2);

function warafy_add_bengali_category_fields($term, $taxonomy = null) {
    $term_id = isset($term->term_id) ? $term->term_id : 0;
    $bengali_name = $term_id ? get_term_meta($term_id, '_bengali_name', true) : '';
    $bengali_description = $term_id ? get_term_meta($term_id, '_bengali_description', true) : '';
    
    ?>
    <div class="form-field term-bengali-name-wrap">
        <label for="bengali_name"><?php _e('Bengali Name', 'warafy-modern'); ?></label>
        <input type="text" id="bengali_name" name="bengali_name" value="<?php echo esc_attr($bengali_name); ?>" placeholder="<?php _e('Enter Bengali category name', 'warafy-modern'); ?>">
        <p class="description"><?php _e('Optional: Bengali translation of category name', 'warafy-modern'); ?></p>
    </div>
    
    <div class="form-field term-bengali-description-wrap">
        <label for="bengali_description"><?php _e('Bengali Description', 'warafy-modern'); ?></label>
        <textarea id="bengali_description" name="bengali_description" rows="5" placeholder="<?php _e('Enter Bengali category description', 'warafy-modern'); ?>"><?php echo esc_textarea($bengali_description); ?></textarea>
        <p class="description"><?php _e('Optional: Bengali translation of category description', 'warafy-modern'); ?></p>
    </div>
    <?php
}

// Save Bengali category fields
add_action('created_product_cat', 'warafy_save_bengali_category_fields');
add_action('edited_product_cat', 'warafy_save_bengali_category_fields');

function warafy_save_bengali_category_fields($term_id) {
    if (isset($_POST['bengali_name'])) {
        update_term_meta($term_id, '_bengali_name', sanitize_text_field($_POST['bengali_name']));
    }
    if (isset($_POST['bengali_description'])) {
        update_term_meta($term_id, '_bengali_description', sanitize_textarea_field($_POST['bengali_description']));
    }
}

// Helper function to get Bengali content with fallback
function warafy_get_bengali_content($post_id, $field, $default = '') {
    $bengali_value = get_post_meta($post_id, '_bengali_' . $field, true);
    return !empty($bengali_value) ? $bengali_value : $default;
}

// Helper function to get Bengali category content with fallback
function warafy_get_bengali_category_content($term_id, $field, $default = '') {
    $bengali_value = get_term_meta($term_id, '_bengali_' . $field, true);
    return !empty($bengali_value) ? $bengali_value : $default;
}

// Language switching functionality
add_action('wp_head', 'warafy_language_switcher_script');

function warafy_language_switcher_script() {
    ?>
    <script>
    function warafy_setLanguage(lang) {
        localStorage.setItem('warafy_language', lang);
        document.cookie = "warafy_language=" + lang + "; path=/; max-age=" + (60*60*24*365);
        window.location.reload();
    }
    
    function warafy_getLanguage() {
        let lang = localStorage.getItem('warafy_language');
        if (!lang) {
            const match = document.cookie.match(new RegExp('(^| )warafy_language=([^;]+)'));
            lang = match ? match[2] : 'en';
        }
        return lang;
    }
    
    function warafy_updateLanguage(lang) {
        document.querySelectorAll('.warafy-language-toggle').forEach(toggle => {
            const theme = toggle.getAttribute('data-theme') || 'dark';
            const bnSpan = toggle.querySelector('.warafy-lang-bn');
            const enSpan = toggle.querySelector('.warafy-lang-en');

            if (!bnSpan || !enSpan) return;

            // Define active color
            const activeClass = 'text-[#FFB800]';

            // Define inactive classes based on theme
            const inactiveClasses = theme === 'light'
                ? ['text-gray-900', 'dark:text-white']
                : ['text-white'];

            // Reset both spans first
            bnSpan.classList.remove(activeClass);
            bnSpan.classList.remove(...inactiveClasses);
            enSpan.classList.remove(activeClass);
            enSpan.classList.remove(...inactiveClasses);

            // Apply classes based on language
            if (lang === 'bn') {
                bnSpan.classList.add(activeClass);
                enSpan.classList.add(...inactiveClasses);
            } else {
                bnSpan.classList.add(...inactiveClasses);
                enSpan.classList.add(activeClass);
            }
        });
        
        document.querySelectorAll('.warafy-translatable').forEach(element => {
            const bengaliText = element.getAttribute('data-bn');
            const englishText = element.getAttribute('data-en');
            
            if (lang === 'bn' && bengaliText) {
                element.innerHTML = bengaliText;
            } else if (lang === 'en' && englishText) {
                element.innerHTML = englishText;
            }
        });
        
        if (lang === 'bn') {
            document.querySelectorAll('.category-title, .woocommerce-loop-category__title, .term-name').forEach(element => {
                const categoryUrl = element.closest('a')?.href;
                if (categoryUrl && categoryUrl.includes('product-category')) {
                    const slug = categoryUrl.split('/').filter(Boolean).pop();
                    if (slug) {
                        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: 'action=warafy_get_bengali_category&category_slug=' + slug + '&nonce=<?php echo wp_create_nonce('warafy_bengali_nonce'); ?>'
                        }).then(response => response.json()).then(data => {
                            if (data.success && data.data.bengali_name) {
                                element.setAttribute('data-en', element.textContent);
                                element.setAttribute('data-bn', data.data.bengali_name);
                                element.textContent = data.data.bengali_name;
                            }
                        });
                    }
                }
            });
        } else {
            document.querySelectorAll('[data-en].category-title, [data-en].woocommerce-loop-category__title, [data-en].term-name').forEach(element => {
                const englishText = element.getAttribute('data-en');
                if (englishText) {
                    element.textContent = englishText;
                }
            });
        }
        
        document.body.classList.toggle('bengali-mode', lang === 'bn');
        window.dispatchEvent(new CustomEvent('languageChanged', { detail: { language: lang } }));
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        const currentLang = warafy_getLanguage();
        warafy_updateLanguage(currentLang);
        
        document.querySelectorAll('.warafy-language-toggle').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const lang = warafy_getLanguage();
                warafy_setLanguage(lang === 'en' ? 'bn' : 'en');
            });
        });
    });
    </script>
    <?php
}

// AJAX handler for Bengali content
add_action('wp_ajax_warafy_get_bengali_content', 'warafy_get_bengali_content_ajax');
add_action('wp_ajax_nopriv_warafy_get_bengali_content', 'warafy_get_bengali_content_ajax');

// AJAX handler for Bengali category data
add_action('wp_ajax_warafy_get_bengali_category', 'warafy_get_bengali_category_ajax');
add_action('wp_ajax_nopriv_warafy_get_bengali_category', 'warafy_get_bengali_category_ajax');

function warafy_get_bengali_category_ajax() {
    if (!wp_verify_nonce($_POST['nonce'], 'warafy_bengali_nonce')) {
        wp_send_json_error('Invalid nonce');
    }
    
    $category_slug = sanitize_text_field($_POST['category_slug']);
    $term = get_term_by('slug', $category_slug, 'product_cat');
    
    if ($term && !is_wp_error($term)) {
        $bengali_name = get_term_meta($term->term_id, '_bengali_name', true);
        $bengali_description = get_term_meta($term->term_id, '_bengali_description', true);
        
        wp_send_json_success([
            'bengali_name' => $bengali_name,
            'bengali_description' => $bengali_description
        ]);
    } else {
        wp_send_json_error('Category not found');
    }
}

function warafy_get_bengali_content_ajax() {
    if (!wp_verify_nonce($_POST['nonce'], 'warafy_bengali_nonce')) {
        wp_send_json_error('Invalid nonce');
    }
    
    $product_id = intval($_POST['product_id']);
    $content_type = sanitize_text_field($_POST['content_type']);
    
    $response = [];
    
    switch ($content_type) {
        case 'title':
            $response['bengali_title'] = get_post_meta($product_id, '_bengali_title', true);
            break;
        case 'description':
            $response['bengali_description'] = get_post_meta($product_id, '_bengali_description', true);
            break;
        case 'short_description':
            $response['bengali_short_description'] = get_post_meta($product_id, '_bengali_short_description', true);
            break;
    }
    
    wp_send_json_success($response);
}

// Filter product title for Bengali display
add_filter('the_title', 'warafy_filter_product_title', 10, 2);

function warafy_filter_product_title($title, $id = null) {
    if (is_admin()) {
        return $title;
    }
    
    if ($id && get_post_type($id) === 'product') {
        $bengali_title = get_post_meta($id, '_bengali_title', true);
        if (!empty($bengali_title)) {
            return '<span class="warafy-translatable" data-en="' . esc_attr($title) . '" data-bn="' . esc_attr($bengali_title) . '">' . $title . '</span>';
        }
    }
    
    return $title;
}

// Filter product content for Bengali display
add_filter('woocommerce_product_short_description', 'warafy_filter_product_short_description');
add_filter('the_content', 'warafy_filter_product_content');

function warafy_filter_product_short_description($content) {
    if (!is_admin() && function_exists('get_the_ID') && get_the_ID()) {
        $post_id = get_the_ID();
        $bengali_content = get_post_meta($post_id, '_bengali_short_description', true);
        if (!empty($bengali_content)) {
            return '<div class="warafy-translatable" data-en="' . esc_attr($content) . '" data-bn="' . esc_attr($bengali_content) . '">' . $content . '</div>';
        }
    }
    return $content;
}

function warafy_filter_product_content($content) {
    if (!is_admin() && function_exists('get_the_ID') && get_the_ID() && function_exists('get_post_type') && get_post_type(get_the_ID()) === 'product') {
        $post_id = get_the_ID();
        $bengali_content = get_post_meta($post_id, '_bengali_description', true);
        if (!empty($bengali_content)) {
            return '<div class="warafy-translatable" data-en="' . esc_attr($content) . '" data-bn="' . esc_attr($bengali_content) . '">' . $content . '</div>';
        }
    }
    return $content;
}

// Translation System
function warafy_get_translations() {
    $json_file = get_template_directory() . '/translations.json';
    if (file_exists($json_file)) {
        $json_content = file_get_contents($json_file);
        return json_decode($json_content, true);
    }
    return [];
}

function __t($text) {
    $lang = isset($_COOKIE['warafy_language']) ? $_COOKIE['warafy_language'] : 'en';
    
    if ($lang === 'bn') {
        $translations = warafy_get_translations();
        if (isset($translations[$text]) && !empty($translations[$text])) {
            return $translations[$text];
        }
    }
    
    return $text;
}

function warafy_is_bengali() {
    $lang = isset($_COOKIE['warafy_language']) ? $_COOKIE['warafy_language'] : 'en';
    return $lang === 'bn';
}

// Handle language switch via GET parameter
add_action('init', 'warafy_handle_language_switch');
function warafy_handle_language_switch() {
    if (isset($_GET['lang'])) {
        $lang = sanitize_text_field($_GET['lang']);
        if (in_array($lang, ['en', 'bn'])) {
            setcookie('warafy_language', $lang, time() + 365 * 24 * 60 * 60, '/');
            $_COOKIE['warafy_language'] = $lang;
            
            $redirect_url = remove_query_arg('lang');
            wp_redirect($redirect_url);
            exit;
        }
    }
}
