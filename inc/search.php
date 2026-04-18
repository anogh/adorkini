<?php
/**
 * Search Autocomplete Functionality
 * Live search, suggestions, popular searches
 */

if (!defined('ABSPATH')) {
    exit;
}

// AJAX handler for live search autocomplete
add_action('wp_ajax_warafy_live_search', 'warafy_live_search');
add_action('wp_ajax_nopriv_warafy_live_search', 'warafy_live_search');

function warafy_live_search() {
    header('Content-Type: application/json; charset=utf-8');
    
    $search_term = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
    
    if (empty($search_term) || strlen($search_term) < 2) {
        wp_send_json_success(['products' => []]);
        wp_die();
    }
    
    $cache_key = 'warafy_search_' . md5(strtolower($search_term));
    $cached_results = get_transient($cache_key);
    
    if ($cached_results !== false) {
        wp_send_json_success(['products' => $cached_results]);
        wp_die();
    }
    
    $products = array();
    
    $args = array(
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => 10,
        'orderby' => 'relevance',
        's' => $search_term,
    );
    
    $products_query = new WP_Query($args);
    
    if ($products_query->have_posts()) {
        while ($products_query->have_posts()) {
            $products_query->the_post();
            $product = wc_get_product(get_the_ID());
            
            if ($product) {
                $products[] = array(
                    'id' => $product->get_id(),
                    'title' => get_the_title(),
                    'price' => $product->get_price_html(),
                    'image' => wp_get_attachment_image_src($product->get_image_id(), 'thumbnail')[0] ?? wc_placeholder_img_src(),
                    'url' => get_permalink(),
                    'sku' => $product->get_sku(),
                    'stock_status' => $product->is_in_stock() ? 'In Stock' : 'Out of Stock',
                    'categories' => warafy_get_product_categories(get_the_ID()),
                    'type' => 'exact'
                );
            }
        }
    }
    
    if (empty($products)) {
        $category_products = warafy_search_by_category($search_term);
        $products = array_merge($products, $category_products);
    }
    
    if (empty($products) && strlen($search_term) >= 3) {
        $fallback_args = array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => 50,
            'orderby' => 'title',
            'order' => 'ASC'
        );
        
        $fallback_query = new WP_Query($fallback_args);
        
        if ($fallback_query->have_posts()) {
            while ($fallback_query->have_posts()) {
                $fallback_query->the_post();
                $title = get_the_title();
                $similarity = warafy_calculate_similarity($search_term, $title);
                
                if ($similarity >= 0.4) {
                    $product = wc_get_product(get_the_ID());
                    if ($product) {
                        $products[] = array(
                            'id' => $product->get_id(),
                            'title' => $title,
                            'price' => $product->get_price_html(),
                            'image' => wp_get_attachment_image_src($product->get_image_id(), 'thumbnail')[0] ?? wc_placeholder_img_src(),
                            'url' => get_permalink(),
                            'sku' => $product->get_sku(),
                            'stock_status' => $product->is_in_stock() ? 'In Stock' : 'Out of Stock',
                            'categories' => warafy_get_product_categories(get_the_ID()),
                            'similarity' => $similarity,
                            'type' => 'fuzzy'
                        );
                    }
                }
            }
            
            usort($products, function($a, $b) {
                return ($b['similarity'] ?? 0) - ($a['similarity'] ?? 0);
            });
            
            $products = array_slice($products, 0, 10);
        }
    }
    
    set_transient($cache_key, $products, 600);
    warafy_log_search($search_term, count($products));
    
    wp_reset_postdata();
    wp_send_json_success(['products' => $products]);
    wp_die();
}

function warafy_search_by_category($search_term) {
    $products = array();
    
    $category_args = array(
        'taxonomy' => 'product_cat',
        'name__like' => $search_term,
        'hide_empty' => true,
        'number' => 5
    );
    
    $categories = get_terms($category_args);
    
    if (!empty($categories) && !is_wp_error($categories)) {
        foreach ($categories as $category) {
            $cat_products = get_posts(array(
                'post_type' => 'product',
                'post_status' => 'publish',
                'posts_per_page' => 3,
                'tax_query' => array(
                    array(
                        'taxonomy' => 'product_cat',
                        'field' => 'term_id',
                        'terms' => $category->term_id,
                    ),
                ),
            ));
            
            foreach ($cat_products as $product_post) {
                $product = wc_get_product($product_post->ID);
                if ($product) {
                    $products[] = array(
                        'id' => $product->get_id(),
                        'title' => get_the_title($product_post->ID),
                        'price' => $product->get_price_html(),
                        'image' => wp_get_attachment_image_src($product->get_image_id(), 'thumbnail')[0] ?? wc_placeholder_img_src(),
                        'url' => get_permalink($product_post->ID),
                        'sku' => $product->get_sku(),
                        'stock_status' => $product->is_in_stock() ? 'In Stock' : 'Out of Stock',
                        'categories' => warafy_get_product_categories($product_post->ID),
                        'type' => 'category',
                        'matched_category' => $category->name
                    );
                }
            }
        }
    }
    
    return $products;
}

function warafy_get_product_categories($product_id) {
    $categories = get_the_terms($product_id, 'product_cat');
    $category_names = array();
    
    if ($categories && !is_wp_error($categories)) {
        foreach ($categories as $category) {
            $category_names[] = $category->name;
        }
    }
    
    return $category_names;
}

function warafy_calculate_similarity($search, $title) {
    $search_lower = strtolower($search);
    $title_lower = strtolower($title);
    
    if ($search_lower === $title_lower) {
        return 1.0;
    }
    
    if (strpos($title_lower, $search_lower) !== false) {
        return 0.9;
    }
    
    similar_text($search_lower, $title_lower, $percent);
    return $percent / 100;
}

// AJAX handler for getting popular searches
add_action('wp_ajax_warafy_get_popular_searches', 'warafy_get_popular_searches');
add_action('wp_ajax_nopriv_warafy_get_popular_searches', 'warafy_get_popular_searches');

function warafy_get_popular_searches() {
    header('Content-Type: application/json; charset=utf-8');
    
    $search_logs = get_option('warafy_search_logs', array());
    $popular_searches = array();
    
    $cutoff_date = date('Y-m-d', strtotime('-7 days'));
    
    foreach ($search_logs as $date => $logs) {
        if ($date >= $cutoff_date) {
            foreach ($logs as $term => $data) {
                if (!isset($popular_searches[$term])) {
                    $popular_searches[$term] = 0;
                }
                $popular_searches[$term] += $data['count'];
            }
        }
    }
    
    arsort($popular_searches);
    $top_searches = array_slice(array_keys($popular_searches), 0, 10);
    
    if (empty($top_searches)) {
        $top_searches = array('phone case', 'laptop stand', 'wireless charger', 'bluetooth speaker', 'phone holder');
    }
    
    wp_send_json_success(['searches' => $top_searches]);
    wp_die();
}

function warafy_log_search($search_term, $result_count) {
    $search_logs = get_option('warafy_search_logs', array());
    $today = date('Y-m-d');
    
    if (!isset($search_logs[$today])) {
        $search_logs[$today] = array();
    }
    
    if (!isset($search_logs[$today][$search_term])) {
        $search_logs[$today][$search_term] = array(
            'count' => 0,
            'results' => 0,
            'last_search' => current_time('mysql')
        );
    }
    
    $search_logs[$today][$search_term]['count']++;
    $search_logs[$today][$search_term]['results'] = $result_count;
    $search_logs[$today][$search_term]['last_search'] = current_time('mysql');
    
    $cutoff_date = date('Y-m-d', strtotime('-30 days'));
    foreach ($search_logs as $date => $logs) {
        if ($date < $cutoff_date) {
            unset($search_logs[$date]);
        }
    }
    
    update_option('warafy_search_logs', $search_logs);
}

// Enqueue search autocomplete JavaScript
function warafy_search_autocomplete_scripts() {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        function initSearchAutocomplete() {
            const searchInputs = document.querySelectorAll('input[name="s"][type="search"]');
            
            searchInputs.forEach(searchInput => {
                const dropdown = document.createElement('div');
                dropdown.className = 'warafy-search-dropdown hidden absolute top-full left-0 right-0 mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg z-50 max-h-96 overflow-y-auto';
                
                const searchForm = searchInput.closest('form');
                if (searchForm) {
                    searchForm.style.position = 'relative';
                    searchForm.appendChild(dropdown);
                }
                
                let searchTimeout;
                
                searchInput.addEventListener('input', function(e) {
                    const searchTerm = e.target.value.trim();
                    clearTimeout(searchTimeout);
                    
                    if (searchTerm.length < 2) {
                        dropdown.classList.add('hidden');
                        return;
                    }
                    
                    searchTimeout = setTimeout(() => {
                        performSearch(searchTerm);
                    }, 300);
                });
                
                document.addEventListener('click', function(e) {
                    if (!searchForm.contains(e.target)) {
                        dropdown.classList.add('hidden');
                    }
                });
                
                function performSearch(term) {
                    dropdown.innerHTML = '<div class="p-4 text-center"><span class="material-symbols-outlined text-2xl text-blue-600 animate-spin" data-icon="refresh"></span></div>';
                    dropdown.classList.remove('hidden');
                    
                    fetch('<?php echo admin_url('admin-ajax.php'); ?>?action=warafy_live_search&s=' + encodeURIComponent(term))
                        .then(response => response.json())
                        .then(data => {
                            if (data.success && data.data.products && data.data.products.length > 0) {
                                displayResults(data.data.products);
                            } else {
                                dropdown.innerHTML = '<div class="p-4 text-center text-gray-500">No products found</div>';
                            }
                        })
                        .catch(error => {
                            dropdown.innerHTML = '<div class="p-4 text-center text-red-500">Search error</div>';
                        });
                }
                
                function displayResults(products) {
                    let html = products.map(product => `
                        <div class="search-result-item flex items-center gap-3 p-3 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" onclick="window.location.href='${product.url}'">
                            <img src="${product.image}" alt="${product.title}" class="w-12 h-12 object-cover rounded">
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate">${product.title}</h4>
                                <p class="text-sm text-gray-500">${product.price}</p>
                            </div>
                        </div>
                    `).join('');
                    
                    dropdown.innerHTML = html;
                }
            });
        }
        
        initSearchAutocomplete();
    });
    </script>
    
    <style>
    .warafy-search-dropdown { animation: fadeInDown 0.2s ease-out; z-index: 9999 !important; }
    @keyframes fadeInDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
    <?php
}
add_action('wp_footer', 'warafy_search_autocomplete_scripts');
