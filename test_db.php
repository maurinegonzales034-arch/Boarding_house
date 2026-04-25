<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "Attempting to include db_connect.php...<br>";

// Make sure the path is correct relative to this file
require_once 'includes/db_connect.php';

echo "db_connect.php included.<br>";

// Try a simple query to verify the connection
try {
    $stmt = $conn->prepare("SELECT 1");
    $stmt->execute();
    $result = $stmt->fetchColumn();
    if ($result == 1) {
        echo "Database connection successful! MySQL is responding.<br>";
    } else {
        echo "Database query executed, but returned unexpected result.<br>";
    }
} catch (PDOException $e) {
    echo "Database Connection FAILED: " . htmlspecialchars($e->getMessage()) . "<br>";
    // Log this error properly in production
}

echo "DB connection test finished.";
?>