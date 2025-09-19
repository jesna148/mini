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

$email = $_SESSION['email'];
$name_from_signup = isset($_SESSION['name']) ? $_SESSION['name'] : "";

// Change GET to POST for any incoming data
$id = isset($_POST['id']) ? $_POST['id'] : null;

$sql = "SELECT * FROM students WHERE email='$email'";
$result = $conn->query($sql);

if ($result->num_rows === 0) {
    $conn->query("INSERT INTO students (name, email) VALUES ('$name_from_signup', '$email')");
    $result = $conn->query($sql);
}
$student = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $department = $conn->real_escape_string($_POST['department']);

    $profile_pic = $student['profile_pic'];
    if (!empty($_FILES['profile_pic']['name'])) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir);
        $fileName = time() . "_" . basename($_FILES["profile_pic"]["name"]);
        $targetFilePath = $targetDir . $fileName;

        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $targetFilePath)) {
                $profile_pic = $fileName;
            }
        } else {
            echo "<script>alert('Invalid file type. Please upload JPG, JPEG, PNG, or GIF.');</script>";
        }
    }

    $update = "UPDATE students 
               SET name='$name', phone='$phone', department='$department', profile_pic='$profile_pic' 
               WHERE email='$email'";
    if ($conn->query($update)) {
        header("Location: profile_view.php"); // ✅ Always go back to profile_view after saving
        exit();
    } else {
        echo "<script>alert('Error updating profile');</script>";
    }
}

$profilePic = (!empty($student['profile_pic'])) ? "uploads/" . $student['profile_pic'] : "uploads/default-profile.png";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Poppins', sans-serif;
            background: #f0f2f5;
            display: flex;
        }
        .sidebar {
            width: 250px;
            background: #1e293b;
            color: white;
            height: 100vh;
            padding: 20px 0;
            position: fixed;
            top: 0;
            left: 0;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.2);
        }
        .sidebar h2 {
            text-align: center;
            font-size: 22px;
            margin-bottom: 25px;
        }
        .sidebar a {
            display: block;
            padding: 12px 20px;
            color: #cbd5e1;
            text-decoration: none;
            transition: 0.3s;
        }
        .sidebar a:hover, .sidebar a.active {
            background: #2563eb;
            color: #fff;
        }
        .profile-container {
            margin-left: 260px;
            width: 450px;
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
            margin-top: 40px;
        }
        img {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            border: 3px solid #6a11cb;
            object-fit: cover;
            margin-bottom: 10px;
        }
        label {
            font-weight: 600;
            margin: 8px 0 3px;
            display: block;
            text-align: left;
        }
        input, select {
            padding: 8px;
            width: 100%;
            border-radius: 6px;
            border: 1px solid #ccc;
            margin-bottom: 10px;
        }
        .btn {
            background: #6a11cb;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            margin-top: 8px;
            font-weight: bold;
        }
        .btn:hover { background: #4b0ca3; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>📚 Student Panel</h2>
    <a href="student_home.php"><i class="fas fa-home"></i> Dashboard</a>
    <a href="events.php"><i class="fas fa-calendar-alt"></i> Events</a>
    <a href="clubs.php"><i class="fas fa-users"></i> Clubs</a>
    <a href="notification_std.php"><i class="fas fa-bell"></i> Notifications</a>
    <a href="feedback.php"><i class="fas fa-cog"></i> Feedback</a>
    <a href="profile_view.php" class="active"><i class="fas fa-user"></i> Profile</a> <!-- ✅ Always profile_view -->
    <a href="page1.html"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<div class="profile-container">
    <img src="<?php echo $profilePic; ?>" alt="Profile Picture">
    <h2>Edit Profile</h2>

    <form method="POST" enctype="multipart/form-data">
        <label>Full Name</label>
        <input type="text" name="name" value="<?php echo $student['name']; ?>" required>

        <label>Phone Number</label>
        <input type="text" name="phone"
               value="<?php echo $student['phone']; ?>"
               placeholder="Enter 10-digit phone number"
               pattern="[0-9]{10}"
               maxlength="10"
               title="Enter a valid 10-digit phone number"
               required>

        <label>Department</label>
        <select name="department" required>
            <option value="">-- Select Department --</option>
            <option value="CSE" <?php if($student['department']=="CSE") echo "selected"; ?>>CSE</option>
            <option value="IT" <?php if($student['department']=="IT") echo "selected"; ?>>IT</option>
            <option value="ECE" <?php if($student['department']=="ECE") echo "selected"; ?>>ECE</option>
            <option value="EEE" <?php if($student['department']=="EEE") echo "selected"; ?>>EEE</option>
            <option value="MECH" <?php if($student['department']=="MECH") echo "selected"; ?>>MECH</option>
            <option value="CIVIL" <?php if($student['department']=="CIVIL") echo "selected"; ?>>CIVIL</option>
            <option value="AIML" <?php if($student['department']=="AIML") echo "selected"; ?>>AIML</option>
            <option value="DS" <?php if($student['department']=="DS") echo "selected"; ?>>DS</option>
            <option value="CYBER" <?php if($student['department']=="CYBER") echo "selected"; ?>>CYBER</option>
            <option value="AI" <?php if($student['department']=="AI") echo "selected"; ?>>AI</option>
            <option value="IOT" <?php if($student['department']=="IOT") echo "selected"; ?>>IOT</option>
            <option value="MBA" <?php if($student['department']=="MBA") echo "selected"; ?>>MBA</option>
            <option value="MCA" <?php if($student['department']=="MCA") echo "selected"; ?>>MCA</option>
            <option value="BBA" <?php if($student['department']=="BBA") echo "selected"; ?>>BBA</option>
            <option value="BCA" <?php if($student['department']=="BCA") echo "selected"; ?>>BCA</option>
            <option value="PHARMA" <?php if($student['department']=="PHARMA") echo "selected"; ?>>PHARMA</option>
            <option value="BIO" <?php if($student['department']=="BIO") echo "selected"; ?>>BIO</option>
        </select>

        <label>Profile Picture</label>
        <input type="file" name="profile_pic">

        <button type="submit" class="btn">Save Profile</button>
    </form>
</div>
</body>
</html>
