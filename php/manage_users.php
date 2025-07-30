<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users - Admin Panel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            padding: 20px;
        }
        h2 {
            text-align: center;
        }
        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: center;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        .action-btn {
            padding: 6px 12px;
            margin: 2px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            color: white;
        }
        .delete-btn {
            background-color: #e74c3c;
        }
        .role-btn {
            background-color: #3498db;
        }
    </style>
</head>
<body>

<h2>Manage Users</h2>

<?php
$conn = new mysqli('localhost', 'root', '', 'project');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle delete action
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM users WHERE id=$id");
    echo "<script>alert('User deleted successfully');window.location='manage_users.php';</script>";
}

// Handle role change
if (isset($_GET['role']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $newRole = $_GET['role'];
    $conn->query("UPDATE users SET Role='$newRole' WHERE id=$id");
    echo "<script>alert('User role updated');window.location='manage_users.php';</script>";
}

// Fetch users
$result = $conn->query("SELECT * FROM users");
?>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['fullname']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= $row['role'] ?></td>
            <td>
                <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Are you sure?')">
                    <button class="action-btn delete-btn">Delete</button>
                </a>
                <?php if ($row['role'] != 'club_leader') { ?>
                    <a href="?role=club_leader&id=<?= $row['id'] ?>">
                        <button class="action-btn role-btn">Make Club Leader</button>
                    </a>
                <?php } else { ?>
                    <a href="?role=student&id=<?= $row['id'] ?>">
                        <button class="action-btn role-btn">Demote to Student</button>
                    </a>
                <?php } ?>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>

<?php $conn->close(); ?>

</body>
</html>
