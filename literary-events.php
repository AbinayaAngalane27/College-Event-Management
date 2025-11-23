<?php
// Include database connection
include('db_connect.php'); // Ensure this file contains your database connection details

// Fetch technical events from the database
$query = "SELECT title, event_date, location, coordinator, price, event_time FROM events WHERE category = 'Literary'";
$result = mysqli_query($conn, $query);

// Check if the query was successful and there are results
if ($result) {
    $events = mysqli_fetch_all($result, MYSQLI_ASSOC);
} else {
    die("Query failed: " . mysqli_error($conn)); // Display query error
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Literary Events - College Event Management</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        header {
            background-color: #4cafa7;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 28px;
        }
        nav {
    background-color: #333; /* Dark gray background for contrast */
    display: flex;
    justify-content: center;
    padding: 12px 0;
}

nav a {
    color: white;
    padding: 10px 15px;
    text-decoration: none;
    font-size: 16px;
    transition: background-color 0.3s, color 0.3s; /* Smooth transition for both */
}

nav a:hover {
    background-color: #4caf50; /* Green for hover effect */
    color: white; /* Ensure text remains readable */
}

nav .active {
    background-color: #4caf50; /* Active link color */
    font-weight: bold;
}

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
        }

        .event-category {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .event-category h3 {
            color: #333;
            margin-bottom: 20px;
        }

        .event-list {
            display: flex;
            flex-direction: column;
        }

        .event-item {
            background-color: #e0f7fa;
            margin: 10px 0;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .event-item h4 {
            margin: 0 0 10px;
        }

        button {
            background-color: #4cafa7;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #45a49f;
        }
        footer {
    background-color: #333; /* Dark background for contrast */
    color: white; /* Text color */
    text-align: center; /* Center the text */
    padding: 20px; /* Padding for spacing */
    position: relative; /* Positioning */
    bottom: 0; /* Stick to the bottom */
    width: 100%; /* Full width */
    box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
}

footer p {
    margin: 0; /* Remove default margin */
    font-size: 14px; /* Font size for better readability */
}
   </style>
</head>
<body>

    <header>
        <h1>Literary Events</h1>
    </header>

    <nav>
        <a href="index.php">Home</a>
        <a href="event_categories.php">Events</a>
        <a href="login.php">Login</a>
        <a href="contact.php">Contact</a>
    </nav>

    <div class="container">
        <h2>Upcoming Literary Events</h2>

        <div class="event-category">
            <h3>Literary Events</h3>
            <div class="event-list">
                <?php if (!empty($events)): ?>
                    <?php foreach ($events as $event): ?>
                        <div class="event-item">
                            <h4><?php echo htmlspecialchars($event['title']); ?></h4>
                            <p>Date: <?php echo htmlspecialchars($event['event_date']); ?></p>
                            <p>Location: <?php echo htmlspecialchars($event['location']); ?></p>
                            <p>Coordinator: <?php echo htmlspecialchars($event['coordinator']); ?></p>
                            <p>Time: <?php echo htmlspecialchars($event['event_time'] ?? 'Not Specified'); ?></p>
                            <p>Registration Fee: <?php echo htmlspecialchars($event['price']); ?></p>
                            <a href="registration.php" class="register-button">Register</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No upcoming Literary events available.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <footer class="footer-animated">
        <p>&copy; 2K25 Rejouir. All rights reserved.</p>
    </footer>

</body>
</html>
