<?php
session_start();
require_once '../includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// --- PROCESS UPDATE PAYMENT ---
if (isset($_POST['update_payment'])) {
    $user_id = $_POST['user_id'];
    $due_date = $_POST['due_date'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE users SET due_date = ?, payment_status = ? WHERE user_id = ?");
    $stmt->execute([$due_date, $status, $user_id]);
    
    header("Location: view_all_users.php?msg=updated");
    exit();
}

// Fetch all users WITH ROOM NUMBER AND CONTACT INFO
$my_code = $_SESSION['boarding_house_code'];
$stmt = $conn->prepare("
    SELECT u.user_id, u.username, u.email, u.contact_number, u.guardian_number, u.home_location, 
           u.due_date, u.payment_status, u.assigned_room_id, r.room_number
    FROM users u
    LEFT JOIN rooms r ON u.assigned_room_id = r.room_id
    WHERE u.boarding_house_code = ? AND u.role = 'user'
    ORDER BY u.username ASC
");
$stmt->execute([$my_code]);
$users = $stmt->fetchAll(); // This creates the $users array
?>
<!DOCTYPE html>
<html>
<head>
    <title>All Users</title>
    <style>
        body { font-family: Arial; margin: 30px; }
        .container { width: 90%; margin: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #8e44ad; color: white; padding: 15px; font-size: 16px; }
        td { border: 1px solid #ddd; padding: 12px; }
        .paid { color: green; font-weight: bold; }
        .unpaid { color: red; font-weight: bold; }
        .room-tag { background: #eee; padding: 5px 10px; border-radius: 15px; font-weight: bold; display: inline-block; }
        .btn-manage { background: #17a2b8; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; }
        .form-box { background: #f9f9f9; padding: 15px; border-radius: 8px; margin-top: 10px; }
        .details-box { background: #eef2f5; padding: 15px; border-radius: 8px; margin-top: 10px; line-height: 1.8; }
        input, select { padding: 8px; margin: 5px; }
        .btn-save { background: #28a745; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="container">
        <h1>👥 Manage Users & Payments</h1>
        <a href="dashboard.php">← Back to Dashboard</a>

        <table>
            <tr>
                <th>USERNAME</th>
                <th>ROOM NUMBER</th>
                <th>DUE DATE</th>
                <th>STATUS</th>
                <th>ACTIONS</th>
            </tr>
            <?php if (count($users) > 0): ?>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($user['username']); ?></strong></td>
                    <td>
                        <?php if ($user['room_number']): ?>
                            <span class="room-tag">🏠 <?php echo htmlspecialchars($user['room_number']); ?></span>
                        <?php else: ?>
                            <span style="color:gray;">--- No Room ---</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $user['due_date'] ? date('F j, Y', strtotime($user['due_date'])) : 'Not Set'; ?></td>
                    <td>
                        <span class="<?php echo strtolower($user['payment_status']); ?>">
                            <?php echo htmlspecialchars($user['payment_status']); ?>
                        </span>
                    </td>
                    <td>
                        <button class="btn-manage" onclick="document.getElementById('details-<?php echo $user['user_id']; ?>').style.display='block'">View Info</button>
                        
                        <div id="details-<?php echo $user['user_id']; ?>" class="details-box" style="display:none;">
                            <p><strong>📧 Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                            <p><strong>📱 My Number:</strong> <?php echo htmlspecialchars($user['contact_number']); ?></p>
                            <p><strong>👨‍👩‍👧 Guardian Number:</strong> <?php echo htmlspecialchars($user['guardian_number']); ?></p>
                            <p><strong>🏠 Home Address:</strong> <?php echo htmlspecialchars($user['home_location']); ?></p>
                        </div>

                        <button class="btn-manage" onclick="document.getElementById('form-<?php echo $user['user_id']; ?>').style.display='block'">Manage Payment</button>
                        
                        <div id="form-<?php echo $user['user_id']; ?>" class="form-box" style="display:none;">
                            <form method="POST">
                                <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                
                                <label>Set Due Date:</label>
                                <input type="date" name="due_date" value="<?php echo $user['due_date']; ?>" required>
                                
                                <label>Status:</label>
                                <select name="status">
                                    <option value="Paid" <?php if($user['payment_status']=='Paid') echo 'selected'; ?>>Paid</option>
                                    <option value="Unpaid" <?php if($user['payment_status']=='Unpaid') echo 'selected'; ?>>Unpaid</option>
                                </select>
                                
                                <button type="submit" name="update_payment" class="btn-save">Save Changes</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align:center; padding:30px; color:gray;">No users have joined yet.</td>
                </tr>
            <?php endif; ?>
        </table>

    </div>
</body>
</html>