
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Login</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1') no-repeat center center/cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 10px;
            width: 300px;
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
        }
        .login-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        input, select, button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .signup-link {
            text-align: center;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Login</h2>
    <form action="" method="post">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" placeholder="Email" required>
        <label for="password">Password</label>
        <input type="password" name="password" id="password" placeholder="Password" required>

        <select name="role" id="role" required>
            <option value="">Select Role</option>
            <option value="student">Student</option>
            <option value="club_leader">Club Leader</option>
            <option value="admin">Admin</option>
        </select>

        <button type="submit">Login</button>
    </form>
    <div class="signup-link">
        Don't have an account? <a href="register.php">Sign Up</a>
    </div>
</div>
<?php
$conn = new mysqli("localhost","root","","project");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
else{
if ($_SERVER["REQUEST_METHOD"] == "POST"){
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];
 

    $sql = "SELECT * FROM users WHERE Email='$email' AND Password='$password' AND Role='$role'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
    session_start();
    $_SESSION['email'] = $email;
    $_SESSION['password'] = $password;
    $_SESSION['role'] = $role;

    echo "<script>
            alert('Login successful! Welcome, $role.');
            window.location.href = '" . ($role == 'student' ? 'student_dashboard.php' : ($role == 'club_leader' ? 'club_leader_dashboard.php' : 'admin_dashboard.php')) . "';
          </script>";
    exit();
   }
 else {
        echo "<script>alert('Invalid credentials or role!');</script>";
    }
}
}
$conn->close();
?>
</body>
</html>