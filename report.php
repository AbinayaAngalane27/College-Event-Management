<?php
session_start();
include('db_connect.php');

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

// Fetch event categories for the dropdown
$query = "SELECT * FROM event_categories";
$result = $conn->query($query);
$categories = $result->fetch_all(MYSQLI_ASSOC);

// Fetch events for the dropdown (based on selected category)
$events = [];
if (isset($_POST['category_id'])) {
    $category_id = $_POST['category_id'];
    $query = "SELECT * FROM events WHERE category_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $events = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Report</title>
</head>
<body>
<style>
    /* General body and layout styles */
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f4f4f9;
        color: #333;
    }

    /* Header styles */
    h1 {
        text-align: center;
        margin-top: 50px;
        font-size: 32px;
        color: #2c3e50;
    }

    /* Form container */
    form {
        width: 80%;
        max-width: 600px;
        margin: 50px auto;
        background-color: #fff;
        padding: 20px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }

    /* Label styles */
    label {
        display: block;
        font-size: 18px;
        margin-bottom: 10px;
        color: #2c3e50;
    }

    /* Select box styles */
    select {
        width: 100%;
        padding: 10px;
        font-size: 16px;
        margin-bottom: 20px;
        border: 1px solid #ddd;
        border-radius: 5px;
        background-color: #f9f9f9;
    }

    /* Submit button styles */
    input[type="submit"] {
        width: 100%;
        padding: 15px;
        font-size: 18px;
        background-color: #3498db;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    input[type="submit"]:hover {
        background-color: #2980b9;
    }

    /* Participants list styling */
    #participants_list {
        margin-top: 30px;
        padding: 20px;
        background-color: #fff;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        max-width: 1000px;
        margin-left: auto;
        margin-right: auto;
    }

    /* Table styling for participants list */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    table th, table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    table th {
        background-color: #3498db;
        color: white;
    }

    table tr:hover {
        background-color: #f1f1f1;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        form {
            width: 95%;
        }

        h1 {
            font-size: 28px;
        }

        input[type="submit"] {
            font-size: 16px;
        }
    }
</style>

<h1>Event Report</h1>

<form id="reportForm" method="POST" action="report.php">
    <label for="category_id">Select Event Category:</label>
    <select id="category_id" name="category_id">
        <option value="">Select Category</option>
        <?php foreach ($categories as $category): ?>
            <option value="<?= $category['id'] ?>"><?= $category['title'] ?></option>
        <?php endforeach; ?>
    </select>
    
    <label for="event_id">Select Event:</label>
    <select id="event_id" name="event_id">
        <option value="">Select Event</option>
        <?php if (!empty($events)): ?>
            <?php foreach ($events as $event): ?>
                <option value="<?= $event['id'] ?>"><?= $event['title'] ?></option>
            <?php endforeach; ?>
        <?php endif; ?>
    </select>
    
    <input type="submit" value="Fetch Participants">
</form>

<div id="participants_list"></div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Handle event category change to fetch events dynamically
    $('#category_id').change(function() {
        var category_id = $(this).val();
        if (category_id) {
            $.ajax({
                url: 'fetch_events.php',
                type: 'POST',
                data: { category_id: category_id },
                success: function(data) {
                    $('#event_id').html(data);
                }
            });
        } else {
            $('#event_id').html('<option value="">Select Event</option>');
        }
    });

    // Handle report form submission to fetch participants
    $('#reportForm').submit(function(e) {
        e.preventDefault();
        var category_id = $('#category_id').val();
        var event_id = $('#event_id').val();

        if (category_id && event_id) {
            $.ajax({
                url: 'fetch_participants.php',
                type: 'POST',
                data: { event_id: event_id, category_id: category_id },
                success: function(data) {
                    $('#participants_list').html(data);
                }
            });
        } else {
            alert("Please select both event category and event.");
        }
    });
});
</script>

</body>
</html>
