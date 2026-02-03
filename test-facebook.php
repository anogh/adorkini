<?php
/**
 * Test page for Facebook App Browser detection
 */

// Include WordPress
require_once('../../../wp-config.php');

// Test Facebook detection
function test_facebook_detection() {
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $referrer = $_SERVER['HTTP_REFERER'] ?? '';
    
    echo "<h2>Facebook Detection Test</h2>";
    echo "<p><strong>User Agent:</strong> " . htmlspecialchars($user_agent) . "</p>";
    echo "<p><strong>Referrer:</strong> " . htmlspecialchars($referrer) . "</p>";
    
    $is_facebook_browser = (
        strpos($user_agent, 'FBAN') !== false ||
        strpos($user_agent, 'FBAV') !== false ||
        strpos($user_agent, 'Instagram') !== false ||
        strpos($user_agent, 'FB_IAB') !== false ||
        strpos($user_agent, 'FBIOS') !== false ||
        strpos($referrer, 'facebook.com') !== false ||
        strpos($referrer, 'fb.me') !== false ||
        isset($_GET['fbclid']) ||
        (isset($_GET['utm_source']) && $_GET['utm_source'] === 'facebook')
    );
    
    echo "<p><strong>Facebook Browser Detected:</strong> " . ($is_facebook_browser ? 'YES' : 'NO') . "</p>";
    
    if ($is_facebook_browser) {
        echo "<div style='background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin: 10px 0;'>";
        echo "✅ Facebook browser detected - Preloader should be disabled and content should load immediately.";
        echo "</div>";
    } else {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin: 10px 0;'>";
        echo "❌ Facebook browser not detected - Normal preloader behavior.";
        echo "</div>";
    }
    
    // Test URL parameters
    echo "<h3>URL Parameters:</h3>";
    foreach ($_GET as $key => $value) {
        echo "<p><strong>$key:</strong> " . htmlspecialchars($value) . "</p>";
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facebook Browser Detection Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-section { border: 1px solid #ddd; padding: 20px; margin: 20px 0; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; }
        .info { background: #d1ecf1; color: #0c5460; }
    </style>
</head>
<body>
    <h1>Facebook App Browser Detection Test</h1>
    
    <div class="test-section info">
        <h2>How to Test</h2>
        <ol>
            <li>Open this page in Facebook mobile app</li>
            <li>Test with: <a href="?fbclid=test">?fbclid=test</a></li>
            <li>Test with: <a href="?utm_source=facebook">?utm_source=facebook</a></li>
        </ol>
    </div>
    
    <?php test_facebook_detection(); ?>
    
    <div class="test-section">
        <h2>Content Visibility Test</h2>
        <div id="test-content" style="background: #f0f0f0; padding: 20px; border-radius: 5px;">
            <p>This content should be visible immediately in Facebook browser.</p>
            <p>If you can see this, the fix is working!</p>
        </div>
    </div>
    
    <script>
        // Test JavaScript detection
        console.log('Testing Facebook browser detection...');
        
        var ua = navigator.userAgent || navigator.vendor || '';
        var isFacebookBrowser = ua.indexOf('FBAN') > -1 || ua.indexOf('FBAV') > -1 || ua.indexOf('Instagram') > -1 || ua.indexOf('FB_IAB') > -1 || ua.indexOf('FBIOS') > -1;
        
        console.log('User Agent:', ua);
        console.log('Facebook Browser (JS):', isFacebookBrowser);
        
        if (isFacebookBrowser) {
            document.body.style.backgroundColor = '#e8f5e8';
            document.getElementById('test-content').style.border = '2px solid green';
        }
    </script>
</body>
</html>
