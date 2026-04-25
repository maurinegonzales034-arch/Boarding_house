<?php
session_start();
require_once '../includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

$username = $_SESSION['username'];
$code = $_SESSION['boarding_house_code'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            background: #f0f2f5;
        }
        header {
            background: linear-gradient(to right, #2c3e50, #34495e);
            color: white;
            padding: 25px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        .welcome-text h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        .code-display {
            font-size: 18px;
        }
        .code-display span {
            background: rgba(255,255,255,0.2);
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            letter-spacing: 2px;
            color: #f1c40f;
        }
        .btn-logout {
            background: #e74c3c;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            transition: background 0.3s;
        }
        .btn-logout:hover {
            background: #c0392b;
        }
        .container {
            width: 90%;
            max-width: 1100px;
            margin: 30px auto;
        }
        .card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        .card h2 {
            color: #333;
            margin-bottom: 25px;
            font-size: 24px;
            display: flex;
            align-items: center;
        }
        .card h2 i {
            margin-right: 10px;
            color: #8e44ad;
        }
        .button-grid {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: center;
        }
        .btn {
            padding: 15px 25px;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 16px;
            transition: transform 0.2s, box-shadow 0.2s;
            display: inline-flex;
            align-items: center;
        }
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .btn-blue { background: #3498db; }
        .btn-green { background: #27ae60; }
        .btn-orange { background: #f39c12; }
        .btn-purple { background: #8e44ad; }
        .btn-profile { background: #1abc9c; }
        .btn-red { background: #e74c3c; } /* New color for overdue */
    </style>
</head>
<body>

<header>
    <div class="welcome-text">
        <h1>Welcome Admin, <?php echo htmlspecialchars($username); ?>!</h1>
        <p class="code-display">Your Boarding House Code: <span><?php echo htmlspecialchars($code); ?></span></p>
    </div>
    <a href="../logout.php" class="btn-logout">🚪 Logout</a>
</header>

<div class="container">
    <div class="card">
        <h2>🛠️ Management Tools</h2>
        
        <div class="button-grid">
            <a href="generate_code.php" class="btn btn-blue">🔄 Generate / Regenerate Code</a>
            <a href="add_room.php" class="btn btn-green">➕ Add New Room</a>
            <a href="view_occupants.php" class="btn btn-orange">👁️ View Room Occupants</a>
            <a href="view_all_users.php" class="btn btn-purple">👥 View All Registered Users</a>
            <a href="view_overdue.php" class="btn btn-red">⚠️ View Overdue Payments</a>
            <a href="edit_profile.php" class="btn btn-profile">👤 Edit My Profile</a>
        </div>
    </div>
</div>

</body>
</html>