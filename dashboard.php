<?php
session_start();

// Security Check: If not logged in, go to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Redirect based on Role
if ($_SESSION['role'] == 'admin') {
    // Go to Admin Dashboard
    header("Location: admin/dashboard.php");
    exit();
} else {
    // Go to User Dashboard
    header("Location: user/dashboard.php");
    exit();
}
?>