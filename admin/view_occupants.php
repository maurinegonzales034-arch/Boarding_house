<?php
session_start();
require_once '../includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// --- PROCESS REMOVE USER ---
if (isset($_GET['remove_user'])) {
    $user_id = $_GET['remove_user'];
    
    // Remove room assignment
    $stmt = $conn->prepare("UPDATE users SET assigned_room_id = NULL WHERE user_id = ?");
    $stmt->execute([$user_id]);
    
    header("Location: view_occupants.php?msg=user_removed");
    exit();
}

// --- PROCESS DELETE ROOM ---
if (isset($_GET['delete_room'])) {
    $room_id = $_GET['delete_room'];
    // First remove occupants
    $stmt = $conn->prepare("UPDATE users SET assigned_room_id = NULL WHERE assigned_room_id = ?");
    $stmt->execute([$room_id]);
    // Then delete room
    $stmt = $conn->prepare("DELETE FROM rooms WHERE room_id = ?");
    $stmt->execute([$room_id]);
    header("Location: view_occupants.php");
    exit();
}

// Fetch rooms and users
$stmt = $conn->prepare("
    SELECT r.room_id, r.room_number, r.monthly_rent, u.username, u.user_id as occupant_id
    FROM rooms r
    LEFT JOIN users u ON r.room_id = u.assigned_room_id
    WHERE r.admin_id = ?
    ORDER BY r.room_number ASC
");
$stmt->execute([$_SESSION['user_id']]);
$rooms = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Room Status</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        .container { width: 90%; margin: auto; }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px; 
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        th, td { 
            border: 1px solid #ddd; 
            padding: 15px; 
            text-align: left; 
        }
        th { 
            background: #2c3e50; 
            color: white; 
            font-size: 16px;
        }
        tr:nth-child(even) { background-color: #f2f2f2; }
        tr:hover { background-color: #ddd; }
        .occupied { 
            color: #27ae60; 
            font-weight: bold; 
        }
        .vacant { 
            color: #e74c3c; 
            font-style: italic;
        }
        .room-num {
            font-weight: bold;
            font-size: 16px;
        }
        .btn {
            padding: 6px 12px;
            text-decoration: none;
            color: white;
            border-radius: 4px;
            font-size: 13px;
            margin: 2px;
            display: inline-block;
        }
        .btn-red { background: #e74c3c; }
        .btn-dark { background: #343a40; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🏠 Room Occupants</h1>
        <a href="dashboard.php" style="font-size:16px;">← Back to Dashboard</a>
        
        <table>
            <tr>
                <th>ROOM NUMBER</th>
                <th>RENT PRICE</th>
                <th>OCCUPANT NAME</th>
                <th>ACTIONS</th>
            </tr>
            <?php foreach ($rooms as $room): ?>
            <tr>
                <td class="room-num"><?php echo htmlspecialchars($room['room_number']); ?></td>
                <td>$<?php echo htmlspecialchars(number_format($room['monthly_rent'], 2)); ?></td>
                <td>
                    <?php if ($room['username']): ?>
                        <span class="occupied">✅ <?php echo htmlspecialchars($room['username']); ?></span>
                    <?php else: ?>
                        <span class="vacant">❌ Vacant / Available</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($room['username']): ?>
                        <a href="view_occupants.php?remove_user=<?php echo $room['occupant_id']; ?>" 
                           class="btn btn-red" 
                           onclick="return confirm('⚠️ Are you sure you want to REMOVE <?php echo $room['username']; ?> from this room? They will have to choose again.')">
                           🚪 Remove User
                        </a>
                    <?php endif; ?>

                    <a href="view_occupants.php?delete_room=<?php echo $room['room_id']; ?>" 
                       class="btn btn-dark" 
                       onclick="return confirm('DELETE Room <?php echo $room['room_number']; ?>?')">
                       🗑️ Delete Room
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>