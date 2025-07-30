<?php
session_start();

// If user is not logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

// Database connection
$conn = new mysqli("localhost", "root", "", "project"); // update credentials

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user data
$sql = "SELECT fullname, email, role FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

$user = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Campus Clubs & Events - Student Panel</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }

        .top-bar {
            background: linear-gradient(to right, #a64bf4, #3dbbff);
            padding: 20px 0;
            text-align: center;
        }

        .top-bar h1 {
            color: white;
            font-size: 2em;
            font-weight: bold;
        }

        .navbar {
            background-color: #111;
            display: flex;
            justify-content: center;
            padding: 12px 0;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin: 0 20px;
            font-weight: 500;
            font-size: 1em;
            transition: 0.3s;
        }

        .navbar a:hover {
            color: #00f2fe;
        }

        .hero {
            background-image: url('https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&w=1950&q=80');
            background-size: cover;
            background-position: center;
            height: 90vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            padding: 0 20px;
        }

        .hero h2 {
            font-size: 3em;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .hero p {
            font-size: 1.2em;
        }

        .profile {
            margin: 40px auto;
            padding: 20px;
            max-width: 600px;
            background: #f2f2f2;
            border-radius: 12px;
            box-shadow: 0 0 12px rgba(0,0,0,0.1);
            font-size: 1.1em;
        }

        .profile-icon {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .profile-icon img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 2px solid #4CAF50;
            background-color: white;
            padding: 5px;
        }

        .profile h3 {
            margin-bottom: 15px;
            color: #333;
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;
            text-align: center;
        }

        .profile p {
            margin: 8px 0;
        }

        .edit-btn {
            display: block;
            width: 100%;
            text-align: center;
            margin-top: 20px;
        }

        .edit-btn a {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
        }

        .edit-btn a:hover {
            background-color: #45a049;
        }

        @media (max-width: 768px) {
            .hero h2 { font-size: 2em; }
            .hero p { font-size: 1em; }
        }
    </style>
</head>
<body>

    <div class="top-bar">
        <h1>Campus Clubs & Event Management</h1>
    </div>

    <div class="navbar">
        <a href="#">Home</a>
        <a href="clubs1.html">Clubs</a>
        <a href="CampusEvents.php">Events</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="hero">
        <h2>Welcome to Student Dashboard</h2>
        <p>Join. Connect. Celebrate. Lead your campus life!</p>
    </div>

    <div class="profile">
        <div class="profile-icon">
            <img src="<?php echo !empty($user['profile_pic']) ? 'uploads/' . $user['profile_pic'] : 'https://cdn-icons-png.flaticon.com/512/847/847969.png'; ?>" alt="Profile Icon">
        </div>
        <h3>Your Profile</h3>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($user['fullname']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><strong>Role:</strong> <?php echo htmlspecialchars($user['role']); ?></p>

    </div>

</body>
</html>
