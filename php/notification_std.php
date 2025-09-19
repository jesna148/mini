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

$student_email = $_SESSION['email'];

// If you use GET for notification id, change to POST
$notif_id = isset($_POST['id']) ? $_POST['id'] : null;

// Update forms in HTML:
// <form action="notification_std.php" method="post">

// Fetch event registration notifications
$sql = "SELECT er.id, e.title, er.status, er.registered_on 
        FROM event_registrations er
        JOIN events e ON er.event_id = e.id
        WHERE er.student_email = ?
        ORDER BY er.registered_on DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_email);
$stmt->execute();
$result = $stmt->get_result();

// Count notifications
$notif_count = $result->num_rows;

// Sidebar badge counts
$eventNotifCount = $conn->query("SELECT COUNT(*) AS cnt FROM notifications WHERE user_email='$student_email' AND type='event' AND status='unread'")->fetch_assoc()['cnt'];
$clubNotifCount = $conn->query("SELECT COUNT(*) AS cnt FROM notifications WHERE user_email='$student_email' AND type='club' AND status='unread'")->fetch_assoc()['cnt'];
$generalNotifCount = $conn->query("SELECT COUNT(*) AS cnt FROM notifications WHERE user_email='$student_email' AND type='general' AND status='unread'")->fetch_assoc()['cnt'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Notifications</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { display: flex; background-color: #f8fafc; }

        /* Sidebar */
        .sidebar { width: 250px; background: #1e293b; color: white; height: 100vh; padding: 20px 0; position: fixed; top: 0; left: 0; box-shadow: 2px 0 10px rgba(0,0,0,0.2); }
        .sidebar h2 { text-align: center; font-size: 22px; margin-bottom: 25px; color: #fff; }
        .sidebar a { display: flex; align-items: center; justify-content: space-between; padding: 12px 20px; color: #cbd5e1; text-decoration: none; font-size: 16px; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background: #2563eb; color: #fff; }
        .sidebar a i { margin-right: 12px; font-size: 18px; }

        /* Badge */
        .badge { background: red; color: white; font-size: 12px; padding: 2px 7px; border-radius: 12px; font-weight: bold; display: none; }

        /* Main */
        .main-content { margin-left: 250px; padding: 20px; width: 100%; }
        h2 { margin-bottom: 20px; color: #2563eb; }

        /* Notification cards */
        .notif { padding: 15px; margin-bottom: 12px; border-radius: 8px; border-left: 6px solid #2563eb; background: #f1f5f9; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        .notif.accepted { border-left-color: #16a34a; }
        .notif.rejected { border-left-color: #dc2626; }
        .notif.pending { border-left-color: #f59e0b; }
        .notif strong { font-size: 16px; color: #1e293b; }
        .notif b { color: #0f172a; }
        .date { font-size: 13px; color: gray; margin-top: 6px; display: block; }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main -->
    <div class="main-content">
        <h2>🔔 My Notifications</h2>
        <div id="notifContainer">
        <?php if ($notif_count > 0) {
            $result->data_seek(0);
            while ($row = $result->fetch_assoc()) { 
                $statusClass = strtolower($row['status']); ?>
                <div class="notif <?php echo $statusClass; ?>">
                    <strong><?php echo htmlspecialchars($row['title']); ?></strong><br>
                    Status: <b><?php echo ucfirst($row['status']); ?></b><br>
                    <span class="date"><?php echo date("d M, Y h:i A", strtotime($row['registered_on'])); ?></span>
                </div>
            <?php }
        } else {
            echo "<p>No notifications yet.</p>";
        } ?>
        </div>
    </div>

    <!-- 🔔 Auto-update -->
    <script>
    function fetchNotifications() {
        fetch("get_notification_count.php")
            .then(response => response.json())
            .then(data => {
                const badge = document.getElementById("notif-badge");
                if (badge) {
                    if (data.count > 0) {
                        badge.textContent = data.count;
                        badge.style.display = "inline-block";
                    } else {
                        badge.style.display = "none";
                    }
                }
            })
            .catch(err => console.error("Error fetching notifications:", err));
    }

    // Initial + refresh
    fetchNotifications();
    setInterval(fetchNotifications, 10000);

    document.addEventListener('DOMContentLoaded', function() {
        const notifCountElem = document.getElementById('notif-count');
        if (notifCountElem) {
            notifCountElem.style.display = 'none';
        }
    });
    </script>

</body>
</html>
