<?php
session_start();
require_once '../includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit();
}

$error = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code = trim($_POST['code']);

    // Check if code exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE boarding_house_code = ? AND role = 'admin'");
    $stmt->execute([$code]);
    
    if ($stmt->rowCount() > 0) {
        // Save code to user's session and database
        $update = $conn->prepare("UPDATE users SET boarding_house_code = ? WHERE user_id = ?");
        $update->execute([$code, $_SESSION['user_id']]);
        
        $_SESSION['boarding_house_code'] = $code;
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "❌ Invalid Code! Please try again.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Enter Code | Boarding House System</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .code-container {
            background: white;
            padding: 40px 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
        }
        .code-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .code-header h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 10px;
        }
        .code-header p {
            color: #666;
            font-size: 14px;
        }
        .error-msg {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
        }
        input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border 0.3s;
            text-transform: uppercase;
        }
        input:focus {
            border-color: #667eea;
            outline: none;
        }
        .btn-submit {
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
            transition: opacity 0.3s;
        }
        .btn-submit:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>

    <div class="code-container">
        <div class="code-header">
            <h1>🔑 Enter Access Code</h1>
            <p>Please enter the code provided by your Admin</p>
        </div>

        <?php if ($error): ?>
            <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Access Code:</label>
                <input type="text" name="code" placeholder="Enter code here" required>
            </div>

            <button type="submit" class="btn-submit">Submit Code</button>
        </form>
    </div>

</body>
</html>