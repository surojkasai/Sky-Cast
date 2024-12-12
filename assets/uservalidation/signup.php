<?php
session_start();
include('db_connect.php'); // Database connection

// Load Composer's autoloader
require __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash the password

    // Check if email is a valid Gmail address
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/@gmail\.com$/', $email)) {
        $error = "Please enter a valid Gmail address (e.g., example@gmail.com).";
    } else {
        // Check for duplicate email
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Email is already registered. Please use a different email.";
        } else {
            // Generate a verification code
            $verification_code = rand(100000, 999999);
            $verification_status = 0; // 0 = not verified

            // Insert new user into the database
            $stmt = $conn->prepare("INSERT INTO users (email, password, verification_code, verification_status) 
                                    VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssi", $email, $password, $verification_code, $verification_status);

            if ($stmt->execute()) {
                // Create a new PHPMailer instance
                $mail = new PHPMailer(true);

                try {
                    // Server settings
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com'; // Your SMTP server
                    $mail->SMTPAuth = true;
                    $mail->Username = 'skycast321@gmail.com'; // Your SMTP username
                    $mail->Password = 'vglj bzpm yypd nkmm'; // Your SMTP password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    // Recipients
                    $mail->setFrom('skycast321@gmail.com', 'Sky-Cast');
                    $mail->addAddress($email); // Add recipient

                    // Content
                    $mail->isHTML(true); // Set email format to HTML
                    $mail->Subject = 'Verify Your Email';
                    $mail->Body = "Your verification code is: <h2><b>$verification_code</b></h2>";
                    $mail->AltBody = "Your verification code is: $verification_code"; // For non-HTML clients

                    // Send email
                    if ($mail->send()) {
                        // Redirect to the verify page
                        header("Location: verify.php?email=" . urlencode($email));
                        exit();
                    } else {
                        $error = "Error sending verification email.";
                    }
                } catch (Exception $e) {
                    $error = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }
            } else {
                $error = "Error signing up. Please try again later.";
            }
        }

        $stmt->close();
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
