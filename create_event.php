<?php
// ✅ FIX: Added session auth check - was missing entirely
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

include('db_connect.php');

$status_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category    = $_POST['category'];
    $event_name  = $_POST['event_name'];
    $coordinator = $_POST['coordinator'];
    $venue       = $_POST['venue'];
    $seat_status = $_POST['seat_status'];
    // ✅ FIX: Added missing required fields event_date, event_time, price
    $event_date  = $_POST['event_date'];
    $event_time  = $_POST['event_time'];
    $price       = $_POST['price'];

    // ✅ FIX: Use prepared statement (was raw string interpolation - SQL injection risk)
    $sql = "INSERT INTO events (category, title, coordinator, location, seat_status, event_date, event_time, price)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssd", $category, $event_name, $coordinator, $venue, $seat_status, $event_date, $event_time, $price);

    if ($stmt->execute()) {
        $status_message = "Event successfully created!";
    } else {
        $status_message = "There was an error creating the event: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header>
    <h1>Create New Event</h1>
    <p>Fill in the event details below to create a new event.</p>
    <nav>
        <a href="dashboard.php">Back to Dashboard</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>
<section>
    <?php if ($status_message): ?>
        <script>alert("<?php echo $status_message; ?>");</script>
    <?php endif; ?>
    <form action="create_event.php" method="POST">
        <label for="category">Category:</label>
        <select name="category" id="category" required>
            <option value="Technical">Technical</option>
            <option value="Cultural">Cultural</option>
            <option value="Sports">Sports</option>
            <option value="Gaming">Gaming</option>
            <option value="Literary">Literary</option>
        </select>

        <label for="event_name">Event Name:</label>
        <input type="text" id="event_name" name="event_name" required>

        <label for="coordinator">Coordinator:</label>
        <input type="text" id="coordinator" name="coordinator" required>

        <label for="venue">Venue:</label>
        <input type="text" id="venue" name="venue" required>

        <!-- ✅ FIX: Added missing required fields -->
        <label for="event_date">Event Date:</label>
        <input type="date" id="event_date" name="event_date" required>

        <label for="event_time">Event Time:</label>
        <input type="time" id="event_time" name="event_time" required>

        <label for="price">Registration Fee (₹):</label>
        <input type="number" id="price" name="price" step="0.01" min="0" value="0" required>

        <label for="seat_status">Seat Status:</label>
        <select name="seat_status" id="seat_status" required>
            <option value="Available">Available</option>
            <option value="unavailable">Unavailable</option>
        </select>

        <input type="submit" value="Create Event" class="btn">
    </form>
</section>
<style>
section { margin: 20px; padding: 15px; background-color: white; border: 1px solid #ddd; border-radius: 5px; }
form { display: grid; gap: 10px; max-width: 600px; margin: 0 auto; }
label { font-weight: bold; }
input, select { padding: 8px; font-size: 14px; width: 100%; border-radius: 5px; border: 1px solid #ddd; }
input[type="submit"] { background-color: #0078d4; color: white; cursor: pointer; }
input[type="submit"]:hover { background-color: #005bb5; }
</style>
</body>
</html>
