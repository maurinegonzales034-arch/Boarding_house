<?php
session_start();
require_once '../includes/db_connect.php'; // Adjust path if necessary

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Redirect to login page if not logged in or not an admin
    header("Location: ../login.php");
    exit();
}

$admin_id = $_SESSION['user_id'];
$rooms = []; // Initialize an empty array for rooms

// Fetch all rooms managed by this admin
try {
    $stmt = $conn->prepare("SELECT room_id, room_number, monthly_rent, due_date FROM rooms WHERE admin_id = ? ORDER BY room_number");
    $stmt->execute([$admin_id]);
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Handle potential database errors
    echo "<script>alert('Database error: Could not fetch rooms.');</script>";
    // Optionally log the error: error_log($e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manage Rooms</title>
    <link rel="stylesheet" href="../css/style.css" />
    <style>
        /* Add some basic styling for the table */
        .rooms-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .rooms-table th, .rooms-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .rooms-table th {
            background-color: #f2f2f2;
        }
        .rooms-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .rooms-table .actions a {
            margin-right: 10px;
            text-decoration: none;
            color: #007bff;
        }
        .rooms-table .actions a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <h1>Manage Rooms</h1>
        <nav>
            <a href="../dashboard.php">Dashboard</a> |
            <a href="../includes/logout.php">Logout</a>
        </nav>
    </header>

    <main>
        <h2>Your Rooms</h2>
        <p><a href="add_room.php">Add New Room</a></p>

        <?php if (empty($rooms)): ?>
            <p>You haven't added any rooms yet. <a href="add_room.php">Click here to add your first room.</a></p>
        <?php else: ?>
            <table class="rooms-table">
                <thead>
                    <tr>
                        <th>Room Number</th>
                        <th>Monthly Rent</th>
                        <th>Due Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rooms as $room): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($room['room_number']); ?></td>
                            <td>$<?php echo htmlspecialchars(number_format($room['monthly_rent'], 2)); ?></td>
                            <td><?php echo htmlspecialchars($room['due_date']); ?></td>
                            <td class="actions">
                                <!-- Add edit/delete links here later -->
                                <a href="#">Edit</a>
                                <a href="#">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2023 Boarding House</p>
    </footer>

    <script src="../js/script.js"></script>
</body>
</html>