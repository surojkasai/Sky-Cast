<?php
session_start();
include('db_connect.php'); 

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username']; // Get username input
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if the user exists in the database
    $query = "SELECT username, email, password FROM users WHERE username = ? AND email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // If user exists and password matches
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['username'] = $user['username']; // Store username in session
        $_SESSION['email'] = $user['email'];  // Store email in session
        
        // Redirect to the full weather page after successful login
        header("Location: /SKYB-2.0/index.html");
        exit();  // Important to stop script execution after redirect
    } else {
        $error = "Invalid username, email, or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #1f1f1f;
            color: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background-color: #2d2d2d;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 300px;
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
            color: #ffffff;
        }

        .input-container {
            position: relative;
            width: 100%;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #444;
            border-radius: 5px;
            background-color: #333;
            color: #fff;
            padding-right: 40px; /* Space for the eye icon */
        }

        .eye-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #aaa;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #f4511e;
            border: none;
            border-radius: 5px;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 20px;
        }

        button:hover {
            background-color: #d63e00;
        }

        .signup-link {
            margin-top: 15px;
            color: #ffffff;
            display: inline-block;
            font-size: 14px;
        }

        .signup-link a {
            color: #f4511e;
            text-decoration: none;
            font-weight: bold;
        }

        .signup-link a:hover {
            text-decoration: underline;
        }

        .error {
            color: red;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <?php if (!empty($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <input type="text" name="username" placeholder="Username" required>
            <input type="text" name="email" placeholder="Email" required>
            
            <div class="input-container">
                <input type="password" id="password" name="password" placeholder="Password" required>
                <span class="eye-icon" onclick="togglePassword()">👁️</span>
            </div>

            <button type="submit">Login</button>
        </form>

        <p class="signup-link">
            Don't have an account? <a href="signup.php">Sign up</a>
        </p>
    </div>

    <script>
        function togglePassword() {
            var passwordField = document.getElementById("password");
            if (passwordField.type === "password") {
                passwordField.type = "text";
            } else {
                passwordField.type = "password";
            }
        }
    </script>
</body>
</html>
