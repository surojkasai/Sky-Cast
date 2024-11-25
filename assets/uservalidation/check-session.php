<?php
session_start();
header('Content-Type: application/json');

// Check for login status using the email session variable
if (isset($_SESSION['email'])) {
    // User is logged in
    echo json_encode(['loggedIn' => true, 'email' => $_SESSION['email']]);
} else {
    // User is not logged in
    echo json_encode(['loggedIn' => false]);
}
?>
