<?php
include('db_connect.php');

// Check if category_id is passed
if (isset($_POST['category_id'])) {
    $category_id = $_POST['category_id'];
    
    // Fetch events for the selected category
    $query = "SELECT * FROM events WHERE category_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Generate options for events
    if ($result->num_rows > 0) {
        echo '<option value="">Select Event</option>';
        while ($row = $result->fetch_assoc()) {
            echo '<option value="' . $row['id'] . '">' . $row['title'] . '</option>';
        }
    } else {
        echo '<option value="">No events found</option>';
    }
}
?>
