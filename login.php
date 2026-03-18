<?php
session_start();
include('db_connect.php');

date_default_timezone_set('Asia/Kolkata');

if (isset($_SESSION['admin_logged_in'])) {
    header('Location: dashboard.php');
    exit();
}

$error_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM admins WHERE username = ?";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $admin = $result->fetch_assoc();

            if (password_verify($password, $admin['password'])) {
                $admin_id = $admin['id'];
                $session_id = session_id();
                $session_start_time = date('Y-m-d H:i:s');
                $session_status = 'active';

                $update_query = "UPDATE admin_sessions SET session_end = ?, session_status = 'closed' WHERE admin_id = ? AND session_status = 'active'";
                $update_stmt = $conn->prepare($update_query);
                $current_time = date('Y-m-d H:i:s');
                $update_stmt->bind_param("si", $current_time, $admin_id);
                $update_stmt->execute();

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

                // ✅ FIX: visit_count now exists in admins table
                $new_visit_count = ($admin['visit_count'] ?? 0) + 1;
                $update_visit_stmt = $conn->prepare("UPDATE admins SET visit_count = ? WHERE id = ?");
                $update_visit_stmt->bind_param("ii", $new_visit_count, $admin_id);
                $update_visit_stmt->execute();

                $_SESSION['visit_count'] = $new_visit_count;
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin_id;
                $_SESSION['session_id'] = $session_id;

                header('Location: dashboard.php');
                exit();
            } else {
                $error_message = "Invalid password. Please try again."; // ✅ FIX: was $error
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
<body class="login-page">
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
    <div class="password-wrapper">
        <input type="password" id="password" name="password" placeholder="Enter your password" required>
        <span class="eye-icon" id="eyeToggle" onclick="togglePassword()" title="Show/Hide Password">
            <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#888" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#888" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
        </span>
    </div>
    <button type="submit">Login</button>
    <!-- ✅ FIX: correct variable + removed display:none -->
    <?php if (!empty($error_message)): ?>
        <div class="error"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>
</form>
<footer class="footer-animated">
    <p>&copy; 2K25 Rejouir. All rights reserved.</p>
</footer>
<style>
.login-page * { margin: 0; padding: 0; box-sizing: border-box; }
.login-page {
    font-family: Arial, sans-serif; background-color: #f3f4f6;
    display: flex; flex-direction: column; align-items: center;
    justify-content: center; min-height: 100vh; position: relative;
}
.login-page h2 { font-size: 32px; color: #00bcd4; margin-bottom: 20px; }
.login-page .container {
    background-color: white; padding: 30px; width: 300px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1); border-radius: 10px;
    display: flex; flex-direction: column; align-items: center; margin-top: 50px;
}
.login-page .container label { font-size: 14px; margin-bottom: 5px; color: #333; align-self: flex-start; }
.login-page .container input[type="text"],
.login-page .container input[type="password"] {
    width: 100%; padding: 10px; margin-bottom: 15px;
    border: 1px solid #ddd; border-radius: 5px; font-size: 14px;
}
.login-page .container input[type="text"]:focus,
.login-page .container input[type="password"]:focus {
    border-color: #00bcd4; outline: none; box-shadow: 0 0 5px rgba(0,188,212,0.5);
}
.login-page .container button[type="submit"] {
    width: 100%; padding: 10px; background-color: #00bcd4; color: white;
    border: none; border-radius: 5px; font-size: 16px; font-weight: bold;
    cursor: pointer; transition: background-color 0.3s ease;
}
.login-page .container button[type="submit"]:hover { background-color: #008c9e; }
.login-page .container .error { color: red; font-size: 14px; margin-top: 10px; }
/* Eye toggle styles */
.password-wrapper {
    position: relative;
    width: 100%;
    margin-bottom: 15px;
}
.password-wrapper input[type="password"],
.password-wrapper input[type="text"] {
    width: 100%;
    padding: 10px 40px 10px 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 14px;
    box-sizing: border-box;
    margin-bottom: 0;
}
.password-wrapper input:focus {
    border-color: #00bcd4;
    outline: none;
    box-shadow: 0 0 5px rgba(0,188,212,0.5);
}
.eye-icon {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    display: flex;
    align-items: center;
    user-select: none;
}
</style>
<script>
function togglePassword() {
    var input = document.getElementById('password');
    var eyeOpen   = document.getElementById('eyeOpen');
    var eyeClosed = document.getElementById('eyeClosed');
    if (input.type === 'password') {
        input.type = 'text';
        eyeOpen.style.display   = 'none';
        eyeClosed.style.display = 'inline';
    } else {
        input.type = 'password';
        eyeOpen.style.display   = 'inline';
        eyeClosed.style.display = 'none';
    }
}
</script>
</body>
</html>