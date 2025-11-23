<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

$event_id = $_GET['id'] ?? null;
if (!$event_id) {
    echo "<script>alert('No event selected.'); window.location.href='dashboard.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? null;
    $category = $_POST['category'] ?? null;
    $event_date = $_POST['event_date'] ?? null;
    $event_time = $_POST['event_time'] ?? null;  // New time input
    $location = $_POST['location'] ?? null;
    $coordinator = $_POST['coordinator'] ?? null;
    $price = $_POST['price'] ?? null;
    $category_id = $_POST['category_id'] ?? null;  // Handling category_id

    try {
        $conn->begin_transaction();

        $update_event_query = "
            UPDATE events 
            SET title = ?, category = ?, event_date = ?, event_time = ?, location = ?, coordinator = ?, price = ?, category_id = ? 
            WHERE id = ?";
        $stmt = $conn->prepare($update_event_query);
        $stmt->bind_param("ssssssddi", $title, $category, $event_date, $event_time, $location, $coordinator, $price, $category_id, $event_id);
        $stmt->execute();

        $conn->commit();
        echo "<script>alert('Event updated successfully!'); window.location.href='dashboard.php';</script>";
    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>alert('Error updating event: {$e->getMessage()}');</script>";
    }
}

// Fetch the event details
$query = "SELECT * FROM events WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();

// Fetch categories for the dropdown
$category_query = "SELECT id, title FROM event_categories";
$categories = $conn->query($category_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Event</title>
</head>
<body>
<style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .form-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            margin: 50px auto;
        }
        h2 {
            text-align: center;
            color: blueviolet;
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
            color: #555;
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"],
        input[type="date"],
        input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #28a745;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #218838;
        }
        .back-link {
            display: inline-block;
            margin-top: 15px;
            text-align: center;
            width: 100%;
            color: #007bff;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }

    </style>
    <div class="form-container">
        <h2>Update Event</h2>
        <form method="POST">
            <label>Event Title:</label>
            <input type="text" name="title" value="<?php echo htmlspecialchars($event['title']); ?>" required>

            <label>Category:</label>
            <input type="text" name="category" value="<?php echo htmlspecialchars($event['category_id']); ?>" required>
          </select>

            <label>Date:</label>
            <input type="date" name="event_date" value="<?php echo htmlspecialchars($event['event_date']); ?>" required>

            <label>Time:</label>
            <input type="time" name="event_time" value="<?php echo htmlspecialchars($event['event_time']); ?>" required>

            <label>Location:</label>
            <input type="text" name="location" value="<?php echo htmlspecialchars($event['location']); ?>" required>

            <label>Coordinator:</label>
            <input type="text" name="coordinator" value="<?php echo htmlspecialchars($event['coordinator']); ?>" required>

            <label>Registration Fee:</label>
            <input type="number" name="price" step="0.01" value="<?php echo htmlspecialchars($event['price']); ?>" required>

            <button type="submit">Update</button>
        </form>
    </div>
</body>
</html>
