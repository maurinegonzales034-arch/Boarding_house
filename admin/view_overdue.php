<?php
session_start();
require_once '../includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

$my_code = $_SESSION['boarding_house_code'];

// Fetch ONLY users who are OVERDUE
$stmt = $conn->prepare("
    SELECT u.user_id, u.username, u.email, u.contact_number, 
           u.due_date, u.payment_status, r.room_number
    FROM users u
    LEFT JOIN rooms r ON u.assigned_room_id = r.room_id
    WHERE u.boarding_house_code = ? 
    AND u.role = 'user'
    AND u.due_date < CURDATE() 
    AND u.payment_status = 'Unpaid'
    ORDER BY u.due_date ASC
");
$stmt->execute([$my_code]);
$overdue_users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Overdue Payments</title>
    <style>
        body { font-family: Arial; margin: 30px; background: #f0f2f5; }
        .container { width: 90%; margin: auto; }
        h1 { color: #e74c3c; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        th { background: #e74c3c; color: white; padding: 15px; text-align: left; }
        td { border-bottom: 1px solid #ddd; padding: 12px 15px; }
        tr:hover { background: #fff5f5; }
        .overdue-badge { background: #ffdddd; color: #d63031; padding: 5px 10px; border-radius: 15px; font-weight: bold; }
        .back-link { display: inline-block; margin-bottom: 20px; color: #3498db; text-decoration: none; }
        .empty-msg { background: white; padding: 30px; text-align: center; color: gray; border-radius: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
        <h1>⚠️ Overdue Payments</h1>

        <?php if (count($overdue_users) > 0): ?>
            <table>
                <tr>
                    <th>USERNAME</th>
                    <th>ROOM</th>
                    <th>DUE DATE</th>
                    <th>CONTACT</th>
                    <th>STATUS</th>
                </tr>
                <?php foreach ($overdue_users as $user): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($user['username']); ?></strong></td>
                    <td><?php echo htmlspecialchars($user['room_number']); ?></td>
                    <td><?php echo date('F j, Y', strtotime($user['due_date'])); ?></td>
                    <td><?php echo htmlspecialchars($user['contact_number']); ?></td>
                    <td><span class="overdue-badge">OVERDUE</span></td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <div class="empty-msg">✅ Great! No overdue payments at the moment.</div>
        <?php endif; ?>
    </div>
</body>
</html>