<?php
session_start();  // Start the session to track login status

// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();  // Ensure no further code is executed
}

// Include database connection
include('db_connect.php'); // Ensure this file exists and is in the correct path

// Fetch the visit count from session or database
$admin_id = $_SESSION['admin_id'];
$visit_count = isset($_SESSION['visit_count']) ? $_SESSION['visit_count'] : 1;

// Fetch events and associated committee data
$query = "SELECT e.id AS event_id, e.title AS event_name, e.category AS event_category, e.coordinator AS event_coordinator,
                 e.location AS committee_venue, e.coordinator AS committee_coordinator, e.seat_status
          FROM events e";

$result = mysqli_query($conn, $query);  // Now $conn should be initialized correctly
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<header>
    <h1>Welcome, Admin!</h1>
    <p>Manage events and committee updates.</p>
    <nav>
        <a href="committee.php">Committee</a>
        <a href="logout.php">Logout</a>
        <a href="report.php">Report</a>
        <a href="session.php">Session</a>
    </nav>
</header>

<section>
    <h2>Event Management</h2>
    <a href="create_event.php" class="btn btn-create-event">Create New Event</a>
    <table>
        <thead>
            <tr>
                <th>Category</th>
                <th>Event Name</th>
                <th>Coordinator</th>
                <th>Committee Details</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Loop through the events and committee data
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row['event_category'] . "</td>";
                echo "<td>" . $row['event_name'] . "</td>";
                echo "<td>" . $row['event_coordinator'] . "</td>";

                // Display committee details from 'location' column
                echo "<td>
                        <strong>Venue:</strong> " . $row['committee_venue'] . "<br>
                        <strong>Coordinator:</strong> " . $row['committee_coordinator'] . "<br>
                        <strong>Seat Status:</strong> " . $row['seat_status'] . "
                      </td>";

                echo "<td>
                        <a href='update_event.php?id=" . $row['event_id'] . "' class='btn'>Update</a>
                        <a href='delete_event.php?id=" . $row['event_id'] . "' class='btn' onclick=\"return confirm('Delete this event?');\">Delete</a>
                      </td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</section>

<section style="text-align: center; padding: 15px; background-color: #e8f4f8; border-radius: 5px; margin: 20px;">
    <p style="font-size: 18px; color: #0078d4; font-weight: bold;">
        👋 Hey Admin, it's your 
        <span style="color: #e74c3c;"><?php echo $visit_count; ?></span><?php 
            $suffix = 'th';
            if ($visit_count == 1) $suffix = 'st';
            elseif ($visit_count == 2) $suffix = 'nd';
            elseif ($visit_count == 3) $suffix = 'rd';
            echo $suffix;
        ?> 
        time visiting the dashboard! 🎉
    </p>
</section>

<style>
    /* General Reset */
body {
    margin: 0;
    font-family: Arial, sans-serif;
    background-color: #f4f4f9;
}

/* Header Styles */
header {
    background-color: #0078d4;
    color: white;
    padding: 1rem;
    text-align: center;
}

header h1 {
    margin: 0;
}

header nav {
    margin-top: 10px;
}

header nav a {
    color: white;
    text-decoration: none;
    margin: 0 10px;
    font-weight: bold;
}

header nav a:hover {
    text-decoration: underline;
}

/* Section Styles */
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

/* Table Styles */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

table thead {
    background-color: #0078d4;
    color: white;
}

table th, table td {
    padding: 10px;
    text-align: left;
    border: 1px solid #ddd;
}

table tbody tr:nth-child(even) {
    background-color: #f9f9f9;
}

/* Buttons */
.btn {
    display: inline-block;
    padding: 8px 12px;
    background-color: #0078d4;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    font-size: 14px;
}

.btn:hover {
    background-color: #005bb5;
}

/* Create Event Button */
.btn-create-event {
    margin: 10px 0;
}
</style>

</body>
</html>
