<?php
// Database credentials from your code
$host = "sql311.infinityfree.com"; // Hostname from your code
$user = "if0_41840070";           // Username from your code
$pass = "j6PLS5bPYD5LZP";           // Password from your code
$db_name = "if0_41840070_bh_db";   // Database name from your code
$port = "10272"; 

try {
    // Construct the DSN (Data Source Name) with the port
    // The format is "mysql:host=hostname;port=portnumber;dbname=database_name;charset=utf8"
    $conn = new PDO("mysql:host=$host;port=$port;dbname=$db_name;charset=utf8", $user, $pass);
    
    // Set PDO error mode to exception for better error handling
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Optional: You might want to set default fetch mode
    // $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // If connection is successful, you can optionally add a success message for debugging
    // echo "Database Connected Successfully!"; 

} catch(PDOException $e) {
    // Log the error or display a user-friendly message
    // For security, avoid displaying detailed error messages in a production environment
    error_log("Database Connection Failed: " . $e->getMessage()); // Log the detailed error
    echo "Database Connection Failed. Please try again later."; // User-friendly message
    exit; // Stop script execution if connection fails
}
?>
