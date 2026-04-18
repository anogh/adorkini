<?php
/**
 * Homepage Settings
 * Admin menu for managing homepage hero sections (Desktop & Mobile)
 */

if (!defined('ABSPATH')) {
    exit;
}

// Register Admin Menu
add_action('admin_menu', 'warafy_homepage_settings_menu');

function warafy_homepage_settings_menu() {
    add_theme_page(
        'Homepage Settings',
        'Homepage Settings',
        'manage_options',
        'warafy-homepage-settings',
        'warafy_homepage_settings_page'
    );
}

// Register Settings
add_action('admin_init', 'warafy_homepage_settings_init');

function warafy_homepage_settings_init() {
    // Slide 1 Settings
    register_setting('warafy_homepage_settings', 'warafy_hero_desktop_image');
    register_setting('warafy_homepage_settings', 'warafy_hero_title');
    register_setting('warafy_homepage_settings', 'warafy_hero_description');
    register_setting('warafy_homepage_settings', 'warafy_hero_button_text');
    register_setting('warafy_homepage_settings', 'warafy_hero_button_url');
    register_setting('warafy_homepage_settings', 'warafy_hero_mobile_image');
    register_setting('warafy_homepage_settings', 'warafy_hero_mobile_title');
    register_setting('warafy_homepage_settings', 'warafy_hero_mobile_description');
    register_setting('warafy_homepage_settings', 'warafy_hero_mobile_button_text');
    register_setting('warafy_homepage_settings', 'warafy_hero_title_bn');
    register_setting('warafy_homepage_settings', 'warafy_hero_description_bn');
    register_setting('warafy_homepage_settings', 'warafy_hero_button_text_bn');
    register_setting('warafy_homepage_settings', 'warafy_hero_mobile_title_bn');
    register_setting('warafy_homepage_settings', 'warafy_hero_mobile_description_bn');
    register_setting('warafy_homepage_settings', 'warafy_hero_mobile_button_text_bn');

    // Slide 2 Settings
    register_setting('warafy_homepage_settings', 'warafy_hero_desktop_image_2');
    register_setting('warafy_homepage_settings', 'warafy_hero_title_2');
    register_setting('warafy_homepage_settings', 'warafy_hero_description_2');
    register_setting('warafy_homepage_settings', 'warafy_hero_button_text_2');
    register_setting('warafy_homepage_settings', 'warafy_hero_button_url_2');
    register_setting('warafy_homepage_settings', 'warafy_hero_mobile_image_2');
    register_setting('warafy_homepage_settings', 'warafy_hero_mobile_title_2');
    register_setting('warafy_homepage_settings', 'warafy_hero_mobile_description_2');
    register_setting('warafy_homepage_settings', 'warafy_hero_mobile_button_text_2');
    register_setting('warafy_homepage_settings', 'warafy_hero_title_bn_2');
    register_setting('warafy_homepage_settings', 'warafy_hero_description_bn_2');
    register_setting('warafy_homepage_settings', 'warafy_hero_button_text_bn_2');
    register_setting('warafy_homepage_settings', 'warafy_hero_mobile_title_bn_2');
    register_setting('warafy_homepage_settings', 'warafy_hero_mobile_description_bn_2');
    register_setting('warafy_homepage_settings', 'warafy_hero_mobile_button_text_bn_2');

    // Slide 3 Settings
    register_setting('warafy_homepage_settings', 'warafy_hero_desktop_image_3');
    register_setting('warafy_homepage_settings', 'warafy_hero_title_3');
    register_setting('warafy_homepage_settings', 'warafy_hero_description_3');
    register_setting('warafy_homepage_settings', 'warafy_hero_button_text_3');
    register_setting('warafy_homepage_settings', 'warafy_hero_button_url_3');
    register_setting('warafy_homepage_settings', 'warafy_hero_mobile_image_3');
    register_setting('warafy_homepage_settings', 'warafy_hero_mobile_title_3');
    register_setting('warafy_homepage_settings', 'warafy_hero_mobile_description_3');
    register_setting('warafy_homepage_settings', 'warafy_hero_mobile_button_text_3');
    register_setting('warafy_homepage_settings', 'warafy_hero_title_bn_3');
    register_setting('warafy_homepage_settings', 'warafy_hero_description_bn_3');
    register_setting('warafy_homepage_settings', 'warafy_hero_button_text_bn_3');
    register_setting('warafy_homepage_settings', 'warafy_hero_mobile_title_bn_3');
    register_setting('warafy_homepage_settings', 'warafy_hero_mobile_description_bn_3');
    register_setting('warafy_homepage_settings', 'warafy_hero_mobile_button_text_bn_3');
}

