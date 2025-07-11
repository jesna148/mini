<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Signup</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: url('https://images.unsplash.com/photo-1551836022-d5d88e9218df') no-repeat center center/cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .signup-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 10px;
            width: 350px;
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
        }
        .signup-container h2 {
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
        .login-link {
            text-align: center;
        }
    </style>
</head>
<body>

<div class="signup-container">
    <h2>Sign Up</h2>
    <form action="" method="post">
        <label for="fullname">Full Name</label>
        <input type="text" id="fullname" name="fullname" placeholder="Full Name" required>
        <label for="email">Email</label>
        <input type="email" name="email" placeholder="Email" required>
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Password" required>

        <select name="role" id="role" required>
            <option value="">Select Role</option>
            <option value="student">Student</option>
            <option value="club_leader">Club Leader</option>
        </select>

        <button type="submit">
            Sign Up</button>
    </form>
    <div class="login-link">
        Already have an account? <a href="login.php">Login</a>
    </div>
</div>
<?php
$conn = new mysqli("localhost","root","","project");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
else{
if ($_SERVER["REQUEST_METHOD"] == "POST"){
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role=$_POST['role'];
   
    

        $sql = "INSERT INTO users (Fullname, Email, Password, Role) 
                VALUES ('$fullname','$email', '$password', '$role')";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Registration Successful!');</script>";
        } 
		else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

$conn->close();
?>
</body>
</html>
