<?php
/**
 * Logo Settings
 * Admin menu for managing site logo URL and size (pixel height).
 * Outputs a single CSS custom property (--logo-h) so every page
 * inherits the same logo size with zero per-page overrides.
 */

if (!defined('ABSPATH')) {
    exit;
}

// Default logo URL (the original hardcoded one)
define('WARAFY_DEFAULT_LOGO_URL', '/wp-content/uploads/2026/02/Weixin-Image_20260127091047_649_135.jpg');

// ── Admin Menu ─────────────────────────────────────────────
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

// ── Register Settings ──────────────────────────────────────
add_action('admin_init', 'warafy_logo_settings_init');

function warafy_logo_settings_init() {
    register_setting('warafy_logo_settings', 'warafy_logo_url', array(
        'sanitize_callback' => 'esc_url_raw',
        'default' => '',
    ));
    register_setting('warafy_logo_settings', 'warafy_logo_height', array(
        'sanitize_callback' => 'warafy_sanitize_logo_height',
        'default' => '48',
    ));
}

function warafy_sanitize_logo_height($value) {
    $value = intval($value);
    // Clamp between 24px and 120px
    if ($value < 24) $value = 24;
    if ($value > 120) $value = 120;
    return $value;
}

// ── Helper Functions (used across templates) ───────────────

/**
 * Get the logo URL.
 */
function warafy_get_logo_url() {
    $url = get_option('warafy_logo_url', '');
    if (empty($url)) {
        return home_url(WARAFY_DEFAULT_LOGO_URL);
    }
    return $url;
}

/**
 * Get the logo height in pixels.
 */
function warafy_get_logo_height() {
    return intval(get_option('warafy_logo_height', '48'));
}

/**
 * (Legacy) Get the logo size multiplier.
 * Kept for backward-compat; returns 1.0 since we now use absolute px.
 */
function warafy_get_logo_multiplier() {
    return 1.0;
}

// ── Centralized Logo CSS (injected into <head> on EVERY page) ──
add_action('wp_head', 'warafy_logo_inline_css', 5);

function warafy_logo_inline_css() {
    $h = warafy_get_logo_height();
    echo "<style id=\"warafy-logo-size\">
:root { --logo-h: {$h}px; }
/* Unified logo styling — single source of truth */
.warafy-logo-img {
    height: var(--logo-h) !important;
    width: auto !important;
    max-width: 280px !important;
    object-fit: contain !important;
    display: block;
}
</style>\n";
}

// ── Admin Settings Page ────────────────────────────────────
function warafy_logo_settings_page() {
    $logo_url    = get_option('warafy_logo_url', '');
    $display_url = !empty($logo_url) ? $logo_url : home_url(WARAFY_DEFAULT_LOGO_URL);
    $height      = get_option('warafy_logo_height', '48');
    ?>
    <div class="wrap">
        <h1>Logo Settings</h1>
        <p>Manage your site logo image and size. Changes apply <strong>globally</strong> to every page (header, login, register, preloader, favicon).</p>

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
                                <img id="logo-preview" src="<?php echo esc_url($display_url); ?>" alt="Logo Preview" style="height: <?php echo esc_attr($height); ?>px; width: auto; display: block;">
                            </div>
                            <p class="description">Preview of how the logo will appear at the current height.</p>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="card" style="max-width: 800px; padding: 20px; margin-top: 20px;">
                <h2 class="title">Logo Size</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="warafy_logo_height">Logo Height (px)</label></th>
                        <td>
                            <input type="range" name="warafy_logo_height" id="warafy_logo_height" min="24" max="120" step="2" value="<?php echo esc_attr($height); ?>" style="width: 300px; vertical-align: middle;">
                            <span id="height-display" style="font-weight: bold; font-size: 16px; margin-left: 12px;"><?php echo esc_html($height); ?>px</span>
                            <p class="description" style="margin-top: 8px;">Drag to adjust. All logos across the site will use this exact height. Range: 24px – 120px.</p>
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

        // Live preview: update height when slider changes
        $('#warafy_logo_height').on('input change', function() {
            var h = $(this).val();
            $('#height-display').text(h + 'px');
            $('#logo-preview').css('height', h + 'px');
        });
    });
    </script>
    <?php
}
