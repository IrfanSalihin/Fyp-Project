<?php
// Start the session (if not already started)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    // User is logged in
    $response = array(
        'status' => 'success',
        'message' => 'User is logged in.',
        'user_id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        // Add any other user-related data you need
    );
} else {
    // User is not logged in
    $response = array(
        'status' => 'error',
        'message' => 'User is not logged in.',
    );
}

// Set the content type to JSON and echo the response
header('Content-Type: application/json');
echo json_encode($response);
