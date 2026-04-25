<?php
$host = "localhost";
$user = "root";
$pass = "";
$db_name = "boarding_house"; // <--- FIXED HERE

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Database Connection Failed: " . $e->getMessage();
    exit;
}
?>