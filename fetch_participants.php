<?php
include('db_connect.php');

// Check if event_id and category_id are passed
if (isset($_POST['event_id']) && isset($_POST['category_id'])) {
    $event_id = $_POST['event_id'];
    $category_id = $_POST['category_id'];

    // Fetch participants for the selected event - try different table names
    $tables_to_try = ['registrations', 'registrations_new', 'participants'];
    $found = false;
    
    foreach ($tables_to_try as $table_name) {
        // Check if table exists
        $check_table = $conn->query("SHOW TABLES LIKE '$table_name'");
        if ($check_table->num_rows > 0) {
            // Table exists, try to fetch data
            $query = "SELECT * FROM $table_name WHERE event_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $event_id);
            $stmt->execute();
            $result = $stmt->get_result();

            // Check if there are participants
            if ($result->num_rows > 0) {
                $found = true;
                // Display participants in a table
                echo '<h3>Participants List</h3>';
                echo '<table border="1">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>College</th>
                            <th>Degree</th>
                            <th>Department</th>
                            <th>Registration Date</th>
                        </tr>';

                // Display each participant
                while ($row = $result->fetch_assoc()) {
                    echo '<tr>
                            <td>' . htmlspecialchars($row['name'] ?? 'N/A') . '</td>
                            <td>' . htmlspecialchars($row['email'] ?? 'N/A') . '</td>
                            <td>' . htmlspecialchars($row['college_name'] ?? $row['college'] ?? 'N/A') . '</td>
                            <td>' . htmlspecialchars($row['degree'] ?? 'N/A') . '</td>
                            <td>' . htmlspecialchars($row['department'] ?? 'N/A') . '</td>
                            <td>' . htmlspecialchars($row['created_at'] ?? $row['registration_date'] ?? 'N/A') . '</td>
                          </tr>';
                }
                echo '</table>';
                echo '<p style="margin-top: 20px;"><strong>Total Participants:</strong> ' . $result->num_rows . '</p>';
                break; // Stop after finding data
            }
        }
    }
    
    if (!$found) {
        echo '<div style="padding: 20px; background-color: #fff3cd; border: 1px solid #ffc107; border-radius: 5px; margin: 20px 0;">';
        echo '<p><strong>No participants found for this event.</strong></p>';
        echo '<p>This could mean:</p>';
        echo '<ul>';
        echo '<li>No one has registered for this event yet</li>';
        echo '<li>The registrations table is empty</li>';
        echo '<li>The table structure might be different</li>';
        echo '</ul>';
        echo '</div>';
    }
} else {
    echo '<div style="padding: 20px; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px;">';
    echo '<p><strong>Error:</strong> Event ID or Category ID not provided.</p>';
    echo '</div>';
}
?>
