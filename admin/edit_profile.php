<?php
session_start();
require_once '../includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Fetch current data
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$admin = $stmt->fetch();

$msg = "";
$msg_class = "";

// Update profile info
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $guardian = $_POST['guardian'];
    $location = $_POST['location'];

    $stmt = $conn->prepare("UPDATE users SET email=?, contact_number=?, guardian_number=?, home_location=? WHERE user_id=?");
    $stmt->execute([$email, $contact, $guardian, $location, $_SESSION['user_id']]);

    $msg = "✅ Profile Updated Successfully!";
    $msg_class = "success";
    
    // Refresh data
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $admin = $stmt->fetch();
}

// Change password
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_pass'])) {
    $new_pass = trim($_POST['new_password']);
    $confirm_pass = trim($_POST['confirm_password']);
    $verification = trim($_POST['verification']);

    // Check if verification is exactly "CONFIRM" (uppercase)
    if ($verification !== "CONFIRM") {
        $msg = "❌ Verification failed! Please type CONFIRM in capital letters.";
        $msg_class = "error";
    } 
    // Check if passwords match
    elseif ($new_pass != $confirm_pass) {
        $msg = "❌ Passwords do not match!";
        $msg_class = "error";
    } 
    // All good, update password
    else {
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
        $stmt->execute([$new_pass, $_SESSION['user_id']]);
        
        $msg = "✅ Password Changed Successfully!";
        $msg_class = "success";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Profile</title>
    <style>
        * { font-family: 'Segoe UI', sans-serif; box-sizing: border-box; }
        body { 
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%); 
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .container { 
            background: white; 
            padding: 40px; 
            border-radius: 15px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 500px;
        }
        h1 { 
            color: #2c3e50; 
            text-align: center; 
            margin-bottom: 30px;
            font-size: 32px;
        }
        .message {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 25px;
            text-align: center;
            font-weight: 500;
        }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .section {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .section h3 {
            color: #333;
            margin-bottom: 20px;
            font-size: 20px;
        }
        .form-group { margin-bottom: 20px; }
        label { 
            display: block; 
            margin-bottom: 8px; 
            font-weight: 600; 
            color: #555; 
            font-size: 15px;
        }
        input, textarea { 
            width: 100%; 
            padding: 14px 15px; 
            border: 1px solid #ddd; 
            border-radius: 8px; 
            font-size: 16px;
            transition: border 0.3s;
        }
        input:focus, textarea:focus {
            border-color: #8e44ad;
            outline: none;
        }
        textarea {
            resize: vertical;
            min-height: 80px;
        }
        .btn { 
            width: 100%;
            background: linear-gradient(to right, #8e44ad, #9b59b6);
            color: white; 
            padding: 14px; 
            border: none; 
            border-radius: 8px; 
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: opacity 0.3s;
        }
        .btn:hover {
            opacity: 0.9;
        }
        .back-link { 
            text-align: center; 
            margin-top: 25px; 
            display: block; 
            color: #8e44ad; 
            text-decoration: none;
            font-weight: 500;
        }
        .verification-note {
            font-size: 13px;
            color: #e74c3c;
            margin-top: 5px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>👤 Admin Profile</h1>
        
        <?php if ($msg): ?>
            <div class="message <?php echo $msg_class; ?>"><?php echo $msg; ?></div>
        <?php endif; ?>

        <!-- PROFILE INFO SECTION -->
        <div class="section">
            <h3>Personal Information</h3>
            <form method="POST">
                <div class="form-group">
                    <label>Email Address:</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                </div>

                <div class="form-group">
                    <label>My Contact Number:</label>
                    <input type="text" name="contact" value="<?php echo htmlspecialchars($admin['contact_number']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Guardian / Parent Number:</label>
                    <input type="text" name="guardian" value="<?php echo htmlspecialchars($admin['guardian_number']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Home Address / Location:</label>
                    <textarea name="location" required><?php echo htmlspecialchars($admin['home_location']); ?></textarea>
                </div>

                <button type="submit" name="update_profile" class="btn">💾 Save Profile</button>
            </form>
        </div>

        <!-- CHANGE PASSWORD SECTION -->
        <div class="section">
            <h3>🔐 Change Password</h3>
            <form method="POST">
                <div class="form-group">
                    <label>New Password:</label>
                    <input type="password" name="new_password" required placeholder="Enter new password">
                </div>

                <div class="form-group">
                    <label>Confirm New Password:</label>
                    <input type="password" name="confirm_password" required placeholder="Enter new password again">
                </div>

                <div class="form-group">
                    <label>Verification:</label>
                    <input type="text" name="verification" required placeholder="Type CONFIRM">
                    <p class="verification-note">⚠️ Please type CONFIRM strictly in CAPITAL LETTERS</p>
                </div>

                <button type="submit" name="change_pass" class="btn">🔄 Change Password</button>
            </form>
        </div>

        <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
    </div>
</body>
</html>