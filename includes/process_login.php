<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password']; // This is what user typed

    // Find user in database
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user) {
        // --- IMPORTANT CHECK ---
        // If you did NOT use password_hash when registering, use this line:
        if ($password == $user['password']) { 
            
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['boarding_house_code'] = $user['boarding_house_code'];
            $_SESSION['assigned_room_id'] = $user['assigned_room_id'];

            header("Location: ../dashboard.php");
            exit();
        } else {
            header("Location: ../login.php?error=Incorrect Password");
            exit();
        }

    } else {
        header("Location: ../login.php?error=Username not found");
        exit();
    }
}
?>