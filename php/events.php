<?php
session_start();
$conn = new mysqli("localhost", "root", "", "project");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$student_email = $_SESSION['email'];

// Notification counts
$eventNotifCount = $conn->query("SELECT COUNT(*) AS cnt FROM notifications WHERE user_email='$student_email' AND type='event' AND status='unread'")->fetch_assoc()['cnt'];
$clubNotifCount = $conn->query("SELECT COUNT(*) AS cnt FROM notifications WHERE user_email='$student_email' AND type='club' AND status='unread'")->fetch_assoc()['cnt'];
$generalNotifCount = $conn->query("SELECT COUNT(*) AS cnt FROM notifications WHERE user_email='$student_email' AND type='general' AND status='unread'")->fetch_assoc()['cnt'];

// ✅ If event ID is passed → show single event
if (isset($_GET['id'])) {
    $eventId = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM events WHERE id=?");
    $stmt->bind_param("i", $eventId);
    $stmt->execute();
    $event = $stmt->get_result()->fetch_assoc();

    $is_registered = false;
    $check = $conn->prepare("SELECT id FROM event_registrations WHERE student_email=? AND event_id=?");
    $check->bind_param("si", $student_email, $eventId);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        $is_registered = true;
    }
} else {
    // ✅ Otherwise → list all events
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $category = isset($_GET['category']) ? $_GET['category'] : '';

    $query = "SELECT * FROM events WHERE title LIKE '%$search%'";
    if (!empty($category)) {
        $query .= " AND category='$category'";
    }
    $query .= " ORDER BY start_date ASC";
    $events = $conn->query($query);

    $ongoing = [];
    $upcoming = [];
    $past = [];

    while ($row = $events->fetch_assoc()) {
        $today = date("Y-m-d");
        if ($today >= $row['start_date'] && $today <= $row['end_date']) {
            $ongoing[] = $row;
        } elseif ($today < $row['start_date']) {
            $upcoming[] = $row;
        } else {
            $past[] = $row;
        }
    }

    $my_registrations = [];
    $reg_query = $conn->query("SELECT event_id FROM event_registrations WHERE student_email='$student_email'");
    while ($r = $reg_query->fetch_assoc()) {
        $my_registrations[] = $r['event_id'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Campus Events</title>
    <style>
        /* General Reset */
        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f6f9;
            color: #333;
        }


        /* Main Content */
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .header {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #4e73df;
        }
        .header p {
            margin: 5px 0 0;
            color: #666;
        }

        /* Event List Cards */
        .event-card {
            background: #fff;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            display: flex;
            align-items: flex-start;
            gap: 15px;
        }
        .event-card img {
            width: 120px;
            height: 80px;
            object-fit: cover;
            border-radius: 6px;
        }
        .event-card h3 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        .event-card p {
            margin: 3px 0;
            font-size: 14px;
            color: #666;
        }
        .event-card .actions {
            margin-top: 10px;
        }
        .event-card a, 
        .event-card button {
            display: inline-block;
            background: #4e73df;
            color: white;
            padding: 6px 12px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 13px;
            margin-right: 5px;
            border: none;
            cursor: pointer;
            transition: 0.2s;
        }
        .event-card a:hover,
        .event-card button:hover {
            background: #2e59d9;
        }
        .event-card button[disabled] {
            background: #aaa;
            cursor: not-allowed;
        }

        /* Event Detail */
        .event-detail {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            max-width: 800px;
            margin: 20px auto;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .event-detail h2 {
            margin-top: 0;
            color: #4e73df;
        }
        .event-detail img {
            width: 100%;
            border-radius: 10px;
            margin-bottom: 15px;
            height: 300px;
            object-fit: cover;
        }
        .event-detail p {
            font-size: 15px;
            margin: 8px 0;
        }
        .event-detail .btn {
            display: inline-block;
            padding: 10px 16px;
            background: #4e73df;
            color: #fff;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
        }
        .event-detail .btn:hover {
            background: #2e59d9;
        }
        .event-detail button[disabled] {
            background: #aaa;
            padding: 10px 16px;
            border-radius: 6px;
            border: none;
            font-size: 14px;
        }
        .back-btn {
            display: inline-block;
            margin-top: 20px;
            background: #444;
            color: white;
            padding: 8px 14px;
            border-radius: 6px;
            text-decoration: none;
        }
        .back-btn:hover {
            background: #222;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 180px;
            }
            .main-content {
                margin-left: 180px;
            }
            .event-card {
                flex-direction: column;
            }
            .event-card img {
                width: 100%;
                height: 150px;
            }
        }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main-content">
    <div class="header">
        <h1>Campus Events</h1>
        <p>Explore upcoming, ongoing, and past events</p>
    </div>

    <?php if (isset($event)) { ?>
        <!-- ✅ Single Event Page -->
        <div class="event-detail">
            <h2><?php echo htmlspecialchars($event['title']); ?></h2>
            <img src="uploads/<?php echo $event['banner']; ?>" alt="Event Banner">
            <p><b>Start:</b> <?php echo $event['start_date']; ?></p>
            <p><b>End:</b> <?php echo $event['end_date']; ?></p>
            <p><b>Description:</b> <?php echo nl2br(htmlspecialchars($event['description'])); ?></p>

            <?php if ($is_registered) { ?>
                <button disabled>Registered ✅</button>
            <?php } else { ?>
                <a href="register_event.php?id=<?php echo $event['id']; ?>" class="btn">Register</a>
            <?php } ?>

            <br><br>
            <a href="events.php" class="back-btn">⬅ Back to All Events</a>
        </div>
    <?php } else { ?>
        <!-- ✅ Example Event Listing -->
        <?php foreach ($ongoing as $e) { ?>
            <div class="event-card">
                <img src="uploads/<?php echo $e['banner']; ?>" alt="Event Banner">
                <div>
                    <h3><?php echo htmlspecialchars($e['title']); ?></h3>
                    <p><b>Start:</b> <?php echo $e['start_date']; ?> | <b>End:</b> <?php echo $e['end_date']; ?></p>
                    <div class="actions">
                        <a href="events.php?id=<?php echo $e['id']; ?>">View Details</a>
                        <?php if (in_array($e['id'], $my_registrations)) { ?>
                            <button disabled>Registered ✅</button>
                        <?php } else { ?>
                            <a href="register_event.php?id=<?php echo $e['id']; ?>">Register</a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    <?php } ?>
</div>

</body>
</html>
