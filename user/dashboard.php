<?php
session_start();
require_once '../includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit();
}

$username = $_SESSION['username'];
$code = $_SESSION['boarding_house_code'] ?? '';
$assigned_room = $_SESSION['assigned_room_id'] ?? null;

// Refresh data
$stmt = $conn->prepare("SELECT assigned_room_id, due_date, payment_status FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user_data = $stmt->fetch();

$_SESSION['assigned_room_id'] = $user_data['assigned_room_id'];
$assigned_room = $user_data['assigned_room_id'];

$due_date = $user_data['due_date'];
$payment_status = $user_data['payment_status'];

// Calculate days left
$days_left = "";
$alert_class = "";
$icon = "";
if ($due_date) {
    $now = new DateTime();
    $due = new DateTime($due_date);
    $interval = $now->diff($due);
    
    if ($now > $due) {
        $days_left = "⚠️ OVERDUE by " . $interval->days . " days!";
        $alert_class = "overdue";
        $icon = "🔴";
    } elseif ($interval->days <= 5) {
        $days_left = "⏳ Coming Soon! Only " . $interval->days . " days left.";
        $alert_class = "warning";
        $icon = "🟡";
    } else {
        $days_left = "✅ All Good!";
        $alert_class = "safe";
        $icon = "🟢";
    }
}

// Redirect if no code
if (empty($code)) {
    header("Location: enter_code.php");
    exit();
}

// Fetch rooms
$rooms = [];
if ($assigned_room == null) {
    $stmt = $conn->prepare("
        SELECT r.room_id, r.room_number, r.monthly_rent 
        FROM rooms r
        JOIN users admin ON r.admin_id = admin.user_id
        WHERE admin.boarding_house_code = ?
    ");
    $stmt->execute([$code]);
    $rooms = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body { 
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); 
            min-height: 100vh;
        }
        header { 
            background: linear-gradient(to right, #2c3e50, #34495e); 
            color: white; 
            padding: 25px 30px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        .welcome-text h1 {
            font-size: 28px;
            margin-bottom: 5px;
        }
        .welcome-text p {
            font-size: 16px;
            opacity: 0.9;
        }
        .code-box {
            background: rgba(255,255,255,0.1);
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .logout-btn {
            background: #e74c3c;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 25px;
            font-weight: bold;
            transition: background 0.3s;
        }
        .logout-btn:hover {
            background: #c0392b;
        }
        .container { 
            width: 90%; 
            max-width: 1100px;
            margin: 30px auto; 
        }
        
        /* --- PROFILE BUTTON --- */
        .profile-btn-container {
            text-align: right;
            margin-bottom: 20px;
        }
        .profile-btn {
            background: linear-gradient(to right, #8e44ad, #9b59b6);
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 25px;
            font-weight: bold;
            display: inline-block;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .profile-btn:hover {
            opacity: 0.9;
        }

        /* --- PAYMENT CARD --- */
        .payment-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            border-left: 8px solid;
        }
        .overdue { border-color: #e74c3c; background: #fff5f5; }
        .warning { border-color: #f39c12; background: #fff9e6; }
        .safe    { border-color: #27ae60; background: #f2fff7; }
        .info    { border-color: #3498db; background: #f0f8ff; }

        .payment-card h3 {
            font-size: 20px;
            margin-bottom: 15px;
            color: #333;
        }
        .detail-row {
            display: flex;
            align-items: center;
            margin: 10px 0;
            font-size: 18px;
        }
        .label {
            font-weight: bold;
            min-width: 120px;
            color: #555;
        }
        .value {
            font-weight: 600;
        }
        .status-paid { color: #27ae60; }
        .status-unpaid { color: #e74c3c; }

        /* --- ROOMS SECTION --- */
        .rooms-section h2 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 24px;
            border-bottom: 3px solid #3498db;
            padding-bottom: 10px;
            display: inline-block;
        }
        .rooms-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .room-card { 
            background: white; 
            border-radius: 15px;
            padding: 25px; 
            width: 280px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s, box-shadow 0.3s;
            border: 1px solid #eee;
        }
        .room-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        .room-number {
            font-size: 32px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 15px;
        }
        .room-price {
            font-size: 20px;
            color: #27ae60;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .btn-select { 
            background: linear-gradient(to right, #27ae60, #2ecc71);
            color: white; 
            padding: 12px 25px; 
            text-decoration: none; 
            border-radius: 25px;
            font-weight: bold;
            display: inline-block;
            transition: opacity 0.3s;
        }
        .btn-select:hover {
            opacity: 0.9;
        }
        .assigned-message {
            background: #d4edda;
            color: #155724;
            padding: 15px 20px;
            border-radius: 10px;
            font-size: 18px;
            font-weight: 500;
        }
    </style>
</head>
<body>

<header>
    <div class="header-top">
        <div class="welcome-text">
            <h1>Welcome, <?php echo htmlspecialchars($username); ?>! 👋</h1>
            <p>Your Access Code: <span class="code-box"><?php echo htmlspecialchars($code); ?></span></p>
        </div>
        <a href="../logout.php" class="logout-btn">🚪 Logout</a>
    </div>
</header>

<div class="container">

    <!-- PROFILE BUTTON -->
    <div class="profile-btn-container">
        <a href="edit_profile.php" class="profile-btn">👤 View / Edit My Info</a>
    </div>

    <!-- PAYMENT INFO CARD -->
    <?php if ($due_date): ?>
    <div class="payment-card <?php echo $alert_class; ?>">
        <h3><?php echo $icon; ?> Payment Reminder</h3>
        <div class="detail-row">
            <span class="label">📅 Due Date:</span>
            <span class="value"><?php echo date('F j, Y', strtotime($due_date)); ?></span>
        </div>
        <div class="detail-row">
            <span class="label">💳 Status:</span>
            <span class="value status-<?php echo strtolower($payment_status); ?>"><?php echo $payment_status; ?></span>
        </div>
        <div class="detail-row">
            <span class="label"></span>
            <span class="value"><?php echo $days_left; ?></span>
        </div>
    </div>
    <?php endif; ?>

    <!-- ROOMS SECTION -->
    <div class="rooms-section">
        <h2>🏠 My Room</h2>
        
        <?php if ($assigned_room != null): ?>
            <p class="assigned-message">✅ You already have a room assigned. Thank you!</p>
        <?php elseif (count($rooms) > 0): ?>
            <div class="rooms-grid">
                <?php foreach ($rooms as $room): ?>
                <div class="room-card">
                    <div class="room-number">Room <?php echo htmlspecialchars($room['room_number']); ?></div>
                    <div class="room-price">💰 $<?php echo htmlspecialchars(number_format($room['monthly_rent'], 2)); ?></div>
                    <a href="select_room.php?id=<?php echo $room['room_id']; ?>" class="btn-select">Choose This Room</a>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p style="font-size:18px; color:gray;">No rooms available yet. Please wait for Admin.</p>
        <?php endif; ?>
    </div>

</div>

</body>
</html>