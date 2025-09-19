<?php
session_start();
$conn = new mysqli("localhost", "root", "", "project");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];
// If you use GET for anything, change to POST
// Example: $id = isset($_POST['id']) ? $_POST['id'] : null;

$sql = "SELECT * FROM students WHERE email='$email'";
$result = $conn->query($sql);
$student = $result->fetch_assoc();

$profilePic = (!empty($student['profile_pic'])) ? "uploads/" . $student['profile_pic'] : "uploads/default-profile.png";

// Notification counts
$eventNotifCount = $conn->query("SELECT COUNT(*) AS cnt FROM notifications WHERE user_email='$email' AND type='event' AND status='unread'")->fetch_assoc()['cnt'];
$clubNotifCount = $conn->query("SELECT COUNT(*) AS cnt FROM notifications WHERE user_email='$email' AND type='club' AND status='unread'")->fetch_assoc()['cnt'];
$generalNotifCount = $conn->query("SELECT COUNT(*) AS cnt FROM notifications WHERE user_email='$email' AND type='general' AND status='unread'")->fetch_assoc()['cnt'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile View</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <!-- ✅ Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background: #f0f2f5;
            display: flex;
            height: 100vh;
        }
        .sidebar {
            width: 250px;
            background: #1e293b;
            color: white;
            height: 100vh;
            padding: 20px 0;
            position: fixed;
            top: 0;
            left: 0;
            box-shadow: 2px 0 10px rgba(0,0,0,0.2);
        }
        .sidebar h2 {
            text-align: center;
            font-size: 22px;
            margin-bottom: 25px;
        }
        .sidebar a {
            display: block;
            padding: 12px 20px;
            color: #cbd5e1;
            text-decoration: none;
            transition: 0.3s;
        }
        .sidebar a:hover, .sidebar a.active {
            background: #2563eb;
            color: #fff;
        }
        .main {
            margin-left: 250px;
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .profile-container {
            width: 400px;
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 3px solid #6a11cb;
            object-fit: cover;
            margin-bottom: 15px;
        }
        h2 { color: #333; margin-bottom: 10px; }
        p { margin: 6px 0; font-size: 15px; color: #555; }
        .btn {
            display: inline-block;
            margin-top: 20px;
            background: #6a11cb;
            color: #fff;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: 0.3s;
        }
        .btn:hover { background: #4b0ca3; }
    </style>
</head>
<body>
<?php include 'sidebar.php'; ?>

<div class="main">
    <div class="profile-container">
        <img src="<?php echo $profilePic; ?>" alt="Profile Picture">
        <h2><?php echo $student['name']; ?></h2>
        <p><b>Email:</b> <?php echo $student['email']; ?></p>
        <p><b>Phone:</b> <?php echo !empty($student['phone']) ? $student['phone'] : "Not updated"; ?></p>
        <p><b>Department:</b> <?php echo !empty($student['department']) ? $student['department'] : "Not updated"; ?></p>

        <!-- ✅ Edit button goes to profile.php -->
        <a href="profile.php" class="btn"><i class="fas fa-edit"></i> Edit Profile</a>
    </div>
</div>
</body>
</html>
