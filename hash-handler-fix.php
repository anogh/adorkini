<?php
// Hash handler fix for account page
// This script adds the hash handler to functions.php

// Read current functions.php
$functions_file = file_get_contents('functions.php');

// Find the location to insert the hash handler (after the product carousel section)
$pattern = '/(\/\/ Enqueue product carousel script only on single product pages.*?}\);)/s';
if (preg_match($pattern, $functions_file, $matches)) {
    $existing_section = $matches[1];
    
    // Check if hash handler already exists
    if (strpos($functions_file, 'Add hash handler for my-account page') === false) {
        // Add the hash handler code
        $hash_handler_code = '
    
    // Add hash handler for my-account page
    if (is_page(\'my-account\')) {
        wp_add_inline_script(\'tailwind\', \'
            (function() {
                const handleHash = () => {
                    const hash = window.location.hash;
                    if (!hash) return;
                    
                    const hashValue = hash.substring(1);
                    const hashMap = {
                        "account-details": "personal-info",
                        "edit-account": "personal-info", 
                        "personal-info": "personal-info",
                        "orders": "orders",
                        "edit-address": "addresses",
                        "addresses": "addresses"
                    };

                    const view = hashMap[hashValue];
                    if (view) {
                        window.location.replace("\' . home_url(\'/my-account\') . \'?view=" + view);
                    }
                };

                // Run immediately and also after DOM is ready
                handleHash();
                if (document.readyState === "loading") {
                    document.addEventListener("DOMContentLoaded", handleHash);
                } else {
                    handleHash();
                }
                window.addEventListener("hashchange", handleHash);
            })();
        \');
    }';
        
        // Insert the hash handler after the existing section
        $new_functions_content = str_replace($existing_section, $existing_section . $hash_handler_code, $functions_file);
        
        // Write back to functions.php
        file_put_contents('functions.php', $new_functions_content);
        
        echo "Hash handler added successfully to functions.php\n";
    } else {
        echo "Hash handler already exists in functions.php\n";
    }
} else {
    echo "Could not find the right location to insert hash handler\n";
}

echo "Done!\n";
?>
