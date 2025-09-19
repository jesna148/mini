<?php
session_start();
$conn = new mysqli("localhost", "root", "", "project");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Only allow admins
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch current settings (always first row)
$result = $conn->query("SELECT * FROM setting LIMIT 1");
$settings = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $site_name = $conn->real_escape_string($_POST['site_name']);
    $admin_email = $conn->real_escape_string($_POST['admin_email']);
    $theme = $conn->real_escape_string($_POST['theme']);

    // Logo Upload
    $logo = $settings['logo'];
    if (!empty($_FILES['logo']['name'])) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir);
        $fileName = time() . "_" . basename($_FILES["logo"]["name"]);
        $targetFilePath = $targetDir . $fileName;

        $allowedTypes = ['jpg', 'jpeg', 'png'];
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES["logo"]["tmp_name"], $targetFilePath)) {
                $logo = $targetFilePath;
            }
        }
    }

    // Update settings
    $update = "UPDATE setting 
               SET site_name='$site_name', admin_email='$admin_email', logo='$logo', theme='$theme'
               WHERE id=1";
    $conn->query($update);

    // ✅ reload with success message
    header("Location: setting.php?success=1");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Settings</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      background-color: #f4f6f8;
      display: flex;
    }
    .sidebar {
      width: 250px;
      height: 100vh;
      background-color: #2c3e50;
      color: #fff;
      position: fixed;
      top: 0;
      left: 0;
      padding-top: 20px;
      box-shadow: 2px 0 8px rgba(0, 0, 0, 0.2);
    }
    .sidebar h2 { text-align: center; margin-bottom: 20px; }
    .sidebar a {
      display: block; padding: 12px 20px; color: #ecf0f1;
      text-decoration: none; font-size: 16px; transition: 0.3s;
    }
    .sidebar a:hover { background-color: #34495e; padding-left: 25px; }
    .sidebar a.active { background-color: #1abc9c; color: white; font-weight: bold; border-left: 5px solid #2ecc71; }
    .sidebar .logout { margin-top: 30px; text-align: center; }
    .sidebar .logout a {
      background-color: #e74c3c; color: white; padding: 10px 15px;
      display: inline-block; border-radius: 5px; text-decoration: none;
    }
    .sidebar .logout a:hover { background-color: #c0392b; }
    .main-content { margin-left: 250px; padding: 20px; width: calc(100% - 250px); }
    form {
      background: #fff; padding: 20px; border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 400px;
    }
    label { display: block; margin: 10px 0 5px; font-weight: bold; }
    input, select {
      padding: 8px; width: 100%; border: 1px solid #ccc;
      border-radius: 5px; margin-bottom: 10px;
    }
    button {
      margin-top: 15px; padding: 8px 15px; background: #FF9800;
      border: none; color: #fff; cursor: pointer;
      border-radius: 5px; font-size: 16px;
    }
    button:hover { background: #E68900; }
    img { max-width: 150px; margin: 10px 0; display: block; }
    .success-msg {
      background: #d4edda;
      color: #155724;
      padding: 10px;
      border-radius: 5px;
      margin-bottom: 15px;
      border: 1px solid #c3e6cb;
    }
  </style>
</head>
<body>
  <div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="manage_users.php">Manage Users</a>
    <a href="manage_clubs.php">Manage Clubs</a>
    <a href="admin_reg.php">Manage Events</a>
    <a href="report.php">Reports</a>
    <a href="setting.php" class="active">Settings</a>
    <a href="more.php">More</a>
    <div class="logout"><a href="logout.php">Logout</a></div>
  </div>

  <div class="main-content">
    <h2>General Settings</h2>

    <?php if (isset($_GET['success'])): ?>
      <div class="success-msg">✅ Settings saved successfully!</div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
      <label for="site_name">Website Name</label>
      <input type="text" name="site_name" id="site_name" value="<?php echo htmlspecialchars($settings['site_name']); ?>">

      <label for="admin_email">Admin Email</label>
      <input type="email" name="admin_email" id="admin_email" value="<?php echo htmlspecialchars($settings['admin_email']); ?>">

      <label for="logo">Logo</label>
      <input type="file" name="logo" id="logo">
      <?php if (!empty($settings['logo'])): ?>
        <img src="<?php echo $settings['logo']; ?>" alt="Current Logo">
      <?php endif; ?>

      <label for="theme">Theme</label>
      <select name="theme" id="theme">
        <option value="light" <?php if($settings['theme']=="light") echo "selected"; ?>>Light</option>
        <option value="dark" <?php if($settings['theme']=="dark") echo "selected"; ?>>Dark</option>
      </select>

      <button type="submit">Save Changes</button>
    </form>
  </div>
</body>
</html>
