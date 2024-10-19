<?php
session_start();
header('Content-Type: application/json');

// Example check for login status
if (isset($_SESSION['username'])) {
    // User is logged in
    echo json_encode(['loggedIn' => true, 'username' => $_SESSION['username']]);
} else {
    // User is not logged in
    echo json_encode(['loggedIn' => false]);
}
?>
