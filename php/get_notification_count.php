<?php
session_start();
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "project");
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

if (!isset($_SESSION['email'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit();
}

$email = $_SESSION['email'];

$eventNotifCount = $conn->query("SELECT COUNT(*) AS cnt FROM notifications WHERE user_email='$email' AND type='event' AND status='unread'")->fetch_assoc()['cnt'];
$clubNotifCount = $conn->query("SELECT COUNT(*) AS cnt FROM notifications WHERE user_email='$email' AND type='club' AND status='unread'")->fetch_assoc()['cnt'];
$generalNotifCount = $conn->query("SELECT COUNT(*) AS cnt FROM notifications WHERE user_email='$email' AND type='general' AND status='unread'")->fetch_assoc()['cnt'];

echo json_encode([
    'event' => $eventNotifCount,
    'club' => $clubNotifCount,
    'general' => $generalNotifCount
]);
$conn->close();