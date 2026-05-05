<?php
session_start();
require_once '../includes/db_connect.php';

// Ensure user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

$msg = ''; // For success messages
$error = ''; // For error messages
$room_number = ''; // Initialize variables to clear form fields
$price = '';

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_number = trim($_POST['room_number']);
    $price = trim($_POST['price']);
    // Retrieve the boarding house code from the session
    $boarding_house_code = $_SESSION['boarding_house_code']; 

    // Basic validation
    if (empty($room_number) || empty($price)) {
        $error = "Room number and price cannot be empty.";
    } else {
        // Check if room number already exists for this boarding house code
        $check_stmt = $conn->prepare("SELECT room_id FROM rooms WHERE room_number = ? AND boarding_house_code = ?");
        $check_stmt->execute([$room_number, $boarding_house_code]);

        if ($check_stmt->rowCount() > 0) {
            $error = "Room number '" . htmlspecialchars($room_number) . "' already exists for this boarding house.";
        } else {
            // Insert the new room
            // Ensure the 'rooms' table has 'boarding_house_code' column
            $stmt = $conn->prepare("INSERT INTO rooms (admin_id, room_number, monthly_rent, boarding_house_code) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$_SESSION['user_id'], $room_number, $price, $boarding_house_code])) {
                $msg = "Room '" . htmlspecialchars($room_number) . "' added successfully!";
                // Clear the form fields after successful insertion
                $room_number = '';
                $price = '';
            } else {
                $error = "Failed to add room. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Room</title>
    <link rel="icon" href="../bh.png" type="image/png">
    <style>
        /* --- Mimicking Login Page Styles --- */
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
        .add-room-container {
            background: white;
            padding: 40px 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
        }
        .add-room-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .add-room-header h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 10px;
        }
        .add-room-header p {
            color: #666;
            font-size: 14px;
        }
        .error-msg, .success-msg { /* Combined styling for messages */
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 500;
        }
        .error-msg {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .success-msg {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
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
        input[type="text"], input[type="number"], select { /* Styles for input fields */
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border 0.3s;
        }
        input:focus, select:focus {
            border-color: #667eea;
            outline: none;
        }
        .btn-add {
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
        .btn-add:hover {
            opacity: 0.9;
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
            display: block;
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        /* --- End Mimicked Styles --- */
    </style>
</head>
<body>

    <div class="add-room-container">
        <div class="add-room-header">
            <h1>➕ Add New Room</h1>
            <p>Enter the details for the new room</p>
        </div>

        <?php if ($error): ?>
            <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($msg): ?>
            <div class="success-msg"><?php echo htmlspecialchars($msg); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="room_number">Room Number:</label>
                <input type="text" id="room_number" name="room_number" value="<?php echo htmlspecialchars($room_number); ?>" placeholder="e.g. 101" required>
            </div>

            <div class="form-group">
                <label for="price">Monthly Rent Price:</label>
                <input type="number" id="price" name="price" value="<?php echo htmlspecialchars($price); ?>" placeholder="e.g. 3000" required>
            </div>

            <button type="submit" class="btn-add">Save Room</button>
        </form>

        <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
    </div>

</body>
</html>
