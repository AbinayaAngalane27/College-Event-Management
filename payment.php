<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$conn = mysqli_connect("localhost", "root", "", "College_event_management");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if 'registration_id' parameter is set and not empty
if (isset($_GET['registration_id']) && !empty($_GET['registration_id'])) {
    $registration_id = mysqli_real_escape_string($conn, $_GET['registration_id']);
} else {
    echo "<script>alert('Registration ID is missing.'); window.location.href = 'event_categories.php';</script>";
    exit;
}

// Query to fetch registration and event details
$query = "
    SELECT r.*, e.price
    FROM registrations r
    JOIN events e ON r.event_id = e.id
    WHERE r.id = '$registration_id'
";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

if (mysqli_num_rows($result) == 0) {
    echo "<script>alert('Invalid Registration ID.'); window.location.href = 'event_categories.php';</script>";
    exit;
}

// Fetch the data if found
$row = mysqli_fetch_assoc($result);

// Query to fetch total amount already paid (if any)
$payment_query = "SELECT SUM(amount_paid) AS total_paid FROM payments WHERE registration_id = '$registration_id'";
$payment_result = mysqli_query($conn, $payment_query);
$payment_row = mysqli_fetch_assoc($payment_result);
$total_paid = $payment_row['total_paid'] ? $payment_row['total_paid'] : 0;

// Calculate the balance based on price
$price = $row['price'];
$balance = $price - $total_paid;

?>

<!-- HTML Payment Form -->
<div class="payment-container">
    <h2>Complete Your Payment</h2>
    <form method="POST" action="">
        <label for="amount_due">Amount Due:</label>
        <input type="number" id="amount_due" name="amount_due" value="<?= $balance; ?>" readonly>

        <label for="payment_method">Payment Method:</label>
        <select id="payment_method" name="payment_method" required onchange="showPaymentFields()">
            <option value="">Select Payment Method</option>
            <option value="Credit Card">Credit Card</option>
            <option value="Debit Card">Debit Card</option>
            <option value="UPI">UPI</option>
            <option value="Net Banking">Net Banking</option>
        </select>

        <!-- Credit/Debit Card Details -->
        <div id="card_details" style="display:none;">
            <label for="card_number">Card Number:</label>
            <input type="text" id="card_number" name="card_number" placeholder="Enter Card Number" maxlength="16">

            <label for="expiry_date">Expiry Date (MM/YY):</label>
            <input type="text" id="expiry_date" name="expiry_date" placeholder="MM/YY" maxlength="5">

            <label for="cvv">CVV:</label>
            <input type="text" id="cvv" name="cvv" placeholder="Enter CVV" maxlength="3">
        </div>

        <!-- UPI Details -->
        <div id="upi_details" style="display:none;">
            <label for="upi_id">UPI ID:</label>
            <input type="text" id="upi_id" name="upi_id" placeholder="Enter UPI ID">
        </div>

        <!-- Submit Button -->
        <button type="submit" name="make_payment" class="submit-btn">Make Payment</button>
    </form>
</div>

<style>
/* General page styles */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f9;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

/* Payment form container */
.payment-container {
    background-color: #ffffff;
    border-radius: 8px;
    padding: 30px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 600px;
}

h2 {
    font-size: 24px;
    text-align: center;
    margin-bottom: 20px;
}

/* Form styles */
form {
    display: flex;
    flex-direction: column;
}

/* Label and input styles */
label {
    margin-bottom: 8px;
    font-weight: bold;
    color: #333;
}

input[type="number"],
input[type="text"],
select {
    padding: 12px;
    margin-bottom: 20px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 16px;
    width: 100%;
}

input[type="text"]:focus, select:focus {
    border-color: #4CAF50;
}

.submit-btn {
    background-color: #4CAF50;
    color: white;
    padding: 15px;
    border: none;
    border-radius: 4px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s;
    width: 100%;
}

.submit-btn:hover {
    background-color: #45a049;
}

/* Hidden fields for different payment methods */
#card_details,
#upi_details {
    display: none;
}

/* Responsive design */
@media (max-width: 600px) {
    .payment-container {
        padding: 20px;
    }
}
</style>

<script>
// Function to toggle payment method fields based on selection
function showPaymentFields() {
    var paymentMethod = document.getElementById("payment_method").value;

    // Show appropriate fields based on selected payment method
    if (paymentMethod === "Credit Card" || paymentMethod === "Debit Card") {
        document.getElementById("card_details").style.display = "block";
        document.getElementById("upi_details").style.display = "none";
    } else if (paymentMethod === "UPI") {
        document.getElementById("card_details").style.display = "none";
        document.getElementById("upi_details").style.display = "block";
    } else {
        document.getElementById("card_details").style.display = "none";
        document.getElementById("upi_details").style.display = "none";
    }
}
</script>

<?php
// Handle form submission
if (isset($_POST['make_payment'])) {
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
    $amount_to_pay = mysqli_real_escape_string($conn, $_POST['amount_due']);

    // Additional fields based on payment method
    $card_number = isset($_POST['card_number']) ? mysqli_real_escape_string($conn, $_POST['card_number']) : NULL;
    $expiry_date = isset($_POST['expiry_date']) ? mysqli_real_escape_string($conn, $_POST['expiry_date']) : NULL;
    $cvv = isset($_POST['cvv']) ? mysqli_real_escape_string($conn, $_POST['cvv']) : NULL;
    $upi_id = isset($_POST['upi_id']) ? mysqli_real_escape_string($conn, $_POST['upi_id']) : NULL;

    // Insert payment record
    $payment_query = "INSERT INTO payments (registration_id, event_id, payment_method, amount_paid, card_number, expiry_date, cvv, upi_id) 
                      VALUES ('$registration_id', '{$row['event_id']}', '$payment_method', '$amount_to_pay', '$card_number', '$expiry_date', '$cvv', '$upi_id')";

    if (mysqli_query($conn, $payment_query)) {
        // Update payment status in the registrations table if balance is paid
        $new_balance = $balance - $amount_to_pay;
        if ($new_balance <= 0) {
            $update_query = "UPDATE registrations SET payment_status = 'paid' WHERE id = '$registration_id'";
            mysqli_query($conn, $update_query);
        }

        echo "<script>alert('Payment successful!'); window.location.href = 'event_categories.php';</script>";
    } else {
        echo "Payment failed: " . mysqli_error($conn);
    }
}

// Close the connection
mysqli_close($conn);
?>
