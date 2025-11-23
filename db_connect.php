<?php
$host = "localhost";
$user = "root";
$pass = ""; // keep empty or put dummy text
$dbname = "college_event_management";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
