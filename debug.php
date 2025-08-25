<?php
/**
 * Debug file to test base URL detection
 * Access this file on both localhost and Raspberry Pi to see what URLs are being generated
 */

$config = require __DIR__ . '/config/config.php';

echo "<h1>Base URL Debug Information</h1>";
echo "<h2>Generated Base URL:</h2>";
echo "<p><strong>" . $config['app']['base_url'] . "</strong></p>";

echo "<h2>Server Variables:</h2>";
echo "<ul>";
echo "<li><strong>HTTP_HOST:</strong> " . ($_SERVER['HTTP_HOST'] ?? 'NOT SET') . "</li>";
echo "<li><strong>SCRIPT_NAME:</strong> " . ($_SERVER['SCRIPT_NAME'] ?? 'NOT SET') . "</li>";
echo "<li><strong>REQUEST_URI:</strong> " . ($_SERVER['REQUEST_URI'] ?? 'NOT SET') . "</li>";
echo "<li><strong>HTTPS:</strong> " . (isset($_SERVER['HTTPS']) ? $_SERVER['HTTPS'] : 'NOT SET') . "</li>";
echo "</ul>";

echo "<h2>Test Links:</h2>";
echo "<ul>";
echo "<li><a href='" . $config['app']['base_url'] . "/'>Home Page</a></li>";
echo "<li><a href='" . $config['app']['base_url'] . "/about.php'>About Page</a></li>";
echo "<li><a href='" . $config['app']['base_url'] . "/events.php'>Events Page</a></li>";
echo "<li><a href='" . $config['app']['base_url'] . "/join.php'>Join Page</a></li>";
echo "<li><a href='" . $config['app']['base_url'] . "/assets/css/global.css'>CSS File</a></li>";
echo "</ul>";

echo "<h2>Expected URLs:</h2>";
echo "<p><strong>Localhost:</strong> http://localhost/hcc-csa-website</p>";
echo "<p><strong>Raspberry Pi:</strong> http://10.0.0.139/ (if in root) or http://10.0.0.139/hcc-csa-website/ (if in subdirectory)</p>";
?>
