<?php
require_once __DIR__ . '/mocks.php';
require_once __DIR__ . '/../inc/bengali-language.php';

function test_warafy_get_translations() {
    $temp_dir = __DIR__ . '/temp_translations';
    if (!is_dir($temp_dir)) {
        mkdir($temp_dir);
    }
    putenv("TEMPLATE_DIR=$temp_dir");

    echo "Running warafy_get_translations tests...\n";

    // Scenario 1: File does not exist
    $json_file = $temp_dir . '/translations.json';
    if (file_exists($json_file)) {
        unlink($json_file);
    }

    $result = warafy_get_translations();
    if (!is_array($result) || !empty($result)) {
        throw new Exception("Scenario 1 Failed: Should return empty array when file missing. Got: " . print_r($result, true));
    }
    echo "✓ Passed: Scenario 1 (File missing)\n";

    // Scenario 2: Valid JSON file
    $test_data = ['hello' => 'ওহে', 'world' => 'বিশ্ব'];
    file_put_contents($json_file, json_encode($test_data));

    $result = warafy_get_translations();
    if ($result !== $test_data) {
        throw new Exception("Scenario 2 Failed: Should return decoded JSON data. Got: " . print_r($result, true));
    }
    echo "✓ Passed: Scenario 2 (Valid JSON)\n";

    // Scenario 3: Invalid JSON file
    file_put_contents($json_file, '{ invalid json }');

    $result = warafy_get_translations();
    // json_decode returns null for invalid JSON
    if ($result !== null) {
        throw new Exception("Scenario 3 Failed: Should return null for invalid JSON. Got: " . print_r($result, true));
    }
    echo "✓ Passed: Scenario 3 (Invalid JSON)\n";

    // Cleanup
    if (file_exists($json_file)) {
        unlink($json_file);
    }
    rmdir($temp_dir);

    echo "All warafy_get_translations tests passed!\n";
}

// Run the test
try {
    test_warafy_get_translations();
} catch (Exception $e) {
    echo "Test failed: " . $e->getMessage() . "\n";
    exit(1);
}
