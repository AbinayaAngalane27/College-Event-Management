<?php
// Include the database connection
include('db_connect.php');

// Check if 'id' is passed in the GET request
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $committee_id = $_GET['id']; // Get the committee ID from the request

    // Validate the 'id' to ensure it is numeric
    if (is_numeric($committee_id)) {
        // Use prepared statements to securely delete the record
        $delete_query = "DELETE FROM committee WHERE id = ?";
        $stmt = $conn->prepare($delete_query); // Prepare the query
        $stmt->bind_param("i", $committee_id); // Bind the parameter as an integer

        if ($stmt->execute()) {
            // Redirect with a success message
            echo "<script>
                    alert('Committee deleted successfully.');
                    window.location.href = 'committee.php';
                  </script>";
        } else {
            // Handle query execution errors
            echo "<script>
                    alert('Error deleting committee. Please try again.');
                    window.location.href = 'committee.php';
                  </script>";
        }
    } else {
        // If 'id' is invalid (not numeric)
        echo "<script>
                alert('Invalid committee ID.');
                window.location.href = 'committee.php';
              </script>";
    }
} else {
    // If 'id' is missing in the GET request
    echo "<script>
            alert('No committee ID specified.');
            window.location.href = 'committee.php';
          </script>";
}
?>
