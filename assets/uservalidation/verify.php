<?php
session_start();
include('db_connect.php'); // Connect to the database

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_GET['email'];
    $entered_code = $_POST['verification_code'];

    // Fetch the correct verification code from the database
    $query = "SELECT verification_code FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);

    if ($row && $entered_code == $row['verification_code']) {
        // Update verification status
        $update_query = "UPDATE users SET verification_status = 1 WHERE email = '$email'";
        mysqli_query($conn, $update_query);
        $message = "Email verified successfully!";
        
        // Redirect to login.php after a short delay
        header("Location: login.php");
        exit();
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
            background-color: #1f1f1f; /* Dark background matching signup page */
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
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Subtle shadow for depth */
            width: 300px; /* Fixed width for the form */
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
            color: #ffffff; /* Make header text white */
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #444; /* Darker border to match the theme */
            border-radius: 5px;
            background-color: #333; /* Dark background for inputs */
            color: #ffffff; /* Light text inside inputs */
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #f4511e; /* Match the orange button color from signup page */
            border: none;
            border-radius: 5px;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #d63e00; /* Slightly darker orange on hover */
        }
        .message {
            text-align: center;
            margin-top: 15px;
            color: red; /* Error message color */
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Verify Email</h2>
        <?php if (isset($message)) : ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
        <form method="POST" action="verify.php?email=<?php echo urlencode($_GET['email']); ?>">
            <input type="text" name="verification_code" placeholder="Verification Code" required>
            <button type="submit">Verify</button>
        </form>
    </div>
</body>
</html>
