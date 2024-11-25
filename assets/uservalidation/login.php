<?php
session_start();
include('db_connect.php'); 

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if the user exists in the database
    $query = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    // If user exists and password matches
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['email'] = $user['email']; // Set session variable
        
        // Redirect to the full weather page after successful login
        header("Location:/SKYB/index.html");
        exit();  // Important to stop script execution after redirect
    } else {
        $error = "No account found with this email or incorrect password.";
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

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #444;
            border-radius: 5px;
            background-color: #333;
            color: #fff;
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
    margin-top: 20px; /* Add space between email input and button */
}


        button:hover {
            background-color: #d63e00;
        }

        .forgot-password {
            margin-top: 15px;
            display: inline-block;
            color: #ffffff;
        }

        .forgot-password:hover {
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
            <input type="text" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>

        <!-- Add Forgot Password Link -->
        <a href="#" class="forgot-password" id="forgotPasswordLink">Forgot your password?</a>
    </div>

   <!-- Forgot Password Modal -->
<div id="forgotPasswordModal" style="display:none;">
    <div class="login-container">
        <h2>Reset Password</h2>
        <form method="POST" action="forgot_password.php">
            <input type="email" name="email" placeholder="Enter your email" required>
            <button type="submit">Send Reset Link</button>
        </form>
    </div>
</div>


    <script>
        const forgotPasswordLink = document.getElementById('forgotPasswordLink');
        const forgotPasswordModal = document.getElementById('forgotPasswordModal');

        forgotPasswordLink.addEventListener('click', function() {
            // Hide the login form and show the forgot password form
            document.querySelector('.login-container').style.display = 'none';
            forgotPasswordModal.style.display = 'block';
        });
    </script>
</body>
</html>
