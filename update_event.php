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
    $title       = $_POST['title']       ?? null;
    $category    = $_POST['category']    ?? null;
    $event_date  = $_POST['event_date']  ?? null;
    $event_time  = $_POST['event_time']  ?? null;
    $location    = $_POST['location']    ?? null;
    $coordinator = $_POST['coordinator'] ?? null;
    $price       = $_POST['price']       ?? null;
    $category_id = $_POST['category_id'] ?? null;
    $seat_status = $_POST['seat_status'] ?? 'Available'; // ✅ FIX: added missing seat_status

    try {
        $conn->begin_transaction();
        // ✅ FIX: added seat_status to UPDATE query
        $update_query = "UPDATE events
            SET title=?, category=?, event_date=?, event_time=?, location=?, coordinator=?, price=?, category_id=?, seat_status=?
            WHERE id=?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ssssssdisi", $title, $category, $event_date, $event_time, $location, $coordinator, $price, $category_id, $seat_status, $event_id);
        $stmt->execute();
        $conn->commit();
        echo "<script>alert('Event updated successfully!'); window.location.href='dashboard.php';</script>";
    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>alert('Error updating event: {$e->getMessage()}');</script>";
    }
}

$stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();

$categories = $conn->query("SELECT id, title FROM event_categories");
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
body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
.form-container { background-color: #fff; padding: 40px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); max-width: 550px; margin: 50px auto; }
h2 { text-align: center; color: blueviolet; margin-bottom: 20px; }
label { font-weight: bold; color: #555; display: block; margin-bottom: 5px; }
input[type="text"], input[type="date"], input[type="time"], input[type="number"], select {
    width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd;
    border-radius: 5px; font-size: 15px; box-sizing: border-box;
}
input:focus, select:focus { border-color: #0078d4; outline: none; box-shadow: 0 0 0 3px rgba(0,120,212,0.12); }
button { width: 100%; padding: 12px; background-color: #28a745; color: white; border: none; border-radius: 5px; font-size: 16px; font-weight: bold; cursor: pointer; }
button:hover { background-color: #218838; }
.back-link { display: block; margin-top: 15px; text-align: center; color: #007bff; text-decoration: none; }
.back-link:hover { text-decoration: underline; }
/* Seat status badge styling inside select */
.status-available { color: green; font-weight: bold; }
.status-unavailable     { color: red;   font-weight: bold; }
</style>
<div class="form-container">
    <h2>Update Event</h2>
    <form method="POST">

        <label>Event Title:</label>
        <input type="text" name="title"
               value="<?php echo htmlspecialchars($event['title']); ?>" required>

        <label>Category:</label>
        <select name="category" required>
            <?php foreach (['Technical','Cultural','Sports','Gaming','Literary'] as $opt): ?>
            <option value="<?php echo $opt; ?>"
                <?php echo $event['category'] === $opt ? 'selected' : ''; ?>>
                <?php echo $opt; ?>
            </option>
            <?php endforeach; ?>
        </select>

        <label>Category ID:</label>
        <select name="category_id">
            <?php while ($cat = $categories->fetch_assoc()): ?>
            <option value="<?php echo $cat['id']; ?>"
                <?php echo $event['category_id'] == $cat['id'] ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($cat['title']); ?>
            </option>
            <?php endwhile; ?>
        </select>

        <label>Date:</label>
        <input type="date" name="event_date"
               value="<?php echo htmlspecialchars($event['event_date']); ?>" required>

        <label>Time:</label>
        <input type="time" name="event_time"
               value="<?php echo htmlspecialchars($event['event_time']); ?>" required>

        <label>Location:</label>
        <input type="text" name="location"
               value="<?php echo htmlspecialchars($event['location']); ?>" required>

        <label>Coordinator:</label>
        <input type="text" name="coordinator"
               value="<?php echo htmlspecialchars($event['coordinator']); ?>" required>

        <label>Registration Fee (₹):</label>
        <input type="number" name="price" step="0.01" min="0"
               value="<?php echo htmlspecialchars($event['price']); ?>" required>

        <!-- ✅ FIX: Added Seat Status field that was completely missing -->
        <label>Seat Status:</label>
        <select name="seat_status" required>
            <option value="Available"
                <?php echo ($event['seat_status'] === 'Available') ? 'selected' : ''; ?>>
                ✅ Available
            </option>
            <option value="unavailable"
                <?php echo ($event['seat_status'] === 'unavailable') ? 'selected' : ''; ?>>
                🚫 Unavailable
            </option>
        </select>

        <button type="submit">Update</button>
    </form>
    <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
</div>
</body>
</html>