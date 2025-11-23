<?php
session_start();
include('db_connect.php');

// Set timezone
date_default_timezone_set('Asia/Kolkata');

// Check if the user is already logged in
if (isset($_SESSION['admin_logged_in'])) {
    header('Location: dashboard.php');
    exit();
}

$error_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query to validate admin credentials
    $query = "SELECT * FROM admins WHERE username = ?";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $admin = $result->fetch_assoc();

            // Verify the password
            if (password_verify($password, $admin['password'])) {
                // Generate session details
                $admin_id = $admin['id'];
                $session_id = session_id();
                $session_start_time = date('Y-m-d H:i:s');
                $session_status = 'active';

                // End any active sessions for the same admin
                $update_query = "UPDATE admin_sessions SET session_end = ?, session_status = 'closed' WHERE admin_id = ? AND session_status = 'active'";
                $update_stmt = $conn->prepare($update_query);
                $current_time = date('Y-m-d H:i:s');
                $update_stmt->bind_param("si", $current_time, $admin_id);
                $update_stmt->execute();

                // Insert new session record
                $insert_query = "INSERT INTO admin_sessions (session_id, admin_id, session_start, session_status) VALUES (?, ?, ?, ?)";
                $insert_stmt = $conn->prepare($insert_query);
                if ($insert_stmt) {
                    $insert_stmt->bind_param("siss", $session_id, $admin_id, $session_start_time, $session_status);
                    if (!$insert_stmt->execute()) {
                        die("Error inserting session: " . $insert_stmt->error);
                    }
                } else {
                    die("Error preparing session insert query: " . $conn->error);
                }

                // Increment visit count in the database
                $new_visit_count = $admin['visit_count'] + 1;
                $update_visit_count_query = "UPDATE admins SET visit_count = ? WHERE id = ?";
                $update_stmt = $conn->prepare($update_visit_count_query);
                $update_stmt->bind_param("ii", $new_visit_count, $admin_id);
                $update_stmt->execute();

                // Store the new visit count in a session variable
                $_SESSION['visit_count'] = $new_visit_count;

                // Set session variables
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin_id;
                $_SESSION['session_id'] = $session_id;

                // Redirect to admin dashboard
                header('Location: dashboard.php');
                exit();
            } else {
                $error_message = "Invalid password. Please try again.";
            }
        } else {
            $error_message = "No account found with that username.";
        }
    } else {
        die("Error preparing login query: " . $conn->error);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rejouir - Admin Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="login-page"> <!-- Added class to target only login page -->
<nav>
    <a href="index.php">Home</a>
    <a href="event_categories.php">Events</a>
    <a href="login.php">Login</a>
    <a href="contact.php">Contact</a>
</nav>
<h2>Admin Login</h2>
<form method="POST" action="" class="container">
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" placeholder="Enter your username" required><br>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" placeholder="Enter your password" required><br>

    <button type="submit">Login</button>
    
    <div class="error"><?php echo isset($error) ? $error : ''; ?></div> <!-- Display error message -->
</form>
<footer class="footer-animated">
    <p>&copy; 2K25 Rejouir. All rights reserved.</p>
</footer>
<style>
    /* General reset */
.login-page * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* Body styling */
.login-page {
    font-family: Arial, sans-serif;
    background-color: #f3f4f6;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    position: relative;
}

/* Header styling */
.login-page h2 {
    font-size: 32px;
    color: #00bcd4;
    margin-bottom: 20px;
}

/* Form container styling */
.login-page .container {
    background-color: white;
    padding: 30px;
    width: 300px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-top: 50px; /* Adjust margin-top if needed */
}

/* Input field styling */
.login-page .container label {
    font-size: 14px;
    margin-bottom: 5px;
    color: #333;
    align-self: flex-start;
}

.login-page .container input[type="text"],
.login-page .container input[type="password"] {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 14px;
}

.login-page .container input[type="text"]:focus,
.login-page .container input[type="password"]:focus {
    border-color: #00bcd4;
    outline: none;
    box-shadow: 0 0 5px rgba(0, 188, 212, 0.5);
}

/* Button styling */
.login-page .container button[type="submit"] {
    width: 100%;
    padding: 10px;
    background-color: #00bcd4;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.login-page .container button[type="submit"]:hover {
    background-color: #008c9e;
}

/* Error message styling */
.login-page .container .error {
    color: red;
    font-size: 14px;
    margin-top: 10px;
    display: none; /* Initially hidden, can be displayed dynamically */
}
</style>
</body>
</html>