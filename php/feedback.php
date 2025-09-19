<?php
session_start();
$conn = new mysqli("localhost", "root", "", "project");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Only students can access
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

// If you use GET for feedback id, change to POST
$feedback_id = isset($_POST['id']) ? $_POST['id'] : null;

// Get student details
$studentQuery = $conn->prepare("SELECT fullname, email FROM users WHERE email=?");
$studentQuery->bind_param("s", $email);
$studentQuery->execute();
$student = $studentQuery->get_result()->fetch_assoc();
$studentName = $student['fullname'];

// ✅ Handle feedback submission
$successMsg = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $rating = intval($_POST['rating']);
    $message = trim($_POST['message']);

    if ($rating >= 1 && $rating <= 5 && !empty($message)) {
        $stmt = $conn->prepare("INSERT INTO feedback (student_name, student_email, rating, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssis", $studentName, $email, $rating, $message);
        if ($stmt->execute()) {
            $successMsg = "✅ Feedback submitted successfully!";
        } else {
            $successMsg = "❌ Error: " . $conn->error;
        }
        $stmt->close();
    } else {
        $successMsg = "⚠ Please fill in all fields correctly.";
    }
}

// ✅ Fetch recent feedback
$feedbacks = $conn->query("SELECT student_name, rating, message, created_at FROM feedback ORDER BY created_at DESC LIMIT 5");

$eventNotifCount = $conn->query("SELECT COUNT(*) AS cnt FROM notifications WHERE user_email='$email' AND type='event' AND status='unread'")->fetch_assoc()['cnt'];
$clubNotifCount = $conn->query("SELECT COUNT(*) AS cnt FROM notifications WHERE user_email='$email' AND type='club' AND status='unread'")->fetch_assoc()['cnt'];
$generalNotifCount = $conn->query("SELECT COUNT(*) AS cnt FROM notifications WHERE user_email='$email' AND type='general' AND status='unread'")->fetch_assoc()['cnt'];

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Feedback</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
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
            top: 0; left: 0;
            box-shadow: 2px 0 10px rgba(0,0,0,0.2);
        }
        .sidebar h2 { text-align: center; margin-bottom: 20px; }
        .sidebar a {
            display: block;
            padding: 12px 20px;
            color: #cbd5e1;
            text-decoration: none;
            transition: 0.3s;
        }
        .sidebar a:hover, .sidebar a.active { background: #2563eb; color: white; }
        .main-content {
            margin-left: 250px;
            padding: 20px;
            width: 100%;
        }
        .header {
            background: #1e293b;
            padding: 15px;
            border-radius: 8px;
            color: white;
            margin-bottom: 20px;
        }
        form {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 25px;
        }
        form label { font-weight: bold; }
        form textarea, form select {
            width: 100%; padding: 10px; margin-top: 8px;
            border: 1px solid #ccc; border-radius: 8px;
        }
        form button {
            margin-top: 15px; padding: 10px 20px;
            background: #2563eb; color: white;
            border: none; border-radius: 6px;
            cursor: pointer;
        }
        .feedback-card {
            background: #f1f5f9;
            padding: 15px;
            border-left: 5px solid #2563eb;
            border-radius: 8px;
            margin-bottom: 12px;
        }
        .msg { margin-bottom: 15px; font-weight: bold; }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

    <div class="main-content">
        <header class="header">
            <h1>Feedback Section</h1>
        </header>

        <?php if (!empty($successMsg)) { ?>
            <div class="msg"><?php echo $successMsg; ?></div>
        <?php } ?>

        <form action="feedback.php" method="post">
            <label for="rating">Rate Your Experience (1-5):</label>
            <select name="rating" id="rating" required>
                <option value="">Select</option>
                <option value="1">1 - Very Poor</option>
                <option value="2">2 - Poor</option>
                <option value="3">3 - Average</option>
                <option value="4">4 - Good</option>
                <option value="5">5 - Excellent</option>
            </select>

            <label for="message">Your Feedback:</label>
            <textarea name="message" id="message" rows="4" required></textarea>

            <button type="submit">Submit Feedback</button>
        </form>

        
    </div>
</body>
</html>
