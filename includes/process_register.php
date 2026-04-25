<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password']; // In real app, use password_hash()
    $role = $_POST['role'];

    // Check if username exists
    $check = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
    $check->execute([$username]);
    
    if ($check->rowCount() > 0) {
        header("Location: ../register.php?error=Username already taken");
        exit();
    }

    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->execute([$username, $password, $role]);

    // Set session
    $_SESSION['user_id'] = $conn->lastInsertId();
    $_SESSION['username'] = $username;
    $_SESSION['role'] = $role;

    // REDIRECT TO MAIN DASHBOARD
    header("Location: ../dashboard.php");
    exit();
}
?>