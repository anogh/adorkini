<?php
/**
 * Translation Hub
 * Centralized interface for managing translations and AI settings
 */

if (!defined('ABSPATH')) {
    exit;
}

// Register Admin Menu
add_action('admin_menu', 'warafy_translation_hub_menu');
function warafy_translation_hub_menu() {
    add_theme_page(
        'Translation Hub',
        'Translation Hub',
        'manage_options',
        'warafy-translation-hub',
        'warafy_translation_hub_page'
    );
}

// Register AI Settings
add_action('admin_init', 'warafy_translation_hub_settings_init');
function warafy_translation_hub_settings_init() {
    register_setting('warafy_ai_settings', 'warafy_ai_api_endpoint');
    register_setting('warafy_ai_settings', 'warafy_ai_model');
    register_setting('warafy_ai_settings', 'warafy_ai_api_key');
    register_setting('warafy_ai_settings', 'warafy_ai_prompt');
}

// Enqueue Scripts and Styles
add_action('admin_enqueue_scripts', 'warafy_translation_hub_scripts');
function warafy_translation_hub_scripts($hook) {
    if ($hook !== 'appearance_page_warafy-translation-hub') {
        return;
    }
    
    wp_enqueue_style('warafy-translation-hub-css', false);
    wp_add_inline_style('warafy-translation-hub-css', "
        /* Force table layout */
        .translation-hub-table { width: 100% !important; border-collapse: collapse; background: #fff; box-shadow: 0 1px 1px rgba(0,0,0,0.04); table-layout: fixed !important; }
        .translation-hub-table th, .translation-hub-table td { padding: 20px; border-bottom: 3px solid #999 !important; text-align: left; vertical-align: top; box-sizing: border-box; word-wrap: break-word !important; overflow-wrap: break-word !important; }
        .translation-hub-table th { background: #f9f9f9; font-weight: 600; color: #23282d; border-bottom: 3px solid #666 !important; }
        .translation-hub-table tbody tr { border-bottom: 2px solid #ccc !important; }
        .translation-hub-table tr:hover { background: #f5f5f5; }
        
        /* Force column widths with max-width */
        .translation-hub-table th:nth-child(1), .translation-hub-table td:nth-child(1) { width: 40px !important; max-width: 40px !important; min-width: 40px !important; text-align: center; }
        .translation-hub-table th:nth-child(2), .translation-hub-table td:nth-child(2) { width: 48% !important; max-width: 48% !important; }
        .translation-hub-table th:nth-child(3), .translation-hub-table td:nth-child(3) { width: 48% !important; max-width: 48% !important; }
        
        .source-text-block { margin-bottom: 15px; }
        .source-label { font-size: 11px; text-transform: uppercase; color: #888; letter-spacing: 0.5px; margin-bottom: 6px; display: block !important; font-weight: bold; clear: both; }
        .source-content { font-size: 14px; line-height: 1.6; color: #333; background: #f8f8f8; padding: 12px; border-radius: 4px; border: 1px solid #ddd; word-wrap: break-word !important; overflow-wrap: break-word !important; white-space: pre-wrap; }
        
        .translation-block { margin-bottom: 15px; display: block !important; width: 100% !important; clear: both; }
        .translation-block .source-label { display: block !important; width: 100%; margin-bottom: 6px; }
        .translation-input { width: 100% !important; max-width: 100% !important; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; line-height: 1.5; box-sizing: border-box !important; display: block !important; clear: both; }
        .translation-input:focus { border-color: #2271b1; box-shadow: 0 0 0 1px #2271b1; outline: none; }
        textarea.translation-input { min-height: 150px; resize: vertical; overflow-y: auto; width: 100% !important; }
        input.translation-input { width: 100% !important; }
        .warafy-spinner { display: none; margin: 0 10px; vertical-align: middle; }
        .warafy-spinner.is-active { display: inline-block; }
        
        /* Search & Replace Highlights */
        .search-highlight { background-color: #fff3cd !important; border-color: #ffc107 !important; }
        .search-current { background-color: #d4edda !important; border-color: #28a745 !important; box-shadow: 0 0 5px rgba(40, 167, 69, 0.5) !important; }
    ");
}

// Render Page
function warafy_translation_hub_page() {
    $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'ui_translations';
    
    // Handle Save for UI Translations
    if (isset($_POST['save_ui_translations']) && check_admin_referer('warafy_save_translations')) {
        $ui_translations = [];
        if (isset($_POST['translations']) && is_array($_POST['translations'])) {
            foreach ($_POST['translations'] as $key => $value) {
                // Decode key if it was base64 encoded to be safe in input names
                $original_key = base64_decode($key);
                // Sanitize but allow Bengali characters
                $ui_translations[$original_key] = sanitize_text_field($value);
            }
        }
        
        $json_file = get_template_directory() . '/translations.json';
        file_put_contents($json_file, json_encode($ui_translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        echo '<div class="notice notice-success is-dismissible"><p>UI Translations saved successfully.</p></div>';
    }

    // Handle Save for Product Translations
    if (isset($_POST['save_product_translations']) && check_admin_referer('warafy_save_product_translations')) {
        if (isset($_POST['products']) && is_array($_POST['products'])) {
            foreach ($_POST['products'] as $product_id => $data) {
                if (!empty($data['bn_title'])) {
                    update_post_meta($product_id, '_bengali_title', sanitize_text_field($data['bn_title']));
                }
                if (!empty($data['bn_short_desc'])) {
                    update_post_meta($product_id, '_bengali_short_description', wp_kses_post($data['bn_short_desc']));
                }
            }
            echo '<div class="notice notice-success is-dismissible"><p>Product translations saved successfully.</p></div>';
        }
    }
    ?>
    <div class="wrap">
        <h1>Translation Hub <span style="font-size: 12px; font-weight: normal; color: #666;">v2.6 (MTPE)</span></h1>
        
        <h2 class="nav-tab-wrapper">
            <a href="?page=warafy-translation-hub&tab=ui_translations" class="nav-tab <?php echo $active_tab == 'ui_translations' ? 'nav-tab-active' : ''; ?>">UI Translations</a>
            <a href="?page=warafy-translation-hub&tab=product_translations" class="nav-tab <?php echo $active_tab == 'product_translations' ? 'nav-tab-active' : ''; ?>">Product Translations</a>
            <a href="?page=warafy-translation-hub&tab=ai_settings" class="nav-tab <?php echo $active_tab == 'ai_settings' ? 'nav-tab-active' : ''; ?>">AI Settings</a>
        </h2>
        
        <?php
        if ($active_tab == 'ui_translations') {
            warafy_render_ui_translations_tab();
        } elseif ($active_tab == 'product_translations') {
            warafy_render_product_translations_tab();
        } elseif ($active_tab == 'ai_settings') {
            warafy_render_ai_settings_tab();
        }
        ?>
    </div>
    <?php
}

function warafy_render_ui_translations_tab() {
    $json_file = get_template_directory() . '/translations.json';
    $translations = [];
    if (file_exists($json_file)) {
        $translations = json_decode(file_get_contents($json_file), true);
    }
    ?>
    <div class="card" style="max-width: 100%; padding: 20px;">
        <h3>Manage UI Text Translations</h3>
        <p class="description">Edit the Bengali translations for static UI text found throughout the theme.</p>
        
        <form method="post">
            <?php wp_nonce_field('warafy_save_translations'); ?>
            <table class="translation-hub-table">
                <thead>
                    <tr>
                        <th style="width: 50%;">English Text (Key)</th>
                        <th style="width: 50%;">Bengali Translation</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($translations)): ?>
                        <?php foreach ($translations as $en => $bn): ?>
                            <tr>
                                <td>
                                    <div class="source-content"><?php echo esc_html($en); ?></div>
                                </td>
                                <td>
                                    <?php 
                                    // Base64 encode key to ensure it's a valid input name even with spaces/special chars
                                    $key_safe = base64_encode($en); 
                                    ?>
                                    <input type="text" name="translations[<?php echo $key_safe; ?>]" value="<?php echo esc_attr($bn); ?>" class="translation-input" placeholder="Enter translation...">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="2">No translations found in translations.json</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <p class="submit">
                <input type="submit" name="save_ui_translations" id="save_ui_translations" class="button button-primary" value="Save Changes">
            </p>
        </form>
    </div>
    <?php
}

function warafy_render_product_translations_tab() {
    // Get all published products
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'orderby' => 'title',
        'order' => 'ASC'
    );
    $products = get_posts($args);
    ?>
    <div class="card" style="max-width: 100%; padding: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3>Product Translations (MTPE)</h3>
            <div>
                <button type="button" id="translate-selected-btn" class="button button-secondary">
                    <span class="dashicons dashicons-translation" style="vertical-align: text-top;"></span> Translate Selected with AI
                </button>
                <span class="spinner warafy-spinner" id="ai-spinner"></span>
            </div>
        </div>
        
        <!-- MTPE Search & Replace Toolbar -->
        <div style="background: #f9f9f9; border: 1px solid #ddd; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
            <div style="display: flex; gap: 20px; flex-wrap: wrap; align-items: flex-end;">
                <!-- Search Section -->
                <div style="flex: 1; min-width: 200px;">
                    <label style="display: block; font-weight: bold; margin-bottom: 5px; font-size: 12px;">Search In:</label>
                    <select id="search-target" style="width: 100%; padding: 8px; margin-bottom: 8px;">
                        <option value="source">Source (English)</option>
                        <option value="target">Target (Bengali)</option>
                        <option value="both">Both</option>
                    </select>
                    <input type="text" id="search-text" placeholder="Search text..." style="width: 100%; padding: 8px; box-sizing: border-box;">
                </div>
                
                <!-- Replace Section -->
                <div style="flex: 1; min-width: 200px;">
                    <label style="display: block; font-weight: bold; margin-bottom: 5px; font-size: 12px;">Replace With:</label>
                    <input type="text" id="replace-text" placeholder="Replace with..." style="width: 100%; padding: 8px; box-sizing: border-box; margin-top: 35px;">
                </div>
                
                <!-- Action Buttons -->
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <button type="button" id="search-btn" class="button button-secondary">
                        <span class="dashicons dashicons-search" style="vertical-align: text-top;"></span> Find
                    </button>
                    <button type="button" id="replace-btn" class="button button-secondary">
                        <span class="dashicons dashicons-editor-paste-word" style="vertical-align: text-top;"></span> Replace
                    </button>
                    <button type="button" id="replace-all-btn" class="button button-secondary">
                        Replace All
                    </button>
                    <button type="button" id="clear-search-btn" class="button">
                        Clear
                    </button>
                    <button type="button" id="save-translations-btn" class="button button-primary" style="margin-left: 20px;">
                        <span class="dashicons dashicons-saved" style="vertical-align: text-top;"></span> Save All Translations
                    </button>
                </div>
            </div>
            <div id="search-results-info" style="margin-top: 10px; font-size: 12px; color: #666;"></div>
        </div>
        
        <form method="post" id="product-translation-form">
            <?php wp_nonce_field('warafy_save_product_translations'); ?>
            
            <div style="border: 2px solid #333;">
                <table class="translation-hub-table" style="width: 100%; table-layout: fixed; border-collapse: collapse;">
                    <colgroup>
                        <col style="width: 40px;">
                        <col style="width: 50%;">
                        <col style="width: 50%;">
                    </colgroup>
                    <thead>
                        <tr style="background: #f1f1f1;">
                            <th style="padding: 15px; border: 1px solid #ccc; text-align: center;"><input type="checkbox" id="select-all-products"></th>
                            <th style="padding: 15px; border: 1px solid #ccc; font-weight: bold;">Original (English)</th>
                            <th style="padding: 15px; border: 1px solid #ccc; font-weight: bold;">Translation (Bengali)</th>
                        </tr>
                    </thead>
                    <tbody id="product-list-body">
                        <?php foreach ($products as $post): 
                            $product = wc_get_product($post->ID);
                            $bn_title = get_post_meta($post->ID, '_bengali_title', true);
                            $bn_short_desc = get_post_meta($post->ID, '_bengali_short_description', true);
                            ?>
                            <tr class="product-row" data-id="<?php echo $post->ID; ?>" style="border-bottom: 2px solid #999;">
                                <td style="padding: 15px; border: 1px solid #ccc; vertical-align: top; text-align: center;">
                                    <input type="checkbox" name="product_ids[]" value="<?php echo $post->ID; ?>" class="product-checkbox">
                                </td>
                                <td style="padding: 15px; border: 1px solid #ccc; vertical-align: top; word-wrap: break-word;">
                                    <div class="source-text-block">
                                        <span class="source-label">Product Name</span>
                                        <div class="source-content"><?php echo esc_html($post->post_title); ?></div>
                                        <input type="hidden" class="source-title" value="<?php echo esc_attr($post->post_title); ?>">
                                    </div>
                                    
                                    <?php if (!empty($post->post_excerpt)): ?>
                                    <div class="source-text-block">
                                        <span class="source-label">Short Description</span>
                                        <div class="source-content"><?php echo nl2br(esc_html($post->post_excerpt)); ?></div>
                                        <input type="hidden" class="source-short-desc" value="<?php echo esc_attr($post->post_excerpt); ?>">
                                    </div>
                                    <?php endif; ?>
                                    
                                    <div style="margin-top: 10px;">
                                        <a href="<?php echo get_permalink($post->ID); ?>" target="_blank" class="button button-small">
                                            <span class="dashicons dashicons-external" style="font-size: 14px; vertical-align: text-bottom;"></span> View Product
                                        </a>
                                        <a href="<?php echo get_edit_post_link($post->ID); ?>" target="_blank" class="button button-small" style="margin-left: 5px;">
                                            <span class="dashicons dashicons-edit" style="font-size: 14px; vertical-align: text-bottom;"></span> Edit
                                        </a>
                                    </div>
                                </td>
                                <td style="padding: 15px; border: 1px solid #ccc; vertical-align: top;">
                                    <div class="translation-block" style="display: block; width: 100%; margin-bottom: 15px;">
                                        <label style="display: block; width: 100%; margin-bottom: 8px; font-size: 11px; text-transform: uppercase; color: #888; font-weight: bold;">Bengali Title</label>
                                        <input type="text" name="products[<?php echo $post->ID; ?>][bn_title]" class="translation-input bn-title-input" value="<?php echo esc_attr($bn_title); ?>" placeholder="Enter Bengali title..." style="width: 100% !important; display: block; box-sizing: border-box; padding: 10px;">
                                    </div>
                                    
                                    <?php if (!empty($post->post_excerpt)): ?>
                                    <div class="translation-block" style="display: block; width: 100%; margin-bottom: 15px;">
                                        <label style="display: block; width: 100%; margin-bottom: 8px; font-size: 11px; text-transform: uppercase; color: #888; font-weight: bold;">Bengali Short Description</label>
                                        <textarea name="products[<?php echo $post->ID; ?>][bn_short_desc]" rows="6" class="translation-input bn-desc-input" placeholder="Enter Bengali description..." style="width: 100% !important; display: block; box-sizing: border-box; padding: 10px; min-height: 150px;"><?php echo esc_textarea($bn_short_desc); ?></textarea>
                                    </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

    <script>
    jQuery(document).ready(function($) {
        // Select All handler
        $('#select-all-products').change(function() {
            $('.product-checkbox').prop('checked', $(this).is(':checked'));
        });

        // AI Translation Handler
        $('#translate-selected-btn').click(function() {
            var selectedRows = $('.product-checkbox:checked').closest('tr');
            
            if (selectedRows.length === 0) {
                alert('Please select at least one product to translate.');
                return;
            }

            var productsToTranslate = [];
            selectedRows.each(function() {
                var row = $(this);
                var id = row.data('id');
                var title = row.find('.source-title').val();
                var shortDesc = row.find('.source-short-desc').val();
                
                productsToTranslate.push({
                    id: id,
                    title: title,
                    short_desc: shortDesc
                });
            });

            if (!confirm('You are about to translate ' + productsToTranslate.length + ' products using AI. This may take a moment. Continue?')) {
                return;
            }

            // UI State: Loading
            $('#translate-selected-btn').prop('disabled', true);
            $('#ai-spinner').addClass('is-active');
            $('.product-row').addClass('warafy_loading');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'warafy_ai_batch_translate',
                    products: productsToTranslate,
                    nonce: '<?php echo wp_create_nonce("warafy_ai_translate_nonce"); ?>'
                },
                success: function(response) {
                    if (response.success && response.data) {
                        // Apply translations
                        $.each(response.data, function(id, data) {
                            var row = $('.product-row[data-id="' + id + '"]');
                            if (data.title) {
                                row.find('.bn-title-input').val(data.title);
                            }
                            if (data.short_desc) {
                                row.find('.bn-desc-input').val(data.short_desc);
                            }
                        });
                        alert('Translation complete! Please review and click "Save Translations".');
                    } else {
                        alert('Error: ' + (response.data || 'Unknown error occurred.'));
                    }
                },
                error: function() {
                    alert('Network error occurred. Please try again.');
                },
                complete: function() {
                    $('#translate-selected-btn').prop('disabled', false);
                    $('#ai-spinner').removeClass('is-active');
                    $('.product-row').removeClass('warafy_loading');
                }
            });
        });
        
        // ==================== MTPE Search & Replace ====================
        var currentMatches = [];
        var currentMatchIndex = -1;
        
        function clearHighlights() {
            $('.source-content, .translation-input').each(function() {
                $(this).removeClass('search-highlight search-current');
            });
            currentMatches = [];
            currentMatchIndex = -1;
            $('#search-results-info').text('');
        }
        
        function performSearch() {
            clearHighlights();
            var searchText = $('#search-text').val().toLowerCase().trim();
            var searchTarget = $('#search-target').val();
            
            if (!searchText) {
                $('#search-results-info').text('Please enter search text.');
                return;
            }
            
            $('.product-row').each(function() {
                var row = $(this);
                
                // Search in source
                if (searchTarget === 'source' || searchTarget === 'both') {
                    row.find('.source-content').each(function() {
                        if ($(this).text().toLowerCase().includes(searchText)) {
                            $(this).addClass('search-highlight');
                            currentMatches.push($(this));
                        }
                    });
                }
                
                // Search in target
                if (searchTarget === 'target' || searchTarget === 'both') {
                    row.find('.translation-input').each(function() {
                        if ($(this).val().toLowerCase().includes(searchText)) {
                            $(this).addClass('search-highlight');
                            currentMatches.push($(this));
                        }
                    });
                }
            });
            
            if (currentMatches.length > 0) {
                currentMatchIndex = 0;
                currentMatches[0].addClass('search-current');
                currentMatches[0][0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                $('#search-results-info').text('Found ' + currentMatches.length + ' match(es). Showing 1 of ' + currentMatches.length);
            } else {
                $('#search-results-info').text('No matches found.');
            }
        }
        
        function replaceCurrentMatch() {
            var searchText = $('#search-text').val();
            var replaceText = $('#replace-text').val();
            var searchTarget = $('#search-target').val();
            
            if (!searchText) {
                alert('Please enter search text.');
                return;
            }
            
            if (searchTarget === 'source') {
                alert('Cannot replace in source (English) content. Source is read-only.\n\nTo edit source content, click "Edit" button on the product row to open the WooCommerce product editor.\n\nTo replace in translations, select "Target (Bengali)" from the dropdown.');
                return;
            }
            
            if (currentMatches.length === 0) {
                // Force search in target only for replace
                var origTarget = $('#search-target').val();
                if (origTarget === 'source') {
                    $('#search-target').val('target');
                }
                performSearch();
                $('#search-target').val(origTarget);
                if (currentMatches.length === 0) return;
            }
            
            var replacedAny = false;
            var current = currentMatches[currentMatchIndex];
            
            // Only replace in input/textarea (target fields)
            if (current.is('input, textarea')) {
                var currentVal = current.val();
                var regex = new RegExp(searchText.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'gi');
                current.val(currentVal.replace(regex, replaceText));
                current.css('background-color', '#d4edda');
                setTimeout(function() { current.css('background-color', ''); }, 1000);
                replacedAny = true;
            } else {
                // This is a source field (div), skip it
                $('#search-results-info').text('Skipped source field (read-only). Moving to next...');
            }
            
            // Move to next match
            currentMatches.splice(currentMatchIndex, 1);
            if (currentMatches.length > 0) {
                if (currentMatchIndex >= currentMatches.length) {
                    currentMatchIndex = 0;
                }
                currentMatches.forEach(function(m) { m.removeClass('search-current'); });
                currentMatches[currentMatchIndex].addClass('search-current');
                currentMatches[currentMatchIndex][0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                $('#search-results-info').text((replacedAny ? 'Replaced. ' : '') + currentMatches.length + ' match(es) remaining.');
            } else {
                clearHighlights();
                $('#search-results-info').text('All done!');
            }
        }
        
        function replaceAllMatches() {
            var searchText = $('#search-text').val();
            var replaceText = $('#replace-text').val();
            var searchTarget = $('#search-target').val();
            
            if (!searchText) {
                alert('Please enter search text.');
                return;
            }
            
            if (searchTarget === 'source') {
                alert('Cannot replace in source (English) content. Source is read-only.\n\nTo replace in translations, select "Target (Bengali)" or "Both" from the dropdown.');
                return;
            }
            
            var replaceCount = 0;
            var regex = new RegExp(searchText.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'gi');
            
            $('.product-row').each(function() {
                var row = $(this);
                
                // Replace in target fields only
                row.find('.translation-input').each(function() {
                    var currentVal = $(this).val();
                    if (currentVal.toLowerCase().includes(searchText.toLowerCase())) {
                        var newVal = currentVal.replace(regex, replaceText);
                        $(this).val(newVal);
                        $(this).css('background-color', '#d4edda');
                        var el = $(this);
                        setTimeout(function() { el.css('background-color', ''); }, 1500);
                        replaceCount++;
                    }
                });
            });
            
            clearHighlights();
            if (replaceCount > 0) {
                $('#search-results-info').html('<strong style="color: green;">✓ Replaced ' + replaceCount + ' field(s). Click "Save All Translations" to save!</strong>');
            } else {
                $('#search-results-info').text('No matches found in target (Bengali) fields.');
            }
        }
        
        // Event handlers
        $('#search-btn').click(function() {
            performSearch();
        });
        
        $('#search-text').keypress(function(e) {
            if (e.which === 13) {
                e.preventDefault();
                performSearch();
            }
        });
        
        $('#replace-btn').click(function() {
            replaceCurrentMatch();
        });
        
        $('#replace-all-btn').click(function() {
            if (confirm('This will replace all occurrences in target fields. Continue?')) {
                replaceAllMatches();
            }
        });
        
        $('#clear-search-btn').click(function() {
            $('#search-text').val('');
            $('#replace-text').val('');
            clearHighlights();
        });
        
        // Save Translations via AJAX
        $('#save-translations-btn').click(function() {
            var btn = $(this);
            var originalText = btn.html();
            
            // Collect all product translations
            var productsData = {};
            $('.product-row').each(function() {
                var row = $(this);
                var productId = row.data('id');
                var bnTitle = row.find('.bn-title-input').val();
                var bnDesc = row.find('.bn-desc-input').val();
                
                productsData[productId] = {
                    bn_title: bnTitle,
                    bn_short_desc: bnDesc || ''
                };
            });
            
            // Show saving state
            btn.html('<span class="dashicons dashicons-update spin" style="vertical-align: text-top;"></span> Saving...');
            btn.prop('disabled', true);
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'warafy_save_product_translations_ajax',
                    products: productsData,
                    nonce: '<?php echo wp_create_nonce("warafy_save_product_translations_ajax"); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        btn.html('<span class="dashicons dashicons-yes" style="vertical-align: text-top;"></span> Saved!');
                        setTimeout(function() {
                            btn.html(originalText);
                        }, 2000);
                    } else {
                        alert('Error: ' + (response.data || 'Failed to save.'));
                        btn.html(originalText);
                    }
                },
                error: function() {
                    alert('Network error. Please try again.');
                    btn.html(originalText);
                },
                complete: function() {
                    btn.prop('disabled', false);
                }
            });
        });
    });
    </script>
    <?php
}

function warafy_render_ai_settings_tab() {
    $default_prompt = "You are a professional translator. Translate the following product information from English to Bengali. Return ONLY a valid JSON object where keys are product IDs and values are objects containing 'title' and 'short_desc' with the Bengali translations. Do not include markdown formatting (like ```json), just the raw JSON string.";
    $saved_prompt = get_option('warafy_ai_prompt', $default_prompt);
    ?>
    <div class="card" style="max-width: 800px; padding: 20px;">
        <h3>AI Configuration</h3>
        <p class="description">Configure the AI service used for automated translations.</p>
        
        <form method="post" action="options.php">
            <?php
            settings_fields('warafy_ai_settings');
            do_settings_sections('warafy_ai_settings');
            ?>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="warafy_ai_api_endpoint">API Endpoint</label></th>
                    <td>
                        <input type="text" name="warafy_ai_api_endpoint" id="warafy_ai_api_endpoint" value="<?php echo esc_attr(get_option('warafy_ai_api_endpoint')); ?>" class="regular-text" placeholder="https://api.openai.com/v1/chat/completions">
                        <p class="description">The API endpoint URL (compatible with OpenAI format).</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="warafy_ai_model">Model Name</label></th>
                    <td>
                        <input type="text" name="warafy_ai_model" id="warafy_ai_model" value="<?php echo esc_attr(get_option('warafy_ai_model', 'gpt-3.5-turbo')); ?>" class="regular-text">
                        <p class="description">e.g., gpt-4o, gpt-4o-mini, gpt-3.5-turbo</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="warafy_ai_api_key">API Key</label></th>
                    <td>
                        <input type="password" name="warafy_ai_api_key" id="warafy_ai_api_key" value="<?php echo esc_attr(get_option('warafy_ai_api_key')); ?>" class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="warafy_ai_prompt">Translation Prompt</label></th>
                    <td>
                        <textarea name="warafy_ai_prompt" id="warafy_ai_prompt" rows="6" class="large-text code" style="width: 100%;"><?php echo esc_textarea($saved_prompt); ?></textarea>
                        <p class="description">The prompt sent to AI for translation. The product data will be appended automatically as JSON. Use placeholders if needed.</p>
                        <button type="button" class="button" onclick="document.getElementById('warafy_ai_prompt').value = '<?php echo esc_js($default_prompt); ?>';">Reset to Default</button>
                    </td>
                </tr>
            </table>
            <?php submit_button('Save Settings'); ?>
        </form>
    </div>
    <?php
}

// AJAX Handler for AI Translation
add_action('wp_ajax_warafy_ai_batch_translate', 'warafy_ai_batch_translate_handler');

function warafy_ai_batch_translate_handler() {
    check_ajax_referer('warafy_ai_translate_nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized');
    }

    $products = isset($_POST['products']) ? $_POST['products'] : [];
    if (empty($products)) {
        wp_send_json_error('No products provided');
    }

    $api_key = get_option('warafy_ai_api_key');
    $api_endpoint = get_option('warafy_ai_api_endpoint');
    $model = get_option('warafy_ai_model');

    if (empty($api_key) || empty($api_endpoint)) {
        wp_send_json_error('AI Configuration missing. Please check Settings tab.');
    }

    // Construct the prompt
    $items_to_translate = [];
    foreach ($products as $p) {
        $items_to_translate[$p['id']] = [
            'title' => $p['title'],
            'short_desc' => $p['short_desc']
        ];
    }

    $default_prompt = "You are a professional translator. Translate the following product information from English to Bengali. Return ONLY a valid JSON object where keys are product IDs and values are objects containing 'title' and 'short_desc' with the Bengali translations. Do not include markdown formatting (like ```json), just the raw JSON string.";
    $saved_prompt = get_option('warafy_ai_prompt', $default_prompt);
    $prompt = $saved_prompt . "\n\nInput Data:\n" . json_encode($items_to_translate);

    // Call API
    $body = [
        'model' => $model,
        'messages' => [
            ['role' => 'system', 'content' => 'You are a helpful assistant that outputs only JSON.'],
            ['role' => 'user', 'content' => $prompt]
        ],
        'temperature' => 0.3
    ];

    $response = wp_remote_post($api_endpoint, [
        'headers' => [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $api_key
        ],
        'body' => json_encode($body),
        'timeout' => 60 // Extended timeout for batch processing
    ]);

    if (is_wp_error($response)) {
        wp_send_json_error('API Request failed: ' . $response->get_error_message());
    }

    $response_body = wp_remote_retrieve_body($response);
    $data = json_decode($response_body, true);

    if (isset($data['error'])) {
        wp_send_json_error('API Error: ' . json_encode($data['error']));
    }

    // Parse logic for different API providers (assuming OpenAI compatible)
    $content = isset($data['choices'][0]['message']['content']) ? $data['choices'][0]['message']['content'] : '';
    
    // Clean up potential markdown code blocks
    $content = str_replace('```json', '', $content);
    $content = str_replace('```', '', $content);
    $content = trim($content);

    $translated_data = json_decode($content, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        wp_send_json_error('Failed to parse AI response: ' . $content);
    }

    wp_send_json_success($translated_data);
}

// AJAX Handler for Saving Product Translations
add_action('wp_ajax_warafy_save_product_translations_ajax', 'warafy_save_product_translations_ajax_handler');

function warafy_save_product_translations_ajax_handler() {
    check_ajax_referer('warafy_save_product_translations_ajax', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized');
    }

    $products = isset($_POST['products']) ? $_POST['products'] : [];
    if (empty($products)) {
        wp_send_json_error('No products provided');
    }

    $saved_count = 0;
    foreach ($products as $product_id => $data) {
        $product_id = intval($product_id);
        if ($product_id > 0) {
            if (isset($data['bn_title'])) {
                update_post_meta($product_id, '_bengali_title', sanitize_text_field($data['bn_title']));
            }
            if (isset($data['bn_short_desc'])) {
                update_post_meta($product_id, '_bengali_short_description', sanitize_textarea_field($data['bn_short_desc']));
            }
            $saved_count++;
        }
    }

    wp_send_json_success('Saved ' . $saved_count . ' product translations.');
}

// Filter to display Bengali short description on frontend
add_filter('the_excerpt', 'warafy_bengali_excerpt_filter', 10, 1);
add_filter('get_the_excerpt', 'warafy_bengali_excerpt_filter', 10, 2);

function warafy_bengali_excerpt_filter($excerpt, $post = null) {
    // Check if we're viewing a product and Bengali is active
    if (!function_exists('warafy_is_bengali') || !warafy_is_bengali()) {
        return $excerpt;
    }
    
    // Get post ID
    $post_id = $post ? (is_object($post) ? $post->ID : $post) : get_the_ID();
    if (!$post_id) {
        return $excerpt;
    }
    
    // Check if this is a product
    if (get_post_type($post_id) !== 'product') {
        return $excerpt;
    }
    
    // Get Bengali short description
    $bengali_desc = get_post_meta($post_id, '_bengali_short_description', true);
    
    // Return Bengali description if available, otherwise return original
    if (!empty($bengali_desc)) {
        return wpautop($bengali_desc);
    }
    
    return $excerpt;
}
