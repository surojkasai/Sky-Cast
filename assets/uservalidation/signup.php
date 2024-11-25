<?php 
session_start();
include('db_connect.php'); // Connect to the database

// Load Composer's autoloader
require __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash password

    // Check if email is a valid Gmail address
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/@gmail\.com$/', $email)) {
        $error = "Please enter a valid Gmail address (e.g., example@gmail.com).";
    } else {
        // Generate a verification code
        $verification_code = rand(100000, 999999);
        $verification_status = 0; // 0 = not verified, 1 = verified

        // Insert new user into the database with verification status and code
        $query = "INSERT INTO users (email, password, verification_code, verification_status) 
                  VALUES ('$email', '$password', '$verification_code', '$verification_status')";

        if (mysqli_query($conn, $query)) {
            // Create a new PHPMailer instance
            $mail = new PHPMailer(true);

            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Specify your SMTP server
                $mail->SMTPAuth = true;
                $mail->Username = 'skycast321@gmail.com'; // Your SMTP username
                $mail->Password = 'vglj bzpm yypd nkmm'; // Your SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587; // TCP port to connect to

                // Recipients
                $mail->setFrom('skycast321@gmail.com', 'Sky-Cast');
                $mail->addAddress($email); // Add recipient

                // Content
                $mail->isHTML(true); // Set email format to HTML
                $mail->Subject = 'Verify Your Email';
                $mail->Body    = "Your verification code is: <h2><b>$verification_code</b></h2>";
                $mail->AltBody = "Your verification code is: $verification_code"; // For non-HTML clients

                // Send email
                if ($mail->send()) {
                    // Redirect to the verify page after sending the email
                    header("Location: verify.php?email=" . urlencode($email));
                    exit();
                } else {
                    echo "Error sending verification email.";
                }
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            // Handle signup error
            $error = "Error signing up. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
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

        .signup-container {
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
        }

        button:hover {
            background-color: #d63e00;
        }

        .error {
            color: red;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <h2>Signup</h2>
        <form method="POST" action="signup.php">
            <input type="text" name="email" placeholder="Gmail Address" required> 
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Signup</button>
        </form>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
