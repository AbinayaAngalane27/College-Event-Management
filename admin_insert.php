<?php
// Include the database connection
require_once 'db_connect.php'; // This will include your db_connect.php file

// Hash the password for admin user
$hashed_password = password_hash('1234admin', PASSWORD_BCRYPT);

// Insert the admin user with hashed password
$sql = "INSERT INTO admins (username, password) VALUES ('admin', '$hashed_password')";

if ($conn->query($sql) === TRUE) {
    echo "Admin user created successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
