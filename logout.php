<?php
session_start();
include('db_connect.php');

// Set timezone
date_default_timezone_set('Asia/Kolkata');

// Ensure admin is logged in
if (isset($_SESSION['admin_logged_in'])) {
    $session_id = $_SESSION['session_id'];

    // Update session_end and session_status
    $logout_time = date('Y-m-d H:i:s');
    $update_query = "UPDATE admin_sessions SET session_end = ?, session_status = 'closed' WHERE session_id = ?";
    $stmt = $conn->prepare($update_query);
    if ($stmt) {
        $stmt->bind_param("ss", $logout_time, $session_id);
        $stmt->execute();
    }

    // Clear session variables
    session_unset();
    session_destroy();

    // Redirect to login page
    header('Location: login.php');
    exit();
}
?>
