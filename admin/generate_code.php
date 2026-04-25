<?php
session_start();
require_once '../includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Generate new random code
$new_code = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));

// 1. Get old code first
$old_code = $_SESSION['boarding_house_code'];

// 2. Update Admin's code
$stmt = $conn->prepare("UPDATE users SET boarding_house_code = ? WHERE user_id = ?");
$stmt->execute([$new_code, $_SESSION['user_id']]);

// 3. Update ALL USERS under this admin to the NEW code
$stmt = $conn->prepare("UPDATE users SET boarding_house_code = ? WHERE boarding_house_code = ? AND role = 'user'");
$stmt->execute([$new_code, $old_code]);

// 4. Update session
$_SESSION['boarding_house_code'] = $new_code;

// 5. Redirect back with success message
header("Location: dashboard.php?msg=✅ Code regenerated successfully! All users updated.");
exit();
?>