// Enqueue Media Scripts
add_action('admin_enqueue_scripts', 'warafy_homepage_settings_scripts');

function warafy_homepage_settings_scripts($hook) {
    if ($hook !== 'appearance_page_warafy-homepage-settings') {
        return;
    }
    wp_enqueue_media();
}

// Render Page
function warafy_homepage_settings_page() {
    $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'slide1';
    ?>
    <div class="wrap">
        <h1>Homepage Settings</h1>
        
        <h2 class="nav-tab-wrapper">
            <a href="?page=warafy-homepage-settings&tab=slide1" class="nav-tab <?php echo $active_tab == 'slide1' ? 'nav-tab-active' : ''; ?>">Slide 1</a>
            <a href="?page=warafy-homepage-settings&tab=slide2" class="nav-tab <?php echo $active_tab == 'slide2' ? 'nav-tab-active' : ''; ?>">Slide 2</a>
            <a href="?page=warafy-homepage-settings&tab=slide3" class="nav-tab <?php echo $active_tab == 'slide3' ? 'nav-tab-active' : ''; ?>">Slide 3</a>
        </h2>

        <form method="post" action="options.php">
            <?php
            settings_fields('warafy_homepage_settings');
            do_settings_sections('warafy_homepage_settings');
            
            // Suffix for option names based on tab
            $s = '';
            if ($active_tab == 'slide2') $s = '_2';
            if ($active_tab == 'slide3') $s = '_3';
            
            // Defaults only for Slide 1
            $is_slide_1 = ($active_tab == 'slide1');
            $def_img = $is_slide_1 ? 'https://lh3.googleusercontent.com/aida-public/AB6AXuBQt_hNv9RJISICe6QmR94cZW3qkEA5JS5XEya0vVDuE6PczpKl1RV2_DCp0Aire2HzStJG74f44FC71rhQIE5i1JA4Z4i2CtFawU4Rsf1yfjJFCHy6oJx6rVaW0lRtVsoRSL0oEoTWYHNJieRZD6BtMbnGqXg7LKmuZ4f7NiCpk_ynULjVXdCo_lUTuxYWM_f2PjENgs6vmjCqBTYJxsU9Br-R7MnIjo-AHXeGJmdUaE7xvx8b1wq7jNB2kHXlWmMPTX30bfVZkQY' : '';
            $def_title = $is_slide_1 ? 'Summer Styles Are Here' : '';
            $def_desc = $is_slide_1 ? 'Discover the hottest trends of the season and refresh your wardrobe.' : '';
            $def_btn = $is_slide_1 ? 'Shop Collection' : 'Shop Now';
            $def_url = $is_slide_1 ? wc_get_page_permalink('shop') : '';
            $def_mob_title = $is_slide_1 ? 'Summer Styles' : '';
            $def_mob_desc = $is_slide_1 ? 'Discover the latest trends for the season.' : '';
            ?>
            
            <div class="card" style="max-width: 800px; padding: 20px; margin-top: 20px;">
                <h2 class="title">Desktop Hero Section (<?php echo ucfirst($active_tab); ?>)</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="warafy_hero_desktop_image<?php echo $s; ?>">Background Image</label></th>
                        <td>
                            <?php $desktop_image = get_option('warafy_hero_desktop_image'.$s, $def_img); ?>
                            <div class="image-preview-wrapper" style="margin-bottom: 10px;">
                                <img id="desktop-image-preview<?php echo $s; ?>" src="<?php echo esc_url($desktop_image); ?>" style="max-width: 100%; height: auto; max-height: 200px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); display: <?php echo empty($desktop_image) ? 'none' : 'block'; ?>;">
                            </div>
                            <input type="hidden" name="warafy_hero_desktop_image<?php echo $s; ?>" id="warafy_hero_desktop_image<?php echo $s; ?>" value="<?php echo esc_attr($desktop_image); ?>">
                            <button type="button" class="button button-secondary warafy-upload-btn" data-target="#warafy_hero_desktop_image<?php echo $s; ?>" data-preview="#desktop-image-preview<?php echo $s; ?>">Upload Image</button>
                            <p class="description">Recommended size: 1920x900px</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="warafy_hero_title<?php echo $s; ?>">Title</label></th>
                        <td>
                            <input type="text" name="warafy_hero_title<?php echo $s; ?>" id="warafy_hero_title<?php echo $s; ?>" value="<?php echo esc_attr(get_option('warafy_hero_title'.$s, $def_title)); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="warafy_hero_description<?php echo $s; ?>">Description</label></th>
                        <td>
                            <textarea name="warafy_hero_description<?php echo $s; ?>" id="warafy_hero_description<?php echo $s; ?>" rows="3" class="large-text"><?php echo esc_textarea(get_option('warafy_hero_description'.$s, $def_desc)); ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="warafy_hero_button_text<?php echo $s; ?>">Button Text</label></th>
                        <td>
                            <input type="text" name="warafy_hero_button_text<?php echo $s; ?>" id="warafy_hero_button_text<?php echo $s; ?>" value="<?php echo esc_attr(get_option('warafy_hero_button_text'.$s, $def_btn)); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="warafy_hero_button_url<?php echo $s; ?>">Button URL</label></th>
                        <td>
                            <input type="text" name="warafy_hero_button_url<?php echo $s; ?>" id="warafy_hero_button_url<?php echo $s; ?>" value="<?php echo esc_attr(get_option('warafy_hero_button_url'.$s, $def_url)); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="warafy_hero_title_bn<?php echo $s; ?>">Bengali Title</label></th>
                        <td>
                            <input type="text" name="warafy_hero_title_bn<?php echo $s; ?>" id="warafy_hero_title_bn<?php echo $s; ?>" value="<?php echo esc_attr(get_option('warafy_hero_title_bn'.$s)); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="warafy_hero_description_bn<?php echo $s; ?>">Bengali Description</label></th>
                        <td>
                            <textarea name="warafy_hero_description_bn<?php echo $s; ?>" id="warafy_hero_description_bn<?php echo $s; ?>" rows="3" class="large-text"><?php echo esc_textarea(get_option('warafy_hero_description_bn'.$s)); ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="warafy_hero_button_text_bn<?php echo $s; ?>">Bengali Button Text</label></th>
                        <td>
                            <input type="text" name="warafy_hero_button_text_bn<?php echo $s; ?>" id="warafy_hero_button_text_bn<?php echo $s; ?>" value="<?php echo esc_attr(get_option('warafy_hero_button_text_bn'.$s)); ?>" class="regular-text">
                        </td>
                    </tr>
                </table>
            </div>

            <div class="card" style="max-width: 800px; padding: 20px; margin-top: 20px;">
                <h2 class="title">Mobile Hero Section (<?php echo ucfirst($active_tab); ?>)</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="warafy_hero_mobile_image<?php echo $s; ?>">Background Image</label></th>
                        <td>
                            <?php $mobile_image = get_option('warafy_hero_mobile_image'.$s, $def_img); ?>
                            <div class="image-preview-wrapper" style="margin-bottom: 10px;">
                                <img id="mobile-image-preview<?php echo $s; ?>" src="<?php echo esc_url($mobile_image); ?>" style="max-width: 100%; height: auto; max-height: 200px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); display: <?php echo empty($mobile_image) ? 'none' : 'block'; ?>;">
                            </div>
                            <input type="hidden" name="warafy_hero_mobile_image<?php echo $s; ?>" id="warafy_hero_mobile_image<?php echo $s; ?>" value="<?php echo esc_attr($mobile_image); ?>">
                            <button type="button" class="button button-secondary warafy-upload-btn" data-target="#warafy_hero_mobile_image<?php echo $s; ?>" data-preview="#mobile-image-preview<?php echo $s; ?>">Upload Image</button>
                            <p class="description">If left empty, will use Desktop image (not recommended as it will be cropped). Recommended size: 800x800px or similar portrait ratio.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="warafy_hero_mobile_title<?php echo $s; ?>">Mobile Title</label></th>
                        <td>
                            <input type="text" name="warafy_hero_mobile_title<?php echo $s; ?>" id="warafy_hero_mobile_title<?php echo $s; ?>" value="<?php echo esc_attr(get_option('warafy_hero_mobile_title'.$s, $def_mob_title)); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="warafy_hero_mobile_description<?php echo $s; ?>">Mobile Description</label></th>
                        <td>
                            <textarea name="warafy_hero_mobile_description<?php echo $s; ?>" id="warafy_hero_mobile_description<?php echo $s; ?>" rows="3" class="large-text"><?php echo esc_textarea(get_option('warafy_hero_mobile_description'.$s, $def_mob_desc)); ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="warafy_hero_mobile_button_text<?php echo $s; ?>">Mobile Button Text</label></th>
                        <td>
                            <input type="text" name="warafy_hero_mobile_button_text<?php echo $s; ?>" id="warafy_hero_mobile_button_text<?php echo $s; ?>" value="<?php echo esc_attr(get_option('warafy_hero_mobile_button_text'.$s, $def_btn)); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="warafy_hero_mobile_title_bn<?php echo $s; ?>">Mobile Bengali Title</label></th>
                        <td>
                            <input type="text" name="warafy_hero_mobile_title_bn<?php echo $s; ?>" id="warafy_hero_mobile_title_bn<?php echo $s; ?>" value="<?php echo esc_attr(get_option('warafy_hero_mobile_title_bn'.$s)); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="warafy_hero_mobile_description_bn<?php echo $s; ?>">Mobile Bengali Description</label></th>
                        <td>
                            <textarea name="warafy_hero_mobile_description_bn<?php echo $s; ?>" id="warafy_hero_mobile_description_bn<?php echo $s; ?>" rows="3" class="large-text"><?php echo esc_textarea(get_option('warafy_hero_mobile_description_bn'.$s)); ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="warafy_hero_mobile_button_text_bn<?php echo $s; ?>">Mobile Bengali Button Text</label></th>
                        <td>
                            <input type="text" name="warafy_hero_mobile_button_text_bn<?php echo $s; ?>" id="warafy_hero_mobile_button_text_bn<?php echo $s; ?>" value="<?php echo esc_attr(get_option('warafy_hero_mobile_button_text_bn'.$s)); ?>" class="regular-text">
                        </td>
                    </tr>
                </table>
            </div>
            
            <?php submit_button(); ?>
        </form>
    </div>

    <!-- Media Uploader Script -->
    <script>
    jQuery(document).ready(function($){
        $('.warafy-upload-btn').click(function(e) {
            e.preventDefault();
            var button = $(this);
            var targetInput = $(button.data('target'));
            var targetPreview = $(button.data('preview'));
            
            var custom_uploader = wp.media({
                title: 'Select Image',
                button: {
                    text: 'Use this image'
                },
                multiple: false
            }).on('select', function() {
                var attachment = custom_uploader.state().get('selection').first().toJSON();
                targetInput.val(attachment.url);
                targetPreview.attr('src', attachment.url);
                targetPreview.show();
            }).open();
        });
    });
    </script>
    <?php
}
