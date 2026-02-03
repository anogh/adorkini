<?php
/**
 * Product Ranking System
 * Admin menu, settings, ranked products
 */

if (!defined('ABSPATH')) {
    exit;
}

// Add admin menu for product ranking
add_action('admin_menu', 'warafy_product_ranking_menu');
function warafy_product_ranking_menu() {
    add_theme_page(
        'Product Ranking',
        'Product Ranking',
        'manage_options',
        'warafy-product-ranking',
        'warafy_product_ranking_page'
    );
}

// Display admin page for product ranking
function warafy_product_ranking_page() {
    ?>
    <div class="wrap">
        <h1>Product Ranking Management</h1>
        <p>Enter comma-separated product IDs for ranking. The order determines the ranking position.</p>
        
        <form method="post" action="options.php">
            <?php
            settings_fields('warafy_product_ranking');
            do_settings_sections('warafy_product_ranking');
            ?>
            
            <h2>Top 10 Products (Homepage)</h2>
            <p>Enter up to 10 product IDs separated by commas. The first ID will be #1, second will be #2, etc.</p>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="warafy_homepage_ranking_ids">Homepage Ranking IDs</label>
                    </th>
                    <td>
                        <textarea id="warafy_homepage_ranking_ids" name="warafy_homepage_ranking_ids" rows="3" class="large-text" placeholder="123, 456, 789, 1011, 1213"><?php 
                            $homepage_ids = get_option('warafy_homepage_ranking_ids', '');
                            echo esc_textarea($homepage_ids);
                        ?></textarea>
                        <p class="description">Enter product IDs separated by commas. Maximum 10 products.</p>
                    </td>
                </tr>
            </table>
            
            <div class="homepage-preview">
                <h3>Preview:</h3>
                <div id="homepage-preview-container" class="ranking-preview">
                    <?php
                    $homepage_ids = get_option('warafy_homepage_ranking_ids', '');
                    if (!empty($homepage_ids)) {
                        $ids_array = array_map('trim', explode(',', $homepage_ids));
                        $ids_array = array_filter($ids_array);
                        $ids_array = array_slice($ids_array, 0, 10);
                        
                        foreach ($ids_array as $index => $product_id) {
                            $product = wc_get_product($product_id);
                            if ($product) {
                                echo '<div class="preview-item">';
                                echo '<span class="rank">#' . ($index + 1) . '</span>';
                                echo '<span class="product-info">' . $product->get_name() . ' (ID: ' . $product_id . ')</span>';
                                echo '</div>';
                            } else {
                                echo '<div class="preview-item error">';
                                echo '<span class="rank">#' . ($index + 1) . '</span>';
                                echo '<span class="product-info">Invalid Product ID: ' . $product_id . '</span>';
                                echo '</div>';
                            }
                        }
                    } else {
                        echo '<p class="no-products">No product IDs entered.</p>';
                    }
                    ?>
                </div>
            </div>
            
            <h2 class="mt-8">Top 100 Products (Ranking Page)</h2>
            <p>Enter up to 100 product IDs separated by commas. The first ID will be #1, second will be #2, etc.</p>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="warafy_full_ranking_ids">Full Ranking IDs</label>
                    </th>
                    <td>
                        <textarea id="warafy_full_ranking_ids" name="warafy_full_ranking_ids" rows="5" class="large-text" placeholder="123, 456, 789, 1011, 1213, 1415, 1617, 1819, 2021, 2223"><?php 
                            $full_ids = get_option('warafy_full_ranking_ids', '');
                            echo esc_textarea($full_ids);
                        ?></textarea>
                        <p class="description">Enter product IDs separated by commas. Maximum 100 products.</p>
                    </td>
                </tr>
            </table>
            
            <div class="full-preview">
                <h3>Preview (First 20):</h3>
                <div id="full-preview-container" class="ranking-preview">
                    <?php
                    $full_ids = get_option('warafy_full_ranking_ids', '');
                    if (!empty($full_ids)) {
                        $ids_array = array_map('trim', explode(',', $full_ids));
                        $ids_array = array_filter($ids_array);
                        $preview_ids = array_slice($ids_array, 0, 20);
                        
                        foreach ($preview_ids as $index => $product_id) {
                            $product = wc_get_product($product_id);
                            if ($product) {
                                echo '<div class="preview-item">';
                                echo '<span class="rank">#' . ($index + 1) . '</span>';
                                echo '<span class="product-info">' . $product->get_name() . ' (ID: ' . $product_id . ')</span>';
                                echo '</div>';
                            } else {
                                echo '<div class="preview-item error">';
                                echo '<span class="rank">#' . ($index + 1) . '</span>';
                                echo '<span class="product-info">Invalid Product ID: ' . $product_id . '</span>';
                                echo '</div>';
                            }
                        }
                        
                        if (count($ids_array) > 20) {
                            echo '<p class="more-items">... and ' . (count($ids_array) - 20) . ' more products</p>';
                        }
                    } else {
                        echo '<p class="no-products">No product IDs entered.</p>';
                    }
                    ?>
                </div>
            </div>
            
            <?php submit_button(); ?>
        </form>
    </div>
    
    <style>
    .ranking-preview { border: 1px solid #ddd; padding: 10px; background: #f9f9f9; margin: 10px 0; max-height: 300px; overflow-y: auto; }
    .preview-item { display: flex; align-items: center; gap: 10px; padding: 5px 0; border-bottom: 1px solid #eee; }
    .preview-item:last-child { border-bottom: none; }
    .preview-item.error { color: #d63638; }
    .rank { font-weight: bold; color: #0073aa; min-width: 40px; }
    .product-info { flex: 1; }
    .no-products, .more-items { text-align: center; color: #666; font-style: italic; padding: 20px; }
    </style>
    <?php
}

// Register settings
add_action('admin_init', 'warafy_product_ranking_settings');
function warafy_product_ranking_settings() {
    register_setting('warafy_product_ranking', 'warafy_homepage_ranking_ids');
    register_setting('warafy_product_ranking', 'warafy_full_ranking_ids');
}

// AJAX handler for product verification
add_action('wp_ajax_warafy_verify_product', 'warafy_verify_product');
function warafy_verify_product() {
    $product_id = intval($_POST['product_id']);
    
    $product = wc_get_product($product_id);
    if ($product) {
        wp_send_json_success(['name' => $product->get_name()]);
    } else {
        wp_send_json_error('Product not found');
    }
}

// Get ranked products helper function
function warafy_get_ranked_products($type = 'homepage', $limit = null) {
    if ($type === 'homepage') {
        $ids_string = get_option('warafy_homepage_ranking_ids', '');
    } else {
        $ids_string = get_option('warafy_full_ranking_ids', '');
    }
    
    $rankings = array_map('trim', explode(',', $ids_string));
    $rankings = array_filter($rankings);
    $rankings = array_map('intval', $rankings);
    
    if ($limit) {
        $rankings = array_slice($rankings, 0, $limit);
    }
    
    return $rankings;
}
