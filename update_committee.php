<?php
include('db_connect.php');

$id = $_GET['id'];
$query = "SELECT * FROM committee WHERE id = $id";
$result = mysqli_query($conn, $query);
$committee = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $event_name = $_POST['event_name'];
    $venue = $_POST['venue'];
    $coordinator_name = $_POST['coordinator_name'];
    $seat_status = $_POST['seat_status'];

    $update_query = "UPDATE committee 
                     SET event_name = '$event_name', venue = '$venue', 
                         coordinator_name = '$coordinator_name', seat_status = '$seat_status'
                     WHERE id = $id";

    if (mysqli_query($conn, $update_query)) {
        header("Location: admin_dashboard.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Update Committee</title>
</head>
<body>
    <form method="POST">
        <label>Event Name:</label>
        <input type="text" name="event_name" value="<?php echo $committee['event_name']; ?>" required>
        <label>Venue:</label>
        <input type="text" name="venue" value="<?php echo $committee['venue']; ?>" required>
        <label>Coordinator Name:</label>
        <input type="text" name="coordinator_name" value="<?php echo $committee['coordinator_name']; ?>" required>
        <label>Seat Status:</label>
        <select name="seat_status">
            <option value="Available" <?php echo $committee['seat_status'] == 'Available' ? 'selected' : ''; ?>>Available</option>
            <option value="Full" <?php echo $committee['seat_status'] == 'Full' ? 'selected' : ''; ?>>Full</option>
        </select>
        <button type="submit">Update Committee</button>
    </form>
</body>
</html>
