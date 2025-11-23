<?php
include('db_connect.php');

// Check if event_id and category_id are passed
if (isset($_POST['event_id']) && isset($_POST['category_id'])) {
    $event_id = $_POST['event_id'];
    $category_id = $_POST['category_id'];

    // Fetch participants for the selected event and category from the registrations_new table
    $query = "SELECT * FROM registrations_new WHERE event_id = ? AND category_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $event_id, $category_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if there are participants
    if ($result->num_rows > 0) {
        // Display participants in a table
        echo '<table border="1">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>College</th>
                    <th>Degree</th>
                    <th>Department</th>
                </tr>';

        // Insert each participant into the participants table
        while ($row = $result->fetch_assoc()) {
            // Check if registration_id is set and not NULL
            if (!isset($row['id']) || $row['id'] === NULL) {
                echo 'Registration ID is missing for participant: ' . htmlspecialchars($row['name']) . '<br>';
                continue; // Skip this participant if registration_id is missing or NULL
            }

            // Debugging: Print the values for event_id, category_id, and registration_id
            echo 'Event ID: ' . $event_id . ' | Category ID: ' . $category_id . ' | Registration ID: ' . $row['id'] . '<br>';

            // Insert participant into the participants table
            $insert_query = "INSERT INTO participants (event_id, category_id, registration_id) VALUES (?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_query);
            $insert_stmt->bind_param("iii", $event_id, $category_id, $row['id']);

            if ($insert_stmt->execute()) {
                echo '<tr>
                        <td>' . htmlspecialchars($row['name']) . '</td>
                        <td>' . htmlspecialchars($row['email']) . '</td>
                        <td>' . htmlspecialchars($row['college_name']) . '</td>
                        <td>' . htmlspecialchars($row['degree']) . '</td>
                        <td>' . htmlspecialchars($row['department']) . '</td>
                      </tr>';
            } else {
                echo 'Error inserting participant into participants table: ' . $insert_stmt->error . '<br>';
            }
        }
        echo '</table>';
    } else {
        echo 'No participants found for this event.';
    }
} else {
    echo 'Event ID or Category ID not provided.';
}
?>
