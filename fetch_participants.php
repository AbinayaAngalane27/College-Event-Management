<?php
include('db_connect.php');

if (isset($_POST['event_id']) && isset($_POST['category_id'])) {
    $event_id    = intval($_POST['event_id']);
    $category_id = intval($_POST['category_id']);

    // ✅ FIX: was 'registrations_new' — table doesn't exist, use 'registrations'
    $query = "SELECT * FROM registrations WHERE event_id = ? AND category_id = ?";
    $stmt  = $conn->prepare($query);
    $stmt->bind_param("ii", $event_id, $category_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo '<table border="1">
                <tr>
                    <th>Name</th><th>Email</th><th>College</th>
                    <th>Degree</th><th>Department</th>
                </tr>';

        while ($row = $result->fetch_assoc()) {
            if (!isset($row['id']) || $row['id'] === NULL) {
                continue;
            }

            // Insert into participants table
            $insert_query = "INSERT INTO participants (event_id, category_id, registration_id) VALUES (?, ?, ?)";
            $insert_stmt  = $conn->prepare($insert_query);
            $insert_stmt->bind_param("iii", $event_id, $category_id, $row['id']);

            if ($insert_stmt->execute()) {
                echo '<tr>
                        <td>' . htmlspecialchars($row['name'])        . '</td>
                        <td>' . htmlspecialchars($row['email'])       . '</td>
                        <td>' . htmlspecialchars($row['college_name']). '</td>
                        <td>' . htmlspecialchars($row['degree'])      . '</td>
                        <td>' . htmlspecialchars($row['department'])  . '</td>
                      </tr>';
            } else {
                echo '<tr><td colspan="5">Error inserting participant: ' . $insert_stmt->error . '</td></tr>';
            }
        }
        echo '</table>';
    } else {
        echo '<p>No participants found for this event.</p>';
    }
} else {
    echo '<p>Event ID or Category ID not provided.</p>';
}
?>
