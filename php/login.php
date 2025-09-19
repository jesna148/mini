<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);


$conn = new mysqli("localhost", "root", "", "project");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);
}

   
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND role = ?");
    $stmt->bind_param("ss", $email, $role);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();


        
    if ($password===$row['password']) {
    
        $_SESSION['fullname'] = $row['fullname'];
        $_SESSION['email'] = $row['email'];
        $_SESSION['role'] = $row['role'];

        
        echo "<script>console.log('Approval Status: " . $row['is_approved'] . "');</script>";

       
        if ($role === 'admin') {
            echo "<script>
                window.location.href = 'admin_home.html';
            </script>";
            exit();
        }

      
        if (isset($row['is_approved']) && (int)$row['is_approved'] === 1) {

                 if ($role == 'student') {
                echo "<script>
                    window.location.href = 'student_home.php';
                </script>";
            } elseif ($role == 'club_leader') {
                echo "<script>
                    window.location.href = 'club_leader_dashboard.html';
                </script>";
            }
            exit();
        } else {
            $error = "Your account is pending admin approval!";
        }
    } else {
        $error = "Invalid email, password, or role!";
    }
    
}



    $stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Login</title>
    <style>
       
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #6e8efb, #a777e3);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        
        .login-container {
            background-color: white;
            padding: 30px 40px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
            width: 350px;
            text-align: center;
            animation: fadeIn 0.8s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h2 {
            margin-bottom: 15px;
            color: #333;
        }

        .input-group {
            margin-bottom: 15px;
            text-align: left;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #555;
        }

        input, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            outline: none;
            transition: border-color 0.3s;
        }

        input:focus, select:focus {
            border-color: #6e8efb;
        }

        button {
            width: 100%;
            padding: 10px;
            background: #6e8efb;
            border: none;
            color: white;
            font-size: 16px;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: #5a75e6;
        }

        .error {
            color: red;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .pending {
            display: none;
            color: #ff9800;
            font-weight: bold;
            margin-top: 10px;
        }
        
    </style>
</head>
<body>

<div class="login-container">
    <h2>Campus Login</h2>
    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
    <form method="POST" action="">
        <div class="input-group">
            <label>Email</label>
            <input type="email" name="email" required>
        </div>

        <div class="input-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>

        <div class="input-group">
            <label>Role</label>
            <select name="role" required>
                <option value="student">Student</option>
                <option value="club_leader">Club Leader</option>
                <option value="admin">Admin</option>
            </select>
        </div>

        <button type="submit">Login</button>
        <div class="login-link">
        Don't have an account? <a href="register.php">Register</a>
        </div>
    </form>

    <p id="pendingMsg" class="pending">Your account is pending approval!</p>
</div>
</body>
</html>