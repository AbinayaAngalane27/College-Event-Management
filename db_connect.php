<?php
$servername = "localhost";  // Change this if you're using a remote server
$username = "root";         // Default username for MySQL
$password = "";             // Default password for MySQL (set if different)
$dbname = "college_event_management";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>