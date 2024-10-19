<?php
session_start();
include('db_connect.php'); 

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if the user exists in the database
    $query = "SELECT * FROM users WHERE username='$username'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    // If user exists and password matches
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['username'] = $user['username']; // Set session variable
        
        // Redirect to the full weather page after successful login
        header("Location:/SKYB/index.html");
        exit();  // Important to stop script execution after redirect
    } else {
        // If login fails, return the status (you can also display an error message)
        echo json_encode(['loggedIn' => false]); // Respond with not logged in status
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
    background-color: #1f1f1f; /* Dark background */
    color: #ffffff; /* Light text color */
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

.login-container {
    background-color: #2d2d2d; /* Darker container background */
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Subtle shadow for depth */
    width: 300px;
    text-align: center;
}

h2 {
    margin-bottom: 20px;
    color: #ffffff; /* Make header text white */
}

input[type="text"], input[type="password"] {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    border: 1px solid #444; /* Darker border to match the theme */
    border-radius: 5px;
    background-color: #333; /* Dark background for inputs */
    color: #fff; /* Light text inside inputs */
}

button {
    width: 100%;
    padding: 10px;
    background-color: #f4511e; /* Match the orange button color from weather page */
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
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>