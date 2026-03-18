<?php
session_start();
include('db_connect.php');

// Redirect if admin is not logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

// Fetch all session details
$query = "SELECT session_id, admin_id, session_start, session_end, session_status FROM admin_sessions ORDER BY session_start DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Sessions</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 900px;
            margin: 50px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: blueviolet;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        .back-link {
            display: inline-block;
            margin-top: 15px;
            color: #007bff;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Admin Session Tracking</h2>
        <table>
            <tr>
                <th>Session ID</th>
                <th>Admin ID</th>
                <th>Session Start</th>
                <th>Session End</th>
                <th>Status</th>
            </tr>

            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['session_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['admin_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['session_start']); ?></td>
                        <td><?php echo htmlspecialchars($row['session_end'] ?: 'Active'); ?></td>
                        <td><?php echo htmlspecialchars($row['session_status']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No sessions found.</td>
                </tr>
            <?php endif; ?>
        </table>
        <a href="dashboard.php" class="back-link">Back to Dashboard</a>
    </div>
</body>
</html>
