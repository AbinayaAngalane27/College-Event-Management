<?php
// Database credentials
$servername = "localhost";  // Use your MySQL server address
$username = "root";         // Your MySQL username
$password = "";             // Your MySQL password (if you set one)
$dbname = "college_event_management";  // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Database connected successfully!";
}
?>
