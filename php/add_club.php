<?php
$conn = new mysqli('localhost', 'root', '', 'project');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $club_name = trim($_POST['club_name']);
    $leader = trim($_POST['leader']);

    if (!empty($club_name) && !empty($leader)) {
        $stmt = $conn->prepare("INSERT INTO clubs (club_name, leader) VALUES (?, ?)");
        $stmt->bind_param("ss", $club_name, $leader);
        
        if ($stmt->execute()) {
            echo "<script>
                    alert('✅ Club Added Successfully!');
                    window.location.href='manage_clubs.php';
                  </script>";
        } else {
            echo "❌ Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "<script>alert('⚠️ All fields are required!');</script>";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add New Club</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 40px; }
    h2 { color: #4CAF50; }
    form { max-width: 400px; background: #f9f9f9; padding: 20px; border-radius: 8px; }
    label { display: block; margin: 10px 0 5px; }
    input { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 5px; }
    button {
      padding: 10px 15px;
      background: #4CAF50;
      border: none;
      color: white;
      cursor: pointer;
      border-radius: 5px;
    }
    button:hover { background: #45a049; }
  </style>
</head>
<body>
  <h2>Add New Club</h2>
  <form method="POST" action="">
    <label for="club_name">Club Name</label>
    <input type="text" name="club_name" id="club_name" required>

    <label for="leader">Leader</label>
    <input type="text" name="leader" id="leader" required>

    <button type="submit">✅ Add Club</button>
  </form>
</body>
</html>
