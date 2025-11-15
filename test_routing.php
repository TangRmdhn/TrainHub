<?php
// File: test_routing.php
// Test routing & .htaccess

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Routing Test</title>
    <style>
        body { font-family: monospace; background: #1a1a1a; color: #0f0; padding: 20px; }
        .test { background: #2a2a2a; padding: 15px; margin: 10px 0; border-left: 4px solid #f60; }
        .pass { border-color: #0f0; }
        .fail { border-color: #f00; }
        pre { background: #0a0a0a; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🔥 TrainHub Routing Test</h1>
    
    <?php
    // Test 1: Check .htaccess exists
    echo '<div class="test ' . (file_exists('.htaccess') ? 'pass' : 'fail') . '">';
    echo '<h3>Test 1: .htaccess File</h3>';
    echo file_exists('.htaccess') ? '✅ EXISTS' : '❌ NOT FOUND';
    echo '</div>';
    
    // Test 2: Check mod_rewrite
    echo '<div class="test ' . (function_exists('apache_get_modules') && in_array('mod_rewrite', apache_get_modules()) ? 'pass' : 'fail') . '">';
    echo '<h3>Test 2: mod_rewrite Module</h3>';
    if (function_exists('apache_get_modules')) {
        echo in_array('mod_rewrite', apache_get_modules()) ? '✅ ENABLED' : '❌ DISABLED';
    } else {
        echo '⚠️ Cannot detect (might be enabled)';
    }
    echo '</div>';
    
    // Test 3: API endpoint availability
    echo '<div class="test">';
    echo '<h3>Test 3: API Endpoint Test</h3>';
    echo '<button onclick="testAPI()">Test API</button>';
    echo '<pre id="apiResult">Click button to test...</pre>';
    echo '</div>';
    
    // Test 4: Check PHP extensions
    echo '<div class="test ' . (extension_loaded('mysqli') && extension_loaded('json') && extension_loaded('mbstring') ? 'pass' : 'fail') . '">';
    echo '<h3>Test 4: PHP Extensions</h3>';
    echo 'mysqli: ' . (extension_loaded('mysqli') ? '✅' : '❌') . '<br>';
    echo 'json: ' . (extension_loaded('json') ? '✅' : '❌') . '<br>';
    echo 'mbstring: ' . (extension_loaded('mbstring') ? '✅' : '❌') . '<br>';
    echo '</div>';
    
    // Test 5: Check Composer autoload
    echo '<div class="test ' . (file_exists('api/vendor/autoload.php') ? 'pass' : 'fail') . '">';
    echo '<h3>Test 5: Composer Dependencies</h3>';
    echo file_exists('api/vendor/autoload.php') ? '✅ INSTALLED' : '❌ RUN: composer install';
    echo '</div>';
    
    // Test 6: Database connection
    echo '<div class="test">';
    echo '<h3>Test 6: Database Connection</h3>';
    try {
        require_once 'api/config/database.php';
        echo is_db_connected() ? '✅ CONNECTED' : '⚠️ DISCONNECTED';
    } catch (Exception $e) {
        echo '❌ ERROR: ' . $e->getMessage();
    }
    echo '</div>';
    ?>
    
    <script>
        async function testAPI() {
            const result = document.getElementById('apiResult');
            result.textContent = 'Testing...';
            
            try {
                const response = await fetch('/trainhub/api/auth/login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email: '', password: '' })
                });
                
                const contentType = response.headers.get('Content-Type');
                const text = await response.text();
                
                result.textContent = 
                    'Status: ' + response.status + '\n' +
                    'Content-Type: ' + contentType + '\n' +
                    'Response:\n' + text.substring(0, 500);
                
                if (contentType && contentType.includes('application/json')) {
                    result.textContent += '\n\n✅ API RETURNING JSON';
                } else {
                    result.textContent += '\n\n❌ API RETURNING HTML (ROUTING ISSUE!)';
                }
            } catch (error) {
                result.textContent = '❌ ERROR: ' + error.message;
            }
        }
    </script>
</body>
</html>