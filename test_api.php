<?php
/**
 * Test the join API to see what it's returning
 * DELETE THIS FILE AFTER DEBUGGING
 */

echo "<h2>Testing Join API</h2>";

// Test data
$testData = [
    'csrf_token' => 'test',
    'first_name' => 'Test',
    'last_name' => 'User',
    'email' => 'test@example.com',
    'year_level' => 'Freshman',
    'major' => 'Computer Science',
    'campus' => 'Central',
    'accepted_code' => '1',
    'consent_privacy' => '1',
    'consent_comms' => '1'
];

// Simulate AJAX request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost/hcc-csa-website/api/join.php');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($testData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'X-Requested-With: XMLHttpRequest'
]);

echo "<h3>Sending test request...</h3>";
echo "<pre>Data: " . print_r($testData, true) . "</pre>";

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<h3>Response:</h3>";
echo "<p><strong>HTTP Code:</strong> $httpCode</p>";
echo "<p><strong>Response Content:</strong></p>";
echo "<pre>" . htmlspecialchars($response) . "</pre>";

// Try to decode as JSON
$jsonData = json_decode($response, true);
if ($jsonData) {
    echo "<h3>Parsed JSON:</h3>";
    echo "<pre>" . print_r($jsonData, true) . "</pre>";
} else {
    echo "<h3>❌ Response is not valid JSON</h3>";
    echo "<p>JSON Error: " . json_last_error_msg() . "</p>";
}

echo "<br><p><strong>🚨 Delete this file (test_api.php) after debugging!</strong></p>";
?>
