<?php
header('Content-Type: application/json');
$dsn = 'mysql:host=localhost;dbname=your_database;charset=utf8mb4';
$dbUser = 'root'; // Your database username
$dbPass = ''; // Your database password

// Connect to the database
try {
    $pdo = new PDO($dsn, $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Check if a file was uploaded
if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
    $uploadDir = 'uploads/'; // Directory to save uploaded files
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true); // Create directory if it doesn't exist
    }

    $fileName = uniqid() . '_' . basename($_FILES['avatar']['name']); // Generate a unique file name
    $targetFile = $uploadDir . $fileName;

    // Move the uploaded file to the target directory
    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetFile)) {
        // Save the file path in the database
        $userId = 1; // Replace with the actual user ID (e.g., from session data)
        $sql = "UPDATE users SET profile_image = :profile_image WHERE id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':profile_image' => $targetFile, ':user_id' => $userId]);

        echo json_encode([
            'success' => true,
            'imageUrl' => $targetFile, // Use the relative path to the uploaded file
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Failed to move uploaded file.',
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'error' => 'No file uploaded or an error occurred.',
    ]);
}
?>
