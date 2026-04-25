<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_number = trim($_POST['room_number'] ?? '');
    $monthly_rent = $_POST['monthly_rent'] ?? '';
    $due_date = $_POST['due_date'] ?? '';

    if (empty($room_number) || empty($monthly_rent) || empty($due_date)) {
        die('Invalid input');
    }

    $admin_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO rooms (admin_id, room_number, monthly_rent, due_date) VALUES (?, ?, ?, ?)");
    $stmt->execute([$admin_id, $room_number, $monthly_rent, $due_date]);

    header("Location: ../dashboard.php");
    exit();
}
?>