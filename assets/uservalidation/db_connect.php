<?php
$servername = "localhost"; // Your server name
$username = "root"; // Default XAMPP username is "root"
$password = ""; // Default XAMPP password is empty
$dbname = "weather_db1"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    // Output error message and log to console
    echo "<script>console.error('Connection failed: " . $conn->connect_error . "');</script>";
} else {
    // Output success message and log to console
    echo "<script>console.log('Database connected successfully!');</script>";
}
?>
