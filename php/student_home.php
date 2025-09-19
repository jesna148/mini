<?php
session_start();
$conn = new mysqli("localhost", "root", "", "project");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ✅ Security check
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

// ✅ Student basic details
$studentQuery = $conn->prepare("SELECT fullname, email FROM users WHERE email=?");
$studentQuery->bind_param("s", $email);
$studentQuery->execute();
$student = $studentQuery->get_result()->fetch_assoc();
$studentName = $student['fullname'];

// ✅ Dashboard stats
$totalEvents = $conn->query("SELECT COUNT(*) AS total FROM events")->fetch_assoc()['total'];
$registeredEvents = $conn->query("SELECT COUNT(*) AS reg FROM event_registrations WHERE student_email='$email'")->fetch_assoc()['reg'];
$activeClubs = $conn->query("SELECT COUNT(*) AS clubs FROM clubs")->fetch_assoc()['clubs'];

// ✅ Events & Clubs preview
$events = $conn->query("SELECT id, title, banner, start_date FROM events ORDER BY start_date ASC LIMIT 5");
$clubs = $conn->query("SELECT id, club_name FROM clubs ORDER BY created_at DESC LIMIT 4");

// ✅ Profile completeness check
$profileCheck = $conn->query("SELECT phone, department, profile_pic FROM students WHERE email='$email'");
$profileData = $profileCheck ? $profileCheck->fetch_assoc() : null;
$isProfileComplete = $profileData && !empty($profileData['phone']) && !empty($profileData['department']) && !empty($profileData['profile_pic']);
$profileLink = $isProfileComplete ? "profile_view.php" : "profile.php";

// ✅ Notifications counts
$eventNotifCount = $conn->query("SELECT COUNT(*) AS cnt FROM notifications WHERE user_email='$email' AND type='event' AND status='unread'")->fetch_assoc()['cnt'];
$clubNotifCount = $conn->query("SELECT COUNT(*) AS cnt FROM notifications WHERE user_email='$email' AND type='club' AND status='unread'")->fetch_assoc()['cnt'];
$generalNotifCount = $conn->query("SELECT COUNT(*) AS cnt FROM notifications WHERE user_email='$email' AND type='general' AND status='unread'")->fetch_assoc()['cnt'];

// ✅ Latest 5 notifications (for dashboard preview)
$latestNotifications = $conn->query("SELECT message, created_at FROM notifications WHERE user_email='$email' ORDER BY created_at DESC LIMIT 5");

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
        * {margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif;}
        body {display: flex; background-color: #f8fafc;}
        .main-content {margin-left: 250px; padding: 20px; width: 100%;}
        .header {display: flex; justify-content: space-between; align-items: center; background: #1e293b; padding: 15px 25px; color: white; border-radius: 10px;}
        .header img {width: 45px; height: 45px; border-radius: 50%; border: 2px solid #2563eb;}
        .stats {display: flex; justify-content: space-between; margin: 25px 0; gap: 20px;}
        .card {background: white; padding: 18px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); flex: 1; text-align: center;}
        .card i {font-size: 30px; color: #2563eb; margin-bottom: 10px;}
        .card a {text-decoration: none; color: #2563eb; font-weight: bold;}
        .events, .clubs, .notifications {margin-top: 25px;}
        .event-slider, .club-container {display: flex; gap: 20px; flex-wrap: wrap;}
        .event-card, .club-card {background: white; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 15px; width: 220px; text-align: center;}
        .event-card img, .club-card img {width: 100%; border-radius: 10px; height: 120px; object-fit: cover;}
        .btn {display: inline-block; margin-top: 8px; padding: 8px 15px; background: #2563eb; color: white; border-radius: 6px; text-decoration: none;}
        .notif-card {background: #f1f5f9; padding: 12px; margin-bottom: 10px; border-left: 5px solid #2563eb; border-radius: 6px;}
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <header class="header">
            <h1>Welcome, <?php echo htmlspecialchars($studentName); ?> 🎓</h1>
            <div>
                <?php if ($profileData && !empty($profileData['profile_pic'])): ?>
                    <img src="<?php echo htmlspecialchars($profileData['profile_pic']); ?>" alt="Profile">
                <?php else: ?>
                    <img src="assets/default-avatar.png" alt="Default">
                <?php endif; ?>
            </div>
        </header>

        <!-- Stats Section -->
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

        <!-- Events Preview -->
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
                } else { echo "<p>No upcoming events.</p>"; } ?>
            </div>
        </section>

        <!-- Clubs Preview -->
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
                } else { echo "<p>No clubs available.</p>"; } ?>
            </div>
        </section>

        <!-- Notifications Preview -->
        <section class="notifications">
            <h2>🔔 Recent Notifications</h2>
            <?php if ($latestNotifications && $latestNotifications->num_rows > 0) {
                while ($notif = $latestNotifications->fetch_assoc()) { ?>
                    <div class="notif-card">
                        <p><?php echo htmlspecialchars($notif['message']); ?></p>
                        <small><?php echo date("d M, Y h:i A", strtotime($notif['created_at'])); ?></small>
                    </div>
                <?php }
            } else { echo "<p>No new notifications.</p>"; } ?>
        </section>
    </div>
</body>
</html>
