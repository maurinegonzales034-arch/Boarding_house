<?php
session_start();
require_once 'includes/db_connect.php';

$msg = "";
$msg_class = "";
$show_form = true;

// Step 1: Verify username and security info
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['verify'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $contact = trim($_POST['contact']);

    // Check if all details match
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ? AND email = ? AND contact_number = ?");
    $stmt->execute([$username, $email, $contact]);
    
    if ($stmt->rowCount() > 0) {
        // Details match, show reset form
        $show_form = false;
        $user_id = $stmt->fetch()['user_id'];
    } else {
        $msg = "❌ Information does not match our records!";
        $msg_class = "error";
    }
}

// Step 2: Update password
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reset'])) {
    $user_id = $_POST['user_id'];
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    if ($new_password == $confirm_password) {
        $update = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
        $update->execute([$new_password, $user_id]);
        
        $msg = "✅ Password reset successfully! You can now login.";
        $msg_class = "success";
        $show_form = true; // Go back to first screen
    } else {
        $msg = "❌ Passwords do not match!";
        $msg_class = "error";
        $show_form = false;
        $user_id = $_POST['user_id'];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <style>
        body { 
            font-family: 'Segoe UI', sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }
        .form-container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
        }
        h1 { color: #333; text-align: center; margin-bottom: 10px; }
        .subtitle { text-align: center; color: #666; margin-bottom: 25px; }
        .message {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .form-group { margin-bottom: 18px; }
        label { display: block; margin-bottom: 6px; font-weight: 600; color: #555; font-size: 14px; }
        input { 
            width: 100%; 
            padding: 12px 15px; 
            border: 1px solid #ddd; 
            border-radius: 8px; 
            font-size: 16px;
        }
        .btn { 
            width: 100%;
            background: linear-gradient(to right, #667eea, #764ba2);
            color: white; 
            padding: 14px; 
            border: none; 
            border-radius: 8px; 
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
            display: block;
            color: #667eea;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>🔐 Reset Password</h1>
        <p class="subtitle">Verify your identity first</p>
        
        <?php if ($msg): ?>
            <div class="message <?php echo $msg_class; ?>"><?php echo $msg; ?></div>
        <?php endif; ?>

        <?php if ($show_form): ?>
            <!-- VERIFICATION FORM -->
            <form method="POST">
                <div class="form-group">
                    <label>Username:</label>
                    <input type="text" name="username" required placeholder="Enter your username">
                </div>

                <div class="form-group">
                    <label>Email Address:</label>
                    <input type="email" name="email" required placeholder="Enter your registered email">
                </div>

                <div class="form-group">
                    <label>Contact Number:</label>
                    <input type="text" name="contact" required placeholder="Enter your phone number">
                </div>

                <button type="submit" name="verify" class="btn">Verify Account</button>
            </form>
        <?php else: ?>
            <!-- NEW PASSWORD FORM -->
            <form method="POST">
                <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                
                <div class="form-group">
                    <label>New Password:</label>
                    <input type="password" name="new_password" required placeholder="Enter new password">
                </div>

                <div class="form-group">
                    <label>Confirm New Password:</label>
                    <input type="password" name="confirm_password" required placeholder="Enter new password again">
                </div>

                <button type="submit" name="reset" class="btn">Save New Password</button>
            </form>
        <?php endif; ?>

        <a href="login.php" class="back-link">← Back to Login</a>
    </div>
</body>
</html>