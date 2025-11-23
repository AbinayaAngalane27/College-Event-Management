<?php
// Database connection
$servername = "localhost";
$username = "root";  // Adjust with your database username
$password = "";  // Adjust with your database password
$dbname = "college_event_management";  // Adjust with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize a success or error message
$status_message = "";

// Process form data when the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $category = $_POST['category'];
    $event_name = $_POST['event_name'];
    $coordinator = $_POST['coordinator'];
    $venue = $_POST['venue'];
    $seat_status = $_POST['seat_status'];

    // Prepare SQL query to insert event details into the database
    $sql = "INSERT INTO events (category, title, coordinator,location , seat_status) 
            VALUES ('$category', '$event_name', '$coordinator', '$venue', '$seat_status')";

    // Execute the query
    if ($conn->query($sql) === TRUE) {
        // Set success message
        $status_message = "Event successfully created!";
    } else {
        // Set error message
        $status_message = "There was an error creating the event. Please try again.";
    }
}

// Close connection
$conn->close();
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
    <!-- Display success or error message -->
    <?php if ($status_message): ?>
        <script>
            alert("<?php echo $status_message; ?>");
        </script>
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

        <label for="seat_status">Seat Status:</label>
        <select name="seat_status" id="seat_status" required>
            <option value="available">Available</option>
            <option value="unavailable">Unavailable</option>
        </select>

        <input type="submit" value="Create Event" class="btn">
    </form>
</section>

<style>
    /* Section Styles for Create Event */
    section {
        margin: 20px;
        padding: 15px;
        background-color: white;
        border: 1px solid #ddd;
        border-radius: 5px;
    }

    section h2 {
        margin-top: 0;
    }

    /* Form Styles */
    form {
        display: grid;
        gap: 10px;
        max-width: 600px;
        margin: 0 auto;
    }

    label {
        font-weight: bold;
    }

    input, select {
        padding: 8px;
        font-size: 14px;
        width: 100%;
        border-radius: 5px;
        border: 1px solid #ddd;
    }

    input[type="submit"] {
        background-color: #0078d4;
        color: white;
        cursor: pointer;
    }

    input[type="submit"]:hover {
        background-color: #005bb5;
    }
</style>

</body>
</html>
