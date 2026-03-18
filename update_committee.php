<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('Invalid ID.'); window.location.href='dashboard.php';</script>";
    exit();
}
$id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM committee WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result    = $stmt->get_result();
$committee = $result->fetch_assoc();

if (!$committee) {
    echo "<script>alert('Committee not found.'); window.location.href='dashboard.php';</script>";
    exit();
}

$err = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $event_name       = trim($_POST['event_name']);
    $venue            = trim($_POST['venue']);
    $coordinator_name = trim($_POST['coordinator_name']);
    $seat_status      = $_POST['seat_status'];

    $upd = $conn->prepare("UPDATE committee SET event_name=?, venue=?, coordinator_name=?, seat_status=? WHERE id=?");
    $upd->bind_param("ssssi", $event_name, $venue, $coordinator_name, $seat_status, $id);

    if ($upd->execute()) {
        header("Location: dashboard.php");
        exit();
    } else {
        $err = "Error: " . $upd->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Committee - Admin</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<header>
    <h1>Update Committee</h1>
    <p>Edit the committee details for this event.</p>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="committee.php">Committee</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<section>
    <h2>Edit Committee Details</h2>

    <?php if ($err): ?>
        <div class="alert-error"><?php echo htmlspecialchars($err); ?></div>
    <?php endif; ?>

    <form method="POST" class="update-form">

        <div class="form-row">
            <label for="event_name">Event Name</label>
            <input type="text" id="event_name" name="event_name"
                   value="<?php echo htmlspecialchars($committee['event_name']); ?>" required>
        </div>

        <div class="form-row">
            <label for="venue">Venue</label>
            <input type="text" id="venue" name="venue"
                   value="<?php echo htmlspecialchars($committee['venue']); ?>" required>
        </div>

        <div class="form-row">
            <label for="coordinator_name">Coordinator Name</label>
            <input type="text" id="coordinator_name" name="coordinator_name"
                   value="<?php echo htmlspecialchars($committee['coordinator_name']); ?>" required>
        </div>

        <div class="form-row">
            <label for="seat_status">Seat Status</label>
            <select id="seat_status" name="seat_status">
                <option value="Available" <?php echo $committee['seat_status'] == 'Available' ? 'selected' : ''; ?>>
                    ✅ Available
                </option>
                <option value="Unavailable" <?php echo $committee['seat_status'] == 'Unavailable' ? 'selected' : ''; ?>>
                    🚫Unavailable
                </option>
            </select>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-save">💾 Save Changes</button>
            <a href="dashboard.php" class="btn btn-cancel">✖ Cancel</a>
        </div>

    </form>
</section>

<style>
body { margin: 0; font-family: Arial, sans-serif; background-color: #f4f4f9; }

/* Header - same as dashboard */
header { background-color: #0078d4; color: white; padding: 1rem; text-align: center; }
header h1 { margin: 0; font-size: 1.6rem; }
header p  { margin: 4px 0 8px; font-size: 0.9rem; opacity: 0.9; }
header nav { margin-top: 8px; }
header nav a { color: white; text-decoration: none; margin: 0 10px; font-weight: bold; font-size: 14px; }
header nav a:hover { text-decoration: underline; }

/* Section - same card style as dashboard */
section {
    margin: 30px auto;
    max-width: 600px;
    padding: 25px 30px;
    background-color: white;
    border: 1px solid #ddd;
    border-radius: 5px;
}
section h2 {
    margin-top: 0;
    color: #0078d4;
    border-bottom: 2px solid #e0ecf8;
    padding-bottom: 10px;
    margin-bottom: 20px;
    font-size: 1.1rem;
}

/* Form */
.update-form { display: flex; flex-direction: column; gap: 16px; }
.form-row { display: flex; flex-direction: column; gap: 5px; }
.form-row label {
    font-weight: bold; font-size: 13px; color: #555;
    text-transform: uppercase; letter-spacing: 0.4px;
}
.form-row input,
.form-row select {
    padding: 10px 12px; border: 1px solid #ddd;
    border-radius: 5px; font-size: 14px; width: 100%; box-sizing: border-box;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.form-row input:focus,
.form-row select:focus {
    border-color: #0078d4;
    box-shadow: 0 0 0 3px rgba(0,120,212,0.12);
    outline: none;
}

/* Buttons - same style as dashboard .btn */
.form-actions { display: flex; gap: 12px; margin-top: 8px; }
.btn {
    display: inline-block; padding: 10px 20px; border-radius: 5px;
    font-size: 14px; font-weight: bold; cursor: pointer;
    text-decoration: none; text-align: center; border: none; transition: background-color 0.2s;
}
.btn-save  { background-color: #0078d4; color: white; flex: 1; }
.btn-save:hover  { background-color: #005bb5; }
.btn-cancel { background-color: #e8e8e8; color: #333; flex: 1; }
.btn-cancel:hover { background-color: #d0d0d0; }

/* Error */
.alert-error {
    background-color: #fde8e8; color: #c0392b;
    border: 1px solid #f5c6c6; border-radius: 5px;
    padding: 10px 14px; margin-bottom: 16px; font-size: 14px;
}

@media (max-width: 640px) {
    section { margin: 15px; padding: 20px; }
    .form-actions { flex-direction: column; }
}
</style>

</body>
</html>