<?php
session_start();
require_once '../includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

$msg = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_num = $_POST['room_number'];
    $price = $_POST['price'];
    
    $stmt = $conn->prepare("INSERT INTO rooms (admin_id, room_number, monthly_rent) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $room_num, $price]);
    
    $msg = "Room Added Successfully!";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Room</title>
    <style>
        .form-box { width: 300px; margin: 50px auto; padding: 20px; background: white; border-radius: 8px; }
        input { width: 100%; padding: 10px; margin: 10px 0; }
        button { background: green; color: white; padding: 10px; border: none; width: 100%; }
    </style>
</head>
<body>
    <div class="form-box">
        <h2>Add New Room</h2>
        <?php if ($msg): ?><p style="color:green;"><?php echo $msg; ?></p><?php endif; ?>
        <form method="POST">
            <label>Room Number:</label>
            <input type="text" name="room_number" required placeholder="e.g. 101">
            <label>Monthly Rent Price:</label>
            <input type="number" name="price" required placeholder="e.g. 3000">
            <button type="submit">Save Room</button>
        </form>
        <br><a href="dashboard.php">← Back</a>
    </div>
</body>
</html>