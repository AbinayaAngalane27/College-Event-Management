<?php
// Include database connection
include('db_connect.php');

// Check if an event ID is provided
if (isset($_GET['id'])) {
    $event_id = intval($_GET['id']);

    // Prepare the delete query
    $delete_query = "DELETE FROM events WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $event_id);

    if ($stmt->execute()) {
        echo "<script>alert('Event deleted successfully.'); window.location.href = 'dashboard.php';</script>";
    } else {
        echo "<script>alert('Failed to delete event.'); window.location.href = 'dashboard.php';</script>";
    }
} else {
    echo "<script>alert('No event ID provided.'); window.location.href = 'admin_dashboard.php';</script>";
}

?>
