<?php
session_start();
require_once '../includes/db_connect.php';

if (!isset($_GET['id']) || !isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$room_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Assign room to user
$stmt = $conn->prepare("UPDATE users SET assigned_room_id = ? WHERE user_id = ?");
$stmt->execute([$room_id, $user_id]);

$_SESSION['assigned_room_id'] = $room_id;

header("Location: dashboard.php");
exit();