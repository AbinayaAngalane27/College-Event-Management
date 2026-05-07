<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

// Database connection
include('db_connect.php');

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

    // Prepare SQL query to insert event details into the database (using prepared statements to prevent SQL injection)
    $sql = "INSERT INTO events (category, title, coordinator, location, seat_status) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("sssss", $category, $event_name, $coordinator, $venue, $seat_status);
        
        // Execute the query
        if ($stmt->execute()) {
            // Set success message
            $status_message = "Event successfully created!";
        } else {
            // Set error message
            $status_message = "There was an error creating the event. Please try again.";
        }
        $stmt->close();
    } else {
        $status_message = "Database error. Please try again.";
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
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', sans-serif;
    background: #f1f5f9;
    color: #0f172a;
    min-height: 100vh;
    overflow-x: hidden;
}

header {
    background:
        linear-gradient(
            135deg,
            rgba(15, 23, 42, 0.96),
            rgba(30, 41, 59, 0.96)
        ),
        url('https://images.unsplash.com/photo-1511578314322-379afb476865?q=80&w=1600&auto=format&fit=crop');
    background-size: cover;
    background-position: center;
    padding: 70px 20px;
    text-align: center;
    position: relative;
    overflow: hidden;
}

/* Glow Effect */
header::before {
    content: "";
    position: absolute;
    width: 350px;
    height: 350px;
    background: rgba(59, 130, 246, 0.15);
    border-radius: 50%;
    top: -120px;
    left: -100px;
    filter: blur(60px);
}

/* Glow Effect 2 */
header::after {
    content: "";
    position: absolute;
    width: 300px;
    height: 300px;
    background: rgba(139, 92, 246, 0.18);
    border-radius: 50%;
    bottom: -120px;
    right: -100px;
    filter: blur(60px);
}

header h1 {
    position: relative;
    z-index: 2;
    font-size: 42px;
    font-weight: 700;
    color: #ffffff;
    letter-spacing: -1px;
    margin-bottom: 14px;
}

header p {
    position: relative;
    z-index: 2;
    color: #cbd5e1;
    font-size: 17px;
    max-width: 650px;
    margin: auto;
    line-height: 1.7;
}

nav {
    position: relative;
    z-index: 2;
    margin-top: 35px;
    display: flex;
    justify-content: center;
    gap: 16px;
    flex-wrap: wrap;
}

nav a {
    text-decoration: none;
    color: #ffffff;
    padding: 12px 22px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 14px;
    backdrop-filter: blur(12px);
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.12);
    transition: all 0.35s ease;
}

nav a:hover {
    background: #3b82f6;
    transform: translateY(-3px);
    box-shadow: 0 12px 25px rgba(59, 130, 246, 0.35);
}

section {
    width: 100%;
    max-width: 760px;
    margin: -50px auto 50px;
    position: relative;
    z-index: 5;
    padding: 0 20px;
}

form {
    background: #ffffff;
    border-radius: 24px;
    padding: 45px;
    box-shadow:
        0 15px 40px rgba(15, 23, 42, 0.08),
        0 4px 10px rgba(15, 23, 42, 0.04);
    border: 1px solid #e2e8f0;
    display: grid;
    gap: 22px;
    animation: fadeUp 0.7s ease;
}

label {
    font-size: 14px;
    font-weight: 600;
    color: #334155;
    margin-bottom: -12px;
}
input,
select {
    width: 100%;
    padding: 15px 18px;
    border-radius: 14px;
    border: 1px solid #cbd5e1;
    background: #f8fafc;
    font-size: 15px;
    color: #0f172a;
    transition: all 0.3s ease;
    outline: none;
}

/* Focus State */
input:focus,
select:focus {
    border-color: #3b82f6;
    background: #ffffff;
    box-shadow: 0 0 0 5px rgba(59, 130, 246, 0.12);
}

/* Placeholder */
input::placeholder {
    color: #94a3b8;
}

input[type="submit"] {
    background: linear-gradient(
        135deg,
        #2563eb,
        #3b82f6
    );
    color: #ffffff;
    border: none;
    padding: 16px;
    border-radius: 14px;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.35s ease;
    margin-top: 10px;
    letter-spacing: 0.3px;
}

/* Hover */
input[type="submit"]:hover {
    transform: translateY(-3px);
    box-shadow: 0 14px 30px rgba(37, 99, 235, 0.35);
}

/* Active */
input[type="submit"]:active {
    transform: scale(0.98);
}

.alert-success {
    background: #dcfce7;
    color: #166534;
    padding: 14px 18px;
    border-radius: 12px;
    margin-bottom: 20px;
    font-weight: 500;
}

.alert-error {
    background: #fee2e2;
    color: #991b1b;
    padding: 14px 18px;
    border-radius: 12px;
    margin-bottom: 20px;
    font-weight: 500;
}

@keyframes fadeUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 768px) {

    header {
        padding: 90px 20px;
    }

    header h1 {
        font-size: 30px;
    }

    header p {
        font-size: 15px;
    }

    section {
        margin-top: -40px;
    }

    form {
        padding: 28px;
        border-radius: 20px;
    }

    nav {
        gap: 10px;
    }

    nav a {
        width: 100%;
        max-width: 240px;
        text-align: center;
    }
}

@media (max-width: 480px) {

    header h1 {
        font-size: 26px;
    }

    form {
        padding: 22px;
    }

    input,
    select {
        padding: 13px 15px;
    }

    input[type="submit"] {
        padding: 14px;
    }
}
</style>

</body>
</html>
