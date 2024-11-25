<?php
session_start();
require 'C:\xampp\htdocs\SKYB\vendor\autoload.php'; // Include PHPMailer via Composer (adjust path if needed)
include('db_connect.php'); // Include your database connection

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Check if the email exists in the database
    $query = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        // Generate a unique reset token
        $token = bin2hex(random_bytes(50));
        $expiry = date("Y-m-d H:i:s", strtotime('+1 hour')); // Token expiration time (1 hour)

        // Store the reset token and expiration in the database
        $updateQuery = "UPDATE users SET reset_token='$token', reset_token_expiry='$expiry' WHERE email='$email'";
        if (mysqli_query($conn, $updateQuery)) {
            // Prepare the password reset link
            $resetLink = "http://yourwebsite.com/reset_password.php?token=$token";

            // Setup PHPMailer
            $mail = new PHPMailer(true);
            try {
                // Server settings
                $mail->isSMTP();                                            // Send using SMTP
                $mail->Host       = 'smtp.gmail.com';                     // Set the SMTP server
                $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
                $mail->Username   = 'surojkasai@gmail.com';               // SMTP username
                $mail->Password   = 'qegp igvj vvuq hbfp';                  // SMTP password
                $mail->SMTPSecure = 'tls';                                  // Enable TLS encryption
                $mail->Port       = 587;                                    // TCP port for TLS

                // Recipients
                $mail->setFrom('surojkasai@gmail.com', 'Your Website');
                $mail->addAddress($email);                                  // Add the recipient's email address

                // Content
                $mail->isHTML(true);                                        // Set email format to HTML
                $mail->Subject = 'Password Reset Request';
                $mail->Body    = "Hi,<br><br>Click the link below to reset your password:<br><br>
                                 <a href='$resetLink'>$resetLink</a><br><br>This link will expire in 1 hour.";
                $mail->AltBody = "Hi,\n\nClick the link below to reset your password:\n\n$resetLink\n\nThis link will expire in 1 hour.";

                $mail->send();
                echo "A password reset link has been sent to your email.";
            } catch (Exception $e) {
                echo "Failed to send the reset link. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            echo "Failed to store the reset token. Please try again.";
        }
    } else {
        echo "No user found with that email address.";
    }
}
?>
