<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$servername = "localhost";
$username   = "root";
$password   = "";
$database   = "college_event_management"; // ✅ FIX: was "College_event_management" (case mismatch)

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$events_result     = $conn->query("SELECT id, title FROM events");
$categories_result = $conn->query("SELECT id, title FROM event_categories");

$message         = "";
$last_insert_id  = null; // ✅ FIX: track inserted ID for Pay Now button

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name         = trim($_POST['name']);
    $email        = trim($_POST['email']);
    $college_name = trim($_POST['college_name']);
    $degree       = trim($_POST['degree']);
    $department   = trim($_POST['department']);
    $event_id     = isset($_POST['event_id'])    ? trim($_POST['event_id'])    : null;
    $category_id  = isset($_POST['category_id']) ? trim($_POST['category_id']) : null;

    $name_pattern  = "/^[a-zA-Z\s]+$/";
    $email_pattern = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/";

    if (empty($name) || empty($email) || empty($college_name) || empty($degree) || empty($department) || is_null($event_id) || is_null($category_id)) {
        $message = "All fields are required!";
    } elseif (!preg_match($name_pattern, $name)) {
        $message = "Invalid name format! Only letters and spaces are allowed.";
    } elseif (!preg_match($email_pattern, $email)) {
        $message = "Invalid email format!";
    } else {
        // ✅ FIX: was 'registrations_new' - table doesn't exist, use 'registrations'
        $check_sql = "SELECT * FROM registrations WHERE email = ? OR name = ?";
        if ($stmt_check = $conn->prepare($check_sql)) {
            $stmt_check->bind_param("ss", $email, $name);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();

            if ($result_check->num_rows > 0) {
                $existing = $result_check->fetch_assoc();
                $last_insert_id = $existing['id'];

                // ✅ FIX: was 'registrations_new'
                $sql_update = "UPDATE registrations
                               SET email=?, college_name=?, degree=?, department=?, event_id=?, category_id=?
                               WHERE email=? OR name=?";
                if ($stmt_update = $conn->prepare($sql_update)) {
                    $stmt_update->bind_param("ssssssss", $email, $college_name, $degree, $department, $event_id, $category_id, $email, $name);
                    if ($stmt_update->execute()) {
                        $message = "Your details have been updated successfully.";
                    } else {
                        $message = "Error executing update: " . $stmt_update->error;
                    }
                    $stmt_update->close();
                }
            } else {
                // ✅ FIX: was 'registrations_new'
                $sql_insert = "INSERT INTO registrations (name, email, college_name, degree, department, event_id, category_id)
                               VALUES (?, ?, ?, ?, ?, ?, ?)";
                if ($stmt_insert = $conn->prepare($sql_insert)) {
                    $stmt_insert->bind_param("sssssii", $name, $email, $college_name, $degree, $department, $event_id, $category_id);
                    if ($stmt_insert->execute()) {
                        $last_insert_id = $conn->insert_id; // ✅ FIX: capture ID for Pay Now
                        $message = "Registration successful! Thank you for registering.";
                    } else {
                        $message = "Error: " . $stmt_insert->error;
                    }
                    $stmt_insert->close();
                }
            }
            $stmt_check->close();
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register for Event</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'Roboto', sans-serif; background: linear-gradient(135deg, #e0f2f1, #ffffff); color: #333; min-height: 100vh; display: flex; flex-direction: column; }
header { background-color: #00695c; color: #fff; padding: 25px 20px; text-align: center; box-shadow: 0 3px 8px rgba(0,0,0,0.15); }
header h1 { font-size: 2.5rem; font-weight: 700; letter-spacing: 1.2px; }
.container { background-color: #fff; width: 480px; max-width: 90%; margin: 40px auto 60px; padding: 40px 35px; border-radius: 12px; box-shadow: 0 8px 20px rgba(0,0,0,0.1); }
.container h2 { text-align: center; font-size: 1.9rem; color: #00796b; margin-bottom: 30px; }
label { display: block; font-weight: 600; margin-bottom: 8px; color: #555; font-size: 0.95rem; }
input[type="text"], input[type="email"], select {
    width: 100%; padding: 14px 16px; border: 1.8px solid #b0bec5; border-radius: 6px;
    font-size: 1rem; color: #333; transition: border-color 0.25s; margin-bottom: 15px;
}
input:focus, select:focus { border-color: #00796b; box-shadow: 0 0 6px #00796baa; outline: none; }
button { width: 100%; padding: 16px; margin-top: 10px; font-size: 1.1rem; font-weight: 700; color: white; background: #00796b; border: none; border-radius: 8px; cursor: pointer; transition: background-color 0.3s; }
button:hover { background-color: #004d40; }
.pay-button { background: #004d40; margin-top: 15px; }
.pay-button:hover { background: #00332b; }
p.msg { font-size: 1.1rem; color: #2e7d32; text-align: center; margin-bottom: 20px; }
</style>

<header><h1>Register for the Event</h1></header>
<div class="container">
    <h2>Registration Form</h2>
    <?php if (!empty($message)): ?>
        <p class="msg"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label>Name:</label>
        <input type="text" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>

        <label>Email:</label>
        <input type="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>

        <label>College Name:</label>
        <input type="text" name="college_name" value="<?php echo isset($_POST['college_name']) ? htmlspecialchars($_POST['college_name']) : ''; ?>" required>

        <label>Degree:</label>
        <input type="text" name="degree" value="<?php echo isset($_POST['degree']) ? htmlspecialchars($_POST['degree']) : ''; ?>" required>

        <label>Department:</label>
        <input type="text" name="department" value="<?php echo isset($_POST['department']) ? htmlspecialchars($_POST['department']) : ''; ?>" required>

        <label>Select Event:</label>
        <select name="event_id" required>
            <option value="">-- Select an Event --</option>
            <?php $events_result->data_seek(0); while ($event = $events_result->fetch_assoc()): ?>
                <option value="<?php echo $event['id']; ?>"
                    <?php echo (isset($_POST['event_id']) && $_POST['event_id'] == $event['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($event['title']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Select Category:</label>
        <select name="category_id" required>
            <option value="">-- Select a Category --</option>
            <?php $categories_result->data_seek(0); while ($category = $categories_result->fetch_assoc()): ?>
                <option value="<?php echo $category['id']; ?>"
                    <?php echo (isset($_POST['category_id']) && $_POST['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($category['title']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <button type="submit">Register</button>
    </form>

    <!-- ✅ FIX: Pass actual last_insert_id so payment.php receives a valid registration_id -->
    <?php if ($last_insert_id): ?>
    <form action="payment.php" method="GET">
        <input type="hidden" name="registration_id" value="<?php echo $last_insert_id; ?>">
        <button type="submit" class="pay-button">Pay Now</button>
    </form>
    <?php endif; ?>
</div>
</body>
</html>
