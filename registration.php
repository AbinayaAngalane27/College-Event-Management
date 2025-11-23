<?php
// Enable error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Database connection settings
$servername = "localhost";
$username = "root";
$password = ""; // Replace with your database password if set
$database = "College_event_management";

// Establish database connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch events and categories from the database for dropdowns
$events_result = $conn->query("SELECT id, title FROM events");
$categories_result = $conn->query("SELECT id, title FROM event_categories");

// Handle form submission
$message = ""; // Variable to hold the success message
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and assign POST data
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $college_name = trim($_POST['college_name']);
    $degree = trim($_POST['degree']);
    $department = trim($_POST['department']);
    $event_id = isset($_POST['event_id']) ? trim($_POST['event_id']) : null;
    $category_id = isset($_POST['category_id']) ? trim($_POST['category_id']) : null;

    // Regular Expression Validation
    $name_pattern = "/^[a-zA-Z\s]+$/"; // Only letters and spaces for name
    $email_pattern = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/"; // Valid email format

    // Ensure all fields are filled
    if (empty($name) || empty($email) || empty($college_name) || empty($degree) || empty($department) || is_null($event_id) || is_null($category_id)) {
        $message = "All fields are required!";
    } elseif (!preg_match($name_pattern, $name)) {
        $message = "Invalid name format! Only letters and spaces are allowed.";
    } elseif (!preg_match($email_pattern, $email)) {
        $message = "Invalid email format!";
    } else {
        // Check if the user already exists
        $check_sql = "SELECT * FROM registrations WHERE email = ? OR name = ?";
        if ($stmt_check = $conn->prepare($check_sql)) {
            $stmt_check->bind_param("ss", $email, $name);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();

            if ($result_check->num_rows > 0) {
                // User exists, so we update their information
                $sql_update = "UPDATE registrations 
                               SET email = ?, college_name = ?, degree = ?, department = ?, event_id = ?, category_id = ? 
                               WHERE email = ? OR name = ?";

                if ($stmt_update = $conn->prepare($sql_update)) {
                    $stmt_update->bind_param("ssssssss", $email, $college_name, $degree, $department, $event_id, $category_id, $email, $name);

                    if ($stmt_update->execute()) {
                        $message = "The details of this name have been updated successfully.";
                    } else {
                        $message = "Error executing update query: " . $stmt_update->error;
                    }
                    $stmt_update->close();
                } else {
                    $message = "Error preparing update statement: " . $conn->error;
                }
            } else {
                // New user, insert their details
                $sql_insert = "INSERT INTO registrations (name, email, college_name, degree, department, event_id, category_id) 
                               VALUES (?, ?, ?, ?, ?, ?, ?)";

                if ($stmt_insert = $conn->prepare($sql_insert)) {
                    $stmt_insert->bind_param("sssssss", $name, $email, $college_name, $degree, $department, $event_id, $category_id);

                    if ($stmt_insert->execute()) {
                        $message = "Registration successful! Thank you for registering.";
                    } else {
                        $message = "Error executing query: " . $stmt_insert->error;
                    }
                    $stmt_insert->close();
                } else {
                    $message = "Error preparing insert statement: " . $conn->error;
                }
            }
            $stmt_check->close();
        } else {
            $message = "Error preparing check statement: " . $conn->error;
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
    /* Reset and basics */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Roboto', sans-serif;
  background: linear-gradient(135deg, #e0f2f1, #ffffff);
  color: #333;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

/* Header */
header {
  background-color: #00695c;
  color: #fff;
  padding: 25px 20px;
  text-align: center;
  box-shadow: 0 3px 8px rgba(0,0,0,0.15);
}

header h1 {
  font-size: 2.5rem;
  font-weight: 700;
  letter-spacing: 1.2px;
}

/* Container */
.container {
  background-color: #fff;
  width: 480px;
  max-width: 90%;
  margin: 40px auto 60px;
  padding: 40px 35px;
  border-radius: 12px;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
  transition: box-shadow 0.3s ease;
}

.container:hover {
  box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
}

/* Form Title */
.container h2 {
  text-align: center;
  font-size: 1.9rem;
  font-weight: 700;
  color: #00796b;
  margin-bottom: 30px;
  letter-spacing: 0.6px;
}

/* Form Elements */
label {
  display: block;
  font-weight: 600;
  margin-bottom: 8px;
  color: #555;
  font-size: 0.95rem;
  user-select: none;
}

input[type="text"],
input[type="email"],
select {
  width: 100%;
  padding: 14px 16px;
  border: 1.8px solid #b0bec5;
  border-radius: 6px;
  font-size: 1rem;
  color: #333;
  transition: border-color 0.25s ease, box-shadow 0.25s ease;
  outline-offset: 2px;
  outline-color: transparent;
}

input[type="text"]:focus,
input[type="email"]:focus,
select:focus {
  border-color: #00796b;
  box-shadow: 0 0 6px #00796baa;
  outline-color: #00796b;
}

/* Button */
button {
  width: 100%;
  padding: 16px;
  margin-top: 20px;
  font-size: 1.1rem;
  font-weight: 700;
  color: white;
  background: #00796b;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: background-color 0.3s ease, box-shadow 0.3s ease;
  letter-spacing: 0.8px;
}

button:hover,
button:focus {
  background-color: #004d40;
  box-shadow: 0 6px 15px rgba(0, 77, 64, 0.5);
  outline: none;
}

/* Pay Button */
.pay-button {
  background: #004d40;
  margin-top: 15px;
}

.pay-button:hover {
  background: #00332b;
}

/* Success message */
p {
  font-size: 1.1rem;
  color: #2e7d32;
  text-align: center;
  margin-bottom: 20px;
}

/* Responsive */
@media (max-width: 600px) {
  .container {
    width: 95%;
    padding: 30px 20px;
    margin: 30px auto 50px;
  }

  header h1 {
    font-size: 2rem;
  }

  .container h2 {
    font-size: 1.6rem;
  }

  button {
    font-size: 1rem;
    padding: 14px;
  }
}

</style>

<header>
    <h1>Register for the Event</h1>
</header>

<div class="container">
    <h2>Registration Form</h2>
    <?php if (!empty($message)): ?>
        <p style="color: green; font-weight: bold;"><?php echo $message; ?></p>
    <?php endif; ?>
    <form method="POST" action="">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>

        <label for="college_name">College Name:</label>
        <input type="text" id="college_name" name="college_name" value="<?php echo isset($_POST['college_name']) ? htmlspecialchars($_POST['college_name']) : ''; ?>" required>

        <label for="degree">Degree:</label>
        <input type="text" id="degree" name="degree" value="<?php echo isset($_POST['degree']) ? htmlspecialchars($_POST['degree']) : ''; ?>" required>

        <label for="department">Department:</label>
        <input type="text" id="department" name="department" value="<?php echo isset($_POST['department']) ? htmlspecialchars($_POST['department']) : ''; ?>" required>

        <label for="event_id">Select Event:</label>
        <select id="event_id" name="event_id" required>
            <option value="">-- Select an Event --</option>
            <?php $events_result->data_seek(0); while ($event = $events_result->fetch_assoc()): ?>
                <option value="<?php echo $event['id']; ?>" 
                    <?php echo (isset($_POST['event_id']) && $_POST['event_id'] == $event['id']) ? 'selected' : ''; ?>>
                    <?php echo $event['title']; ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="category_id">Select Category:</label>
        <select id="category_id" name="category_id" required>
            <option value="">-- Select a Category --</option>
            <?php $categories_result->data_seek(0); while ($category = $categories_result->fetch_assoc()): ?>
                <option value="<?php echo $category['id']; ?>" 
                    <?php echo (isset($_POST['category_id']) && $_POST['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                    <?php echo $category['title']; ?>
                </option>
            <?php endwhile; ?>
        </select>

        <button type="submit">Register</button>
    </form>

    <!-- Pay Button - Redirect to payment.php -->
    <form action="payment.php" method="GET">
        <input type="hidden" name="registration_id" value="<?php echo isset($_POST['registration_id']) ? $_POST['registration_id'] : ''; ?>">
        <button type="submit" class="pay-button">Pay Now</button>
    </form>
</div>

</body>
</html>
