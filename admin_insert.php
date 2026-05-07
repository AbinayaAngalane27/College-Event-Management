<?php
// Include the database connection
require_once 'db_connect.php';

// Delete existing admin user to avoid duplicates
$delete_sql = "DELETE FROM admins WHERE username = 'admin'";
$conn->query($delete_sql);

// Hash the password for admin user
$hashed_password = password_hash("admin1234", PASSWORD_DEFAULT);

// Insert the admin user with hashed password
$sql = "INSERT INTO admins (username, password) VALUES ('admin', '$hashed_password')";

if ($conn->query($sql) === TRUE) {
    echo "<h2 style='color: green;'>✓ Admin user created successfully!</h2>";
    echo "<p><strong>Username:</strong> admin</p>";
    echo "<p><strong>Password:</strong> admin1234</p>";
    echo "<hr>";
    echo "<p>You can now <a href='login.php'>login here</a></p>";
} else {
    echo "<h2 style='color: red;'>Error creating admin user:</h2>";
    echo "<p>" . $conn->error . "</p>";
}

$conn->close();
?>