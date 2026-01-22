<?php
// Upload this file to your public_html folder and visit it at your-domain.com/db_check.php

// -----------------------------------------------------------------------------
// INSTRUCTIONS:
// 1. Open this file in your File Manager.
// 2. FILL IN THE VALUES BELOW with exactly what you have in your .env file or Control Panel.
// -----------------------------------------------------------------------------

$host     = 'sqlXXX.epizy.com';   // CHANGE THIS (MySQL Hostname)
$username = 'epiz_XXXXXXX';       // CHANGE THIS (MySQL Username)
$password = 'your_password';      // CHANGE THIS (MySQL Password)
$database = 'epiz_XXXXXXX_db';    // CHANGE THIS (Database Name)

// -----------------------------------------------------------------------------

echo "<h1>Database Connection Test</h1>";
echo "<p>Testing connection to <strong>$host</strong> over port 3306...</p>";

try {
    $dsn = "mysql:host=$host;port=3306;dbname=$database;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    $pdo = new PDO($dsn, $username, $password, $options);
    
    echo "<h2 style='color: green;'>✅ SUCCESS!</h2>";
    echo "<p>Connected successfully to database: <strong>$database</strong></p>";
    
} catch (\PDOException $e) {
    echo "<h2 style='color: red;'>❌ FAILED</h2>";
    echo "<p>Could not connect to database.</p>";
    echo "<h3>Error Details:</h3>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
    
    echo "<h3>Troubleshooting Tips:</h3>";
    echo "<ul>";
    echo "<li><strong>Host:</strong> Ensure 'MySQL Hostname' is correct. It is NOT 'localhost' or '127.0.0.1'. Check your Control Panel.</li>";
    echo "<li><strong>Password:</strong> Ensure your password is correct. It is usually your hosting account password (or VPanel password).</li>";
    echo "<li><strong>Database/User:</strong> Ensure the prefix (e.g., epiz_12345_) matches exactly.</li>";
    echo "</ul>";
}
