<?php
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

include('db_connect.php');

$admin_id = $_SESSION['admin_id'];
// ✅ FIX: Read visit_count from session (set during login)
$visit_count = isset($_SESSION['visit_count']) ? $_SESSION['visit_count'] : 1;

$query = "SELECT e.id AS event_id, e.title AS event_name, e.category AS event_category,
                 e.coordinator AS event_coordinator, e.location AS committee_venue,
                 e.coordinator AS committee_coordinator, e.seat_status
          FROM events e";
$result = mysqli_query($conn, $query);
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
                <th>Category</th><th>Event Name</th><th>Coordinator</th>
                <th>Committee Details</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['event_category']); ?></td>
                <td><?php echo htmlspecialchars($row['event_name']); ?></td>
                <td><?php echo htmlspecialchars($row['event_coordinator']); ?></td>
                <td>
                    <strong>Venue:</strong> <?php echo htmlspecialchars($row['committee_venue']); ?><br>
                    <strong>Coordinator:</strong> <?php echo htmlspecialchars($row['committee_coordinator']); ?><br>
                    <strong>Seat Status:</strong> <?php echo htmlspecialchars($row['seat_status']); ?>
                </td>
                <td>
                    <a href="update_event.php?id=<?php echo $row['event_id']; ?>" class="btn">Update</a>
                    <a href="delete_event.php?id=<?php echo $row['event_id']; ?>" class="btn"
                       onclick="return confirm('Delete this event?');">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</section>
<section>
    <p>Hey Admin, it's your <?php echo $visit_count; ?><?php
        echo ($visit_count == 1) ? 'st' : (($visit_count == 2) ? 'nd' : (($visit_count == 3) ? 'rd' : 'th'));
    ?> time visiting the dashboard.</p>
</section>
<style>
body { margin: 0; font-family: Arial, sans-serif; background-color: #f4f4f9; }
header { background-color: #0078d4; color: white; padding: 1rem; text-align: center; }
header h1 { margin: 0; }
header nav { margin-top: 10px; }
header nav a { color: white; text-decoration: none; margin: 0 10px; font-weight: bold; }
header nav a:hover { text-decoration: underline; }
section { margin: 20px; padding: 15px; background-color: white; border: 1px solid #ddd; border-radius: 5px; }
section h2 { margin-top: 0; }
table { width: 100%; border-collapse: collapse; margin-top: 10px; }
table thead { background-color: #0078d4; color: white; }
table th, table td { padding: 10px; text-align: left; border: 1px solid #ddd; }
table tbody tr:nth-child(even) { background-color: #f9f9f9; }
.btn { display: inline-block; padding: 8px 12px; background-color: #0078d4; color: white; text-decoration: none; border-radius: 5px; font-size: 14px; }
.btn:hover { background-color: #005bb5; }
.btn-create-event { margin: 10px 0; }
</style>
</body>
</html>
