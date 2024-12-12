<?php
session_start();
include('db_connect.php'); // Connect to the database

// Check if email is passed via GET or session
if (isset($_GET['email'])) {
    $email = $_GET['email'];
    $_SESSION['email'] = $email; // Store in session for safety
} elseif (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
} else {
    die("No email provided for verification.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $entered_code = $_POST['verification_code'];

    // Use prepared statements to fetch the verification code
    $stmt = $conn->prepare("SELECT verification_code FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row && $entered_code == $row['verification_code']) {
        // Use prepared statements to update verification status
        $update_stmt = $conn->prepare("UPDATE users SET verification_status = 1 WHERE email = ?");
        $update_stmt->bind_param("s", $email);
        if ($update_stmt->execute()) {
            $message = "Email verified successfully!";
            // Redirect to login.php after a short delay
            header("Location: login.php");
            exit();
        } else {
            $message = "Failed to update verification status. Please try again.";
        }
    } else {
        $message = "Invalid verification code.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #1f1f1f; /* Dark background */
            color: #ffffff; /* Light text color */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #2d2d2d; /* Darker container background */
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Subtle shadow */
            width: 300px;
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
            color: #ffffff; /* Header text color */
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #444; /* Border for inputs */
            border-radius: 5px;
            background-color: #333; /* Input background */
            color: #ffffff; /* Input text color */
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #f4511e; /* Button color */
            border: none;
            border-radius: 5px;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #d63e00; /* Button hover color */
        }
        .message {
            text-align: center;
            margin-top: 15px;
            color: red; /* Message color */
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Verify Email</h2>
        <?php if (isset($message)) : ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <form method="POST" action="verify.php">
            <input type="text" name="verification_code" placeholder="Verification Code" required>
            <button type="submit">Verify</button>
        </form>
    </div>
</body>
</html>
