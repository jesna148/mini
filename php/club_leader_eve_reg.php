<?php
session_start();
$conn = new mysqli("localhost", "root", "", "project");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT er.id, er.student_email, er.event_id, e.title AS event_name, er.registered_on, er.status
        FROM event_registrations er
        JOIN events e ON er.event_id = e.id
        ORDER BY er.registered_on DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Club Leader - Event Registrations</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f9; padding: 20px; }
        h1 { color: #2c3e50; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background-color: #2c3e50; color: white; }
        .pending { color: orange; }
        .accepted { color: green; font-weight: bold; }
        .rejected { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Event Registrations (Club Leader Panel)</h1>
    <table>
        <tr>
            <th>ID</th>
            <th>Student Email</th>
            <th>Event</th>
            <th>Registered On</th>
            <th>Status</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['student_email'] ?></td>
                <td><?= $row['event_name'] ?></td>
                <td><?= $row['registered_on'] ?></td>
                <td class="<?= $row['status'] ?>"><?= ucfirst($row['status']) ?></td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>
