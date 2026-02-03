<?php
if (!defined('ABSPATH')) {
    exit;
}

class Warafy_Session_Manager {
    
    private static $instance = null;
    private $session_id = null;
    private $cookie_name = 'warafy_session_id';
    private $expiration = 3600; // 1 hour in seconds

    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {
        $this->init_session();
    }

    private function init_session() {
        if (is_admin() || (defined('DOING_AJAX') && DOING_AJAX && !isset($_COOKIE[$this->cookie_name]))) {
            return;
        }

        if (isset($_COOKIE[$this->cookie_name])) {
            $this->session_id = sanitize_text_field($_COOKIE[$this->cookie_name]);
        } else {
            $this->session_id = $this->generate_session_id();
            // Set cookie for 1 hour
            setcookie($this->cookie_name, $this->session_id, time() + $this->expiration, COOKIEPATH, COOKIE_DOMAIN);
        }
    }

    private function generate_session_id() {
        return md5(uniqid(rand(), true));
    }

    public function get_homepage_data() {
        if (!$this->session_id) {
            return $this->generate_homepage_data(); // Fallback if no cookie
        }

        $transient_key = 'warafy_home_' . $this->session_id;
        $data = get_transient($transient_key);

        if (false === $data) {
            $data = $this->generate_homepage_data();
            set_transient($transient_key, $data, $this->expiration);
        }

        return $data;
    }

    private function generate_homepage_data() {
        // 1. Get All Top-Level Categories
        $categories = get_terms([
            'taxonomy' => 'product_cat',
            'hide_empty' => true,
            'parent' => 0,
        ]);

        // 2. Select 5 Random Categories
        $selected_cats = [];
         if (!empty($categories) && !is_wp_error($categories)) {
            $cats_pool = $categories;
            shuffle($cats_pool);
            $selected_cats = array_slice($cats_pool, 0, 5);
        }

        // 3. Select 8 Random Products for each Category
        $category_sections = [];
        foreach ($selected_cats as $cat) {
            $product_ids = get_posts([
                'post_type' => 'product',
                'posts_per_page' => 8,
                'orderby' => 'rand',
                'fields' => 'ids',
                'post_status' => 'publish',
                'tax_query' => [[
                    'taxonomy' => 'product_cat',
                    'field' => 'term_id',
                    'terms' => $cat->term_id
                ]],
            ]);
            
            $category_sections[] = [
                'term' => $cat,
                'product_ids' => $product_ids
            ];
        }

        // 4. Generate Recommended Products List (All Products Shuffled)
        $all_product_ids = get_posts([
            'post_type' => 'product',
            'posts_per_page' => -1, // Get all
            'fields' => 'ids',
            'post_status' => 'publish',
        ]);
        
        if ($all_product_ids) {
            shuffle($all_product_ids);
        } else {
            $all_product_ids = [];
        }

        return [
            'category_sections' => $category_sections,
            'recommended_ids' => $all_product_ids
        ];
    }

    public function get_recommended_products($page, $per_page = 8) {
        $data = $this->get_homepage_data();
        $recommended_ids = $data['recommended_ids'];
        
        $total_products = count($recommended_ids);
        $offset = ($page - 1) * $per_page;
        
        if ($offset >= $total_products) {
            return []; // No more products
        }

        $current_page_ids = array_slice($recommended_ids, $offset, $per_page);
        
        if (empty($current_page_ids)) {
            return [];
        }

        // Fetch product objects in specific order
        $args = [
            'post_type' => 'product',
            'post__in' => $current_page_ids,
            'orderby' => 'post__in',
            'posts_per_page' => $per_page // Should match count of IDs
        ];

        $query = new WP_Query($args);
        return $query->posts;
    }
}
