<?php
// ✅ FIX: Added session auth check - was missing entirely
session_start();
include('db_connect.php');

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_id']) && isset($_POST['new_location'])) {
    $event_id = $_POST['event_id'];
    $new_location = $_POST['new_location'];

    $check_query = "SELECT id FROM events WHERE location = ? AND seat_status = 'unavailable'";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("s", $new_location);
    $stmt->execute();
    $check_result = $stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'The selected location is already booked. Please choose a different location.']);
    } else {
        $update_query = "UPDATE events SET location = ?, seat_status = 'unavailable' WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("si", $new_location, $event_id);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Location updated successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error updating location. Please try again.']);
        }
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Committee Management</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<header>
    <h1>Committee Management</h1>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>
<section>
    <h2>Assign Locations to Events</h2>
    <table>
        <thead>
            <tr>
                <th>Event Name</th><th>Current Location</th>
                <th>Seat Status</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = "SELECT id, title, location, seat_status FROM events";
            $result = mysqli_query($conn, $query);
            while ($row = mysqli_fetch_assoc($result)):
            ?>
            <tr>
                <td><?php echo htmlspecialchars($row['title']); ?></td>
                <td><?php echo htmlspecialchars($row['location']); ?></td>
                <td><?php echo htmlspecialchars($row['seat_status']); ?></td>
                <td>
                    <form class="update-location-form" data-event-id="<?php echo $row['id']; ?>">
                        <input type="text" name="new_location" placeholder="Enter new location" required>
                        <button type="submit" class="btn">Update</button>
                    </form>
                    <a href="delete_committee.php?id=<?php echo $row['id']; ?>"
                       class="btn"
                       onclick="return confirm('Are you sure you want to delete this committee?');">
                       Delete
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</section>
<script>
$(document).ready(function() {
    $('.update-location-form').on('submit', function(e) {
        e.preventDefault();
        var eventId = $(this).data('event-id');
        var newLocation = $(this).find('input[name="new_location"]').val();
        $.ajax({
            url: 'committee.php',
            type: 'POST',
            data: { event_id: eventId, new_location: newLocation },
            success: function(response) {
                var data = JSON.parse(response);
                alert(data.message);
            },
            error: function() { alert('An error occurred. Please try again.'); }
        });
    });
});
</script>
<style>
body { margin: 0; font-family: Arial, sans-serif; background-color: #f4f4f9; }
header { background-color: #0078d4; color: white; padding: 1rem; text-align: center; }
header nav a { color: white; text-decoration: none; margin: 0 10px; font-weight: bold; }
header nav a:hover { text-decoration: underline; }
section { margin: 20px; padding: 15px; background-color: white; border: 1px solid #ddd; border-radius: 5px; }
table { width: 100%; border-collapse: collapse; margin-top: 10px; }
table thead { background-color: #0078d4; color: white; }
table th, table td { padding: 10px; text-align: left; border: 1px solid #ddd; }
.btn { display: inline-block; padding: 8px 12px; background-color: #0078d4; color: white; text-decoration: none; border-radius: 5px; font-size: 14px; cursor: pointer; border: none; }
.btn:hover { background-color: #005bb5; }
</style>
</body>
</html>
