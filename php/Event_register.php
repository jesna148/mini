<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $event = trim($_POST['event']);

    if (empty($name) || empty($email) || empty($event)) {
        // Redirect back with error
        header("Location: events.php?error=emptyfields");
        exit();
    }

    // Connect to DB
    $conn = new mysqli("localhost", "root", "", "project");

    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    // Insert into event_registrations
    $stmt = $conn->prepare("INSERT INTO event_registrations (name, email, event_name) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $event);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();

        // Show JavaScript alert and redirect
        echo "<script>
            alert('Registration Successful');
            window.location.href = 'CampusEvents.php';
        </script>";
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    // Not a POST request
    header("Location: CampusEvents.php");
    exit();
}
