<?php
session_start();
require_once 'db_connect.php';

// Ensure the user is logged in and is a 'user' role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    // Redirect to login if not logged in or not a user
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $boarding_code_input = trim($_POST['boarding_code'] ?? ''); // Renamed to avoid confusion

    if (empty($boarding_code_input)) {
        // Display error message to the user
        echo "<script>alert('Error: Please provide a boarding house code.'); window.location.href = '../dashboard.php';</script>";
        exit();
    }

    // Check if the entered code exists and is associated with an admin user
    // We need to find the admin_id associated with this code
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE boarding_house_code = ? AND role = 'admin'");
    $stmt->execute([$boarding_code_input]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin) {
        // Code is valid and belongs to an admin. Now link the current user to this code.
        $admin_id_associated = $admin['user_id']; // Get the admin's user_id

        // Update the current user's record with the boarding house code
        $stmt = $conn->prepare("UPDATE users SET boarding_house_code = ?, admin_id_link = ? WHERE user_id = ?");
        // NOTE: You'll need to ADD an 'admin_id_link' column to your 'users' table in the database
        // to properly associate users with a specific admin.
        // For now, we'll just store the code itself as per the original request.
        // If you want to link to the specific admin, uncomment the line below and add the column.

        $stmt_update_code = $conn->prepare("UPDATE users SET boarding_house_code = ? WHERE user_id = ?");
        if ($stmt_update_code->execute([$boarding_code_input, $_SESSION['user_id']])) {
            $_SESSION['boarding_house_code'] = $boarding_code_input; // Update session variable
            echo "<script>alert('Successfully joined boarding house!'); window.location.href = '../dashboard.php';</script>";
        } else {
            echo "<script>alert('Error: Failed to update your boarding house association.'); window.location.href = '../dashboard.php';</script>";
        }
    } else {
        // Code is invalid or not associated with an admin
        echo "<script>alert('Error: Invalid boarding house code. Please check the code and try again.'); window.location.href = '../dashboard.php';</script>";
    }
} else {
    // If accessed directly without POST, redirect to dashboard
    header("Location: ../dashboard.php");
    exit();
}
?>