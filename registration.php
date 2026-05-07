<?php
// Enable error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Database connection
$servername = "localhost";
$username   = "root";
$password   = "";
$database   = "college_event_management";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// -------------------------------------------------------
// Read pre-selected event & category from GET params
// (passed by event pages like cultural-events.php)
// -------------------------------------------------------
$preselected_event_id    = isset($_GET['event_id'])    ? intval($_GET['event_id'])    : null;
$preselected_category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : null;

// Pre-fill event title and category title for display
$preselected_event_title    = '';
$preselected_category_title = '';

if ($preselected_event_id) {
    $r = $conn->query("SELECT title FROM events WHERE id = $preselected_event_id LIMIT 1");
    if ($r && $row_e = $r->fetch_assoc()) {
        $preselected_event_title = $row_e['title'];
    }
}
if ($preselected_category_id) {
    $r = $conn->query("SELECT title FROM event_categories WHERE id = $preselected_category_id LIMIT 1");
    if ($r && $row_c = $r->fetch_assoc()) {
        $preselected_category_title = $row_c['title'];
    }
}

// Fetch events and categories for fallback dropdowns
$events_result     = $conn->query("SELECT id, title FROM events");
$categories_result = $conn->query("SELECT id, title FROM event_categories");

// -------------------------------------------------------
// Handle form submission
// -------------------------------------------------------
$message              = "";
$saved_registration_id = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name         = trim($_POST['name']         ?? '');
    $email        = trim($_POST['email']        ?? '');
    $college_name = trim($_POST['college_name'] ?? '');
    $degree       = trim($_POST['degree']       ?? '');
    $department   = trim($_POST['department']   ?? '');
    $event_id     = !empty($_POST['event_id'])    ? intval($_POST['event_id'])    : null;
    $category_id  = !empty($_POST['category_id']) ? intval($_POST['category_id']) : null;

    $name_pattern  = "/^[a-zA-Z\s]+$/";
    $email_pattern = "/^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,6}$/";

    // Validation
    if (empty($name) || empty($email) || empty($college_name) || empty($degree) ||
        empty($department) || !$event_id || !$category_id) {
        $message = "All fields are required!";
    } elseif (!preg_match($name_pattern, $name)) {
        $message = "Invalid name format! Only letters and spaces are allowed.";
    } elseif (!preg_match($email_pattern, $email)) {
        $message = "Invalid email format!";
    } else {
        // Check if user already exists
        $stmt_check = $conn->prepare("SELECT id FROM registrations WHERE email = ? OR name = ? LIMIT 1");
        $stmt_check->bind_param("ss", $email, $name);
        $stmt_check->execute();
        $res_check = $stmt_check->get_result();

        if ($res_check->num_rows > 0) {
            // UPDATE existing record
            $existing = $res_check->fetch_assoc();
            $saved_registration_id = $existing['id'];
            $stmt_check->close();

            $stmt_upd = $conn->prepare(
                "UPDATE registrations
                 SET email=?, college_name=?, degree=?, department=?, event_id=?, category_id=?
                 WHERE id=?"
            );
            $stmt_upd->bind_param("ssssiis", $email, $college_name, $degree, $department,
                                             $event_id, $category_id, $saved_registration_id);
            if ($stmt_upd->execute()) {
                $message = "Your registration details have been updated successfully.";
            } else {
                $message = "Error updating record: " . $stmt_upd->error;
            }
            $stmt_upd->close();
        } else {
            $stmt_check->close();
            // INSERT new record
            $stmt_ins = $conn->prepare(
                "INSERT INTO registrations (name, email, college_name, degree, department, event_id, category_id)
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt_ins->bind_param("sssssii", $name, $email, $college_name, $degree, $department,
                                             $event_id, $category_id);
            if ($stmt_ins->execute()) {
                $saved_registration_id = $conn->insert_id;
                $message = "Registration successful! Thank you for registering.";
            } else {
                $message = "Error registering: " . $stmt_ins->error;
            }
            $stmt_ins->close();
        }
    }
}

// Keep connection open until after HTML output (results needed for dropdowns)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register for Event</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #e0f2f1, #ffffff);
            color: #333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        header {
            background-color: #00695c;
            color: #fff;
            padding: 25px 20px;
            text-align: center;
            box-shadow: 0 3px 8px rgba(0,0,0,0.15);
        }
        header h1 { font-size: 2.5rem; font-weight: 700; letter-spacing: 1.2px; }
        .container {
            background-color: #fff;
            width: 520px;
            max-width: 90%;
            margin: 40px auto 60px;
            padding: 40px 35px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        .container h2 {
            text-align: center;
            font-size: 1.9rem;
            font-weight: 700;
            color: #00796b;
            margin-bottom: 30px;
        }
        label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            color: #555;
            font-size: 0.95rem;
        }
        input[type="text"],
        input[type="email"],
        select {
            width: 100%;
            padding: 13px 16px;
            border: 1.8px solid #b0bec5;
            border-radius: 6px;
            font-size: 1rem;
            color: #333;
            margin-bottom: 18px;
            transition: border-color 0.25s;
        }
        input[type="text"]:focus,
        input[type="email"]:focus,
        select:focus {
            border-color: #00796b;
            box-shadow: 0 0 6px #00796baa;
            outline: none;
        }
        /* Read-only locked field for auto-filled event/category */
        .field-locked {
            width: 100%;
            padding: 13px 16px;
            border: 1.8px solid #a5d6a7;
            border-radius: 6px;
            font-size: 1rem;
            background-color: #f1f8f1;
            color: #2e7d32;
            margin-bottom: 18px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .field-locked .lock-icon { font-size: 0.85rem; opacity: 0.7; }
        button {
            width: 100%;
            padding: 15px;
            margin-top: 10px;
            font-size: 1.1rem;
            font-weight: 700;
            color: white;
            background: #00796b;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s;
            letter-spacing: 0.8px;
        }
        button:hover { background-color: #004d40; }
        .pay-button { background: #004d40; margin-top: 12px; }
        .pay-button:hover { background: #00332b; }
        .pay-button:disabled { background: #9e9e9e; cursor: not-allowed; }
        .msg-success { color: #2e7d32; font-weight: bold; text-align: center; margin-bottom: 18px; font-size: 1rem; }
        .msg-error   { color: #c62828; font-weight: bold; text-align: center; margin-bottom: 18px; font-size: 1rem; }
        @media (max-width: 600px) {
            .container { width: 95%; padding: 25px 18px; }
            header h1 { font-size: 1.9rem; }
        }
    </style>
</head>
<body>
<header>
    <h1>Register for the Event</h1>
</header>

<div class="container">
    <h2>Registration Form</h2>

    <?php if (!empty($message)): ?>
        <?php $isError = strpos($message, 'Error') !== false || strpos($message, 'required') !== false || strpos($message, 'Invalid') !== false; ?>
        <p class="<?php echo $isError ? 'msg-error' : 'msg-success'; ?>"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form method="POST" action="">

        <label for="name">Name:</label>
        <input type="text" id="name" name="name"
               value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email"
               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>

        <label for="college_name">College Name:</label>
        <input type="text" id="college_name" name="college_name"
               value="<?php echo isset($_POST['college_name']) ? htmlspecialchars($_POST['college_name']) : ''; ?>" required>

        <label for="degree">Degree:</label>
        <input type="text" id="degree" name="degree"
               value="<?php echo isset($_POST['degree']) ? htmlspecialchars($_POST['degree']) : ''; ?>" required>

        <label for="department">Department:</label>
        <input type="text" id="department" name="department"
               value="<?php echo isset($_POST['department']) ? htmlspecialchars($_POST['department']) : ''; ?>" required>

        <!-- =====================================================
             EVENT FIELD
             If coming from an event page → show locked field
             Otherwise → show dropdown
        ====================================================== -->
        <label>Event:</label>
        <?php
        $current_event_id = isset($_POST['event_id']) ? intval($_POST['event_id']) : $preselected_event_id;
        if ($preselected_event_id && $preselected_event_title): ?>
            <div class="field-locked">
                <span class="lock-icon">🔒</span>
                <?php echo htmlspecialchars($preselected_event_title); ?>
            </div>
            <input type="hidden" name="event_id" value="<?php echo $preselected_event_id; ?>">
        <?php else: ?>
            <select id="event_id" name="event_id" required>
                <option value="">-- Select an Event --</option>
                <?php $events_result->data_seek(0); while ($ev = $events_result->fetch_assoc()): ?>
                    <option value="<?php echo $ev['id']; ?>"
                        <?php echo ($current_event_id == $ev['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($ev['title']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        <?php endif; ?>

        <!-- =====================================================
             CATEGORY FIELD
             If coming from an event page → show locked field
             Otherwise → show dropdown
        ====================================================== -->
        <label>Event Category:</label>
        <?php
        $current_category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : $preselected_category_id;
        if ($preselected_category_id && $preselected_category_title): ?>
            <div class="field-locked">
                <span class="lock-icon">🔒</span>
                <?php echo htmlspecialchars($preselected_category_title); ?>
            </div>
            <input type="hidden" name="category_id" value="<?php echo $preselected_category_id; ?>">
        <?php else: ?>
            <select id="category_id" name="category_id" required>
                <option value="">-- Select a Category --</option>
                <?php $categories_result->data_seek(0); while ($cat = $categories_result->fetch_assoc()): ?>
                    <option value="<?php echo $cat['id']; ?>"
                        <?php echo ($current_category_id == $cat['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['title']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        <?php endif; ?>

        <button type="submit">Register</button>
    </form>

    <!-- Pay Now — only active after a successful registration -->
    <form action="payment.php" method="GET">
        <input type="hidden" name="registration_id"
               value="<?php echo $saved_registration_id ? intval($saved_registration_id) : ''; ?>">
        <?php if ($saved_registration_id): ?>
            <button type="submit" class="pay-button">💳 Pay Now</button>
        <?php else: ?>
            <button type="button" class="pay-button" disabled
                    onclick="alert('Please complete your registration first before proceeding to payment.')">
                💳 Pay Now
            </button>
        <?php endif; ?>
    </form>
</div>

<?php $conn->close(); ?>
</body>
</html>
