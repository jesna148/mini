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

$studentQuery = $conn->prepare("SELECT fullname, email FROM users WHERE email=?");
$studentQuery->bind_param("s", $email);
$studentQuery->execute();
$student = $studentQuery->get_result()->fetch_assoc();
$studentName = $student['fullname'];

$totalEvents = $conn->query("SELECT COUNT(*) AS total FROM events")->fetch_assoc()['total'];
$registeredEvents = $conn->query("SELECT COUNT(*) AS reg FROM event_registrations WHERE student_email='$email'")->fetch_assoc()['reg'];
$activeClubs = $conn->query("SELECT COUNT(*) AS clubs FROM clubs")->fetch_assoc()['clubs'];

$events = $conn->query("SELECT id, title, banner, start_date FROM events ORDER BY start_date ASC LIMIT 5");

$clubs = $conn->query("SELECT id, club_name FROM clubs ORDER BY created_at DESC LIMIT 4");

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            display: flex;
            background-color: #f8fafc;
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
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.2);
        }

        .sidebar h2 {
            text-align: center;
            font-size: 22px;
            margin-bottom: 25px;
            color: #fff;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #cbd5e1;
            text-decoration: none;
            font-size: 16px;
            transition: 0.3s;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #2563eb;
            color: #fff;
        }

        .sidebar a i {
            margin-right: 12px;
            font-size: 18px;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
            width: 100%;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #1e293b;
            padding: 15px 25px;
            color: white;
            border-radius: 10px;
        }

        .header img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            border: 2px solid #2563eb;
        }

        .logout-btn {
            background-color: #e63946;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .logout-btn:hover {
            background-color: #d62828;
        }

        .stats {
            display: flex;
            justify-content: space-between;
            margin: 25px 0;
        }

        .card {
            background: white;
            padding: 18px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 30%;
            text-align: center;
        }

        .card i {
            font-size: 30px;
            color: #2563eb;
            margin-bottom: 10px;
        }

        .card a {
            text-decoration: none;
            color: #2563eb;
            font-weight: bold;
        }

        .events, .clubs {
            margin-top: 25px;
        }

        .event-slider, .club-container {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .event-card, .club-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 15px;
            width: 220px;
            text-align: center;
        }

        .event-card img, .club-card img {
            width: 100%;
            border-radius: 10px;
            height: 120px;
            object-fit: cover;
        }

        .btn {
            display: inline-block;
            margin-top: 8px;
            padding: 8px 15px;
            background: #2563eb;
            color: white;
            border-radius: 6px;
            text-decoration: none;
        }

        .notifications {
            margin-top: 30px;
        }

        .notif-card {
            background: #f1f5f9;
            padding: 12px;
            margin-bottom: 10px;
            border-left: 5px solid #2563eb;
            border-radius: 6px;
        }
    </style>
</head>
<body>
    
    <div class="sidebar">
        <h2>📚 Student Panel</h2>
        <a href="student_home.php" class="active"><i class="fas fa-home"></i> Dashboard</a>
        <a href="events.php"><i class="fas fa-calendar-alt"></i> Events</a>
        <a href="clubs.php"><i class="fas fa-users"></i> Clubs</a>
        <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
        <a href="#"><i class="fas fa-cog"></i> Settings</a>
        <a href="#"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="main-content">
       
        <header class="header">
            <h1>Welcome, <?php echo htmlspecialchars($studentName); ?> 🎓</h1>
        </header>

        <section class="stats">
            <div class="card">
                <i class="fas fa-calendar-alt"></i>
                <h3><?php echo $totalEvents; ?></h3>
                <a href="events.php">Total Events</a>
            </div>
            <div class="card">
                <i class="fas fa-ticket-alt"></i>
                <h3><?php echo $registeredEvents; ?></h3>
                <a href="events.php">Registered Events</a>
            </div>
            <div class="card">
                <i class="fas fa-users"></i>
                <h3><?php echo $activeClubs; ?></h3>
                <a href="clubs.php">Active Clubs</a>
            </div>
        </section>

        <section class="events">
            <h2>🎉 Upcoming Events</h2>
            <div class="event-slider">
                <?php if ($events && $events->num_rows > 0) {
                    while ($event = $events->fetch_assoc()) { ?>
                        <div class="event-card">
                            <img src="<?php echo $event['banner']; ?>" alt="Event">
                            <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                            <p><?php echo date("d M, Y", strtotime($event['start_date'])); ?></p>
                            <a href="events.php?id=<?php echo $event['id']; ?>" class="btn">View Details</a>
                        </div>
                    <?php }
                } else {
                    echo "<p>No upcoming events.</p>";
                } ?>
            </div>
        </section>

        <section class="clubs">
            <h2>🔥 Featured Clubs</h2>
            <div class="club-container">
                <?php if ($clubs && $clubs->num_rows > 0) {
                    while ($club = $clubs->fetch_assoc()) { ?>
                        <div class="club-card">
                            <img src="assets/default-club.png" alt="Club">
                            <h3><?php echo htmlspecialchars($club['club_name']); ?></h3>
                            <a href="club_details.php?id=<?php echo $club['id']; ?>" class="btn">Join Now</a>
                        </div>
                    <?php }
                } else {
                    echo "<p>No clubs available.</p>";
                } ?>
            </div>
        </section>

        <section class="notifications">
            <h2>🔔 Notifications</h2>
            <div class="notif-card">
                <p>⚡ Event registration deadline is approaching!</p>
            </div>
            <div class="notif-card">
                <p>🎯 Join clubs to connect with your peers!</p>
            </div>
        </section>
    </div>
</body>
</html>
