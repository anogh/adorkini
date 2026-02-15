<?php
/**
 * Logo Settings
 * Admin menu for managing site logo URL and size multiplier
 */

if (!defined('ABSPATH')) {
    exit;
}

// Default logo URL (the original hardcoded one)
define('WARAFY_DEFAULT_LOGO_URL', '/wp-content/uploads/2026/02/Weixin-Image_20260127091047_649_135.jpg');

// Register Admin Menu
add_action('admin_menu', 'warafy_logo_settings_menu');

function warafy_logo_settings_menu() {
    add_theme_page(
        'Logo Settings',
        'Logo Settings',
        'manage_options',
        'warafy-logo-settings',
        'warafy_logo_settings_page'
    );
}

// Register Settings
add_action('admin_init', 'warafy_logo_settings_init');

function warafy_logo_settings_init() {
    register_setting('warafy_logo_settings', 'warafy_logo_url', array(
        'sanitize_callback' => 'esc_url_raw',
        'default' => '',
    ));
    register_setting('warafy_logo_settings', 'warafy_logo_multiplier', array(
        'sanitize_callback' => 'warafy_sanitize_logo_multiplier',
        'default' => '1.0',
    ));
}

function warafy_sanitize_logo_multiplier($value) {
    $allowed = array('0.5','0.6','0.7','0.8','0.9','1.0','1.1','1.2','1.3','1.4','1.5','1.6','1.7','1.8','1.9','2.0');
    return in_array($value, $allowed) ? $value : '1.0';
}

/**
 * Helper to get the logo URL (used in header.php)
 */
function warafy_get_logo_url() {
    $url = get_option('warafy_logo_url', '');
    if (empty($url)) {
        return home_url(WARAFY_DEFAULT_LOGO_URL);
    }
    return $url;
}

/**
 * Helper to get the logo size multiplier
 */
function warafy_get_logo_multiplier() {
    return floatval(get_option('warafy_logo_multiplier', '1.0'));
}

// Render Page
function warafy_logo_settings_page() {
    $logo_url = get_option('warafy_logo_url', '');
    $display_url = !empty($logo_url) ? $logo_url : home_url(WARAFY_DEFAULT_LOGO_URL);
    $multiplier = get_option('warafy_logo_multiplier', '1.0');
    $multiplier_options = array('0.5','0.6','0.7','0.8','0.9','1.0','1.1','1.2','1.3','1.4','1.5','1.6','1.7','1.8','1.9','2.0');
    ?>
    <div class="wrap">
        <h1>Logo Settings</h1>
        <p>Manage your site logo image URL and size multiplier. Changes apply to all header logos, preloader, and favicons across the site.</p>

        <form method="post" action="options.php">
            <?php
            settings_fields('warafy_logo_settings');
            do_settings_sections('warafy_logo_settings');
            ?>

            <div class="card" style="max-width: 800px; padding: 20px; margin-top: 20px;">
                <h2 class="title">Logo Image</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="warafy_logo_url">Logo Image URL</label></th>
                        <td>
                            <input type="url" name="warafy_logo_url" id="warafy_logo_url" value="<?php echo esc_attr($logo_url); ?>" class="large-text" placeholder="<?php echo esc_attr(home_url(WARAFY_DEFAULT_LOGO_URL)); ?>">
                            <p class="description">Paste the full URL of your logo image. Leave empty to use the default logo.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Current Logo Preview</th>
                        <td>
                            <div id="logo-preview-wrapper" style="padding: 15px; background: #f0f0f1; border-radius: 8px; display: inline-block; margin-bottom: 10px;">
                                <img id="logo-preview" src="<?php echo esc_url($display_url); ?>" alt="Logo Preview" style="max-width: 300px; height: auto; display: block; transform: scale(<?php echo esc_attr($multiplier); ?>); transform-origin: left top;">
                            </div>
                            <p class="description">Preview of how the logo will appear (with current multiplier applied).</p>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="card" style="max-width: 800px; padding: 20px; margin-top: 20px;">
                <h2 class="title">Logo Size</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="warafy_logo_multiplier">Size Multiplier</label></th>
                        <td>
                            <select name="warafy_logo_multiplier" id="warafy_logo_multiplier">
                                <?php foreach ($multiplier_options as $opt): ?>
                                    <option value="<?php echo esc_attr($opt); ?>" <?php selected($multiplier, $opt); ?>>
                                        <?php echo esc_html($opt); ?>×
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description">Multiply the logo size. 1.0× = original size. Range: 0.5× (half) to 2.0× (double).</p>
                        </td>
                    </tr>
                </table>
            </div>

            <?php submit_button(); ?>
        </form>
    </div>

    <script>
    jQuery(document).ready(function($) {
        // Live preview: update image when URL changes
        $('#warafy_logo_url').on('input change', function() {
            var url = $(this).val().trim();
            if (!url) {
                url = '<?php echo esc_js(home_url(WARAFY_DEFAULT_LOGO_URL)); ?>';
            }
            $('#logo-preview').attr('src', url);
        });

        // Live preview: update scale when multiplier changes
        $('#warafy_logo_multiplier').on('change', function() {
            var scale = $(this).val();
            $('#logo-preview').css('transform', 'scale(' + scale + ')');
        });
    });
    </script>
    <?php
}
