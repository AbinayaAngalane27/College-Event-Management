<?php
// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'college_event_management';
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$query = isset($_GET['query']) ? $_GET['query'] : '';
$response = '';

if (!empty($query)) {
    $sql = "SELECT title, category, event_date, event_time, location, coordinator, price FROM events 
            WHERE title LIKE ? OR category LIKE ?";
    $stmt = $conn->prepare($sql);
    $searchQuery = "%" . $query . "%";
    $stmt->bind_param('ss', $searchQuery, $searchQuery);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $response .= "Here are the events I found! <br>";
        while ($row = $result->fetch_assoc()) {
            $response .= "<strong>" . htmlspecialchars($row['title']) . "</strong> - " . htmlspecialchars($row['category']) . "<br>";
            $response .= "Date: " . htmlspecialchars($row['event_date']) . " | Time: " . htmlspecialchars($row['event_time']) . "<br>";
            $response .= "Location: " . htmlspecialchars($row['location']) . "<br>";
            $response .= "Coordinator: " . htmlspecialchars($row['coordinator']) . " | Registration Fee: " . htmlspecialchars($row['price']) . "<br><br>";
        }
    } else {
        $response = "Sorry, I couldn't find any events matching your query.";
    }
}

echo $response;
$conn->close();
?>
