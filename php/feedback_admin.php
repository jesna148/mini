<?php
session_start();
$conn = new mysqli("localhost", "root", "", "project");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Only admin can access
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch all feedback from approved users only
$result = $conn->query("
    SELECT f.id, u.fullname, u.email, f.message, f.created_at
    FROM feedback f
    JOIN users u ON f.id = u.id
    WHERE u.is_approved = 1
    ORDER BY f.created_at DESC
");

$feedbacks = [];
while ($row = $result->fetch_assoc()) {
    $feedbacks[] = $row;
}

// Get current page to set active class
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin | Student Feedback</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f9f9f9;
            margin: 0;
            padding: 0;
            display: flex;
        }

        .sidebar {
            width: 220px;
            background: #2c3e50;
            min-height: 100vh;
            padding: 20px;
            box-sizing: border-box;
            color: white;
            position: relative;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 1.5rem;
        }

        .sidebar a {
            display: block;
            padding: 10px 15px;
            color: white;
            text-decoration: none;
            margin-bottom: 10px;
            border-radius: 5px;
            transition: 0.3s;
        }

        .sidebar a:hover {
            background: #1abc9c;
        }

        .sidebar a.active {
            background: #1abc9c;
            font-weight: bold;
        }

        .sidebar .logout {
            position: absolute;
            bottom: 20px;
            width: 180px;
        }

        .main-container {
            flex: 1;
            padding: 50px;
            box-sizing: border-box;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #2c3e50;
        }

        .feedback-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 6px 12px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .feedback-card h3 {
            margin-bottom: 5px;
            color: #1abc9c;
        }

        .feedback-card p {
            color: #555;
            font-size: 0.95rem;
            margin-bottom: 5px;
        }

        .feedback-card small {
            color: #999;
        }

        .no-feedback {
            text-align: center;
            color: #999;
            font-size: 1rem;
        }

        a.back {
            display: inline-block;
            margin-bottom: 20px;
            text-decoration: none;
            color: white;
            background: #2c3e50;
            padding: 10px 15px;
            border-radius: 5px;
            transition: 0.3s;
        }

        a.back:hover {
            background: #1abc9c;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="manage_users.php" <?= $current_page === 'manage_users.php' ? 'class="active"' : '' ?>>Manage Users</a>
        <a href="manage_clubs.php" <?= $current_page === 'manage_clubs.php' ? 'class="active"' : '' ?>>Manage Clubs</a>
        <a href="admin_reg.php" <?= $current_page === 'admin_reg.php' ? 'class="active"' : '' ?>>Manage Events</a>
        <a href="feedback_admin.php" <?= $current_page === 'feedback_admin.php' ? 'class="active"' : '' ?>>Feedback</a>
        <a href="report.php" <?= $current_page === 'report.php' ? 'class="active"' : '' ?>>Reports</a>
        <a href="settings.php" <?= $current_page === 'settings.php' ? 'class="active"' : '' ?>>Settings</a>
        <a href="more.php" <?= $current_page === 'more.php' ? 'class="active"' : '' ?>>More</a>
        <div class="logout">
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="main-container">
       <h1>Student Feedback</h1>

        <?php if (count($feedbacks) > 0): ?>
            <?php foreach ($feedbacks as $fb): ?>
                <div class="feedback-card">
                    <h3><?= htmlspecialchars($fb['fullname']) ?> (<?= htmlspecialchars($fb['email']) ?>)</h3>
                    <p><?= nl2br(htmlspecialchars($fb['message'])) ?></p>
                    <small>Submitted on: <?= $fb['created_at'] ?></small>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-feedback">No feedback submitted by approved students yet.</p>
        <?php endif; ?>
    </div>

</body>
</html>
