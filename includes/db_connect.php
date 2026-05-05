<?php
// --- Database Connection Configuration ---

// Database credentials - Ensure these are EXACTLY as provided by your hosting provider
$host = "sql311.infinityfree.com"; // Your MySQL server hostname
$port = "10272";                   // The specific port number for your MySQL connection
$user = "if0_41840070";           // Your MySQL username
$pass = "j6PLS5bPYD5LZP";           // Your MySQL password
$db_name = "if0_41840070_bh_db";   // The name of your database

// --- Enable Error Reporting for Debugging (Remove/Comment Out in Production) ---
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
// --- End Debugging ---


// --- Establish Database Connection using PDO ---
try {
    // Construct the DSN (Data Source Name) string
    // Includes host, port, database name, and character set for the connection
    $dsn = "mysql:host=$host;port=$port;dbname=$db_name;charset=utf8";

    // Create a new PDO instance (connect to the database)
    $conn = new PDO($dsn, $user, $pass);

    // Set PDO attributes for error handling and fetching
    // ERRMODE_EXCEPTION: Throws exceptions on errors, making them easier to catch
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // DEFAULT_FETCH_MODE: Sets the default fetch mode to associative arrays (column names as keys)
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // If the connection is successful, the script continues. 
    // You can add a success message here for testing if needed, but remove it for production.
    // echo "Database connected successfully!";

} catch(PDOException $e) {
    // --- Handle Connection Errors ---
    // Log the detailed error message for server-side analysis
    error_log("Database Connection Error: " . $e->getMessage() . " | Host: $host, Port: $port, DB: $db_name, User: $user");
    
    // Display a user-friendly message to the visitor
    // Avoid showing detailed error messages to end-users for security reasons
    die("We are currently experiencing issues connecting to the database. Please try again later."); 
    // 'die()' stops script execution and displays the message.
}

// --- End of Database Connection ---

// Now, the $conn variable holds your active database connection object.
// You can include this file in other PHP scripts using:
// require_once 'includes/db_connect.php'; 
?>
