<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ✅ FIX: was "College_event_management" (case mismatch, fails on Linux)
$conn = mysqli_connect("localhost", "root", "", "college_event_management");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_GET['registration_id']) && !empty($_GET['registration_id'])) {
    $registration_id = intval($_GET['registration_id']); // ✅ FIX: use intval instead of escape string
} else {
    echo "<script>alert('Registration ID is missing.'); window.location.href = 'event_categories.php';</script>";
    exit;
}

// ✅ FIX: was 'registrations_new' — use 'registrations'
$query = "SELECT r.*, e.price FROM registrations r JOIN events e ON r.event_id = e.id WHERE r.id = ?";
$stmt  = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $registration_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result || mysqli_num_rows($result) == 0) {
    echo "<script>alert('Invalid Registration ID.'); window.location.href = 'event_categories.php';</script>";
    exit;
}
$row = mysqli_fetch_assoc($result);

// ✅ FIX: removed SUM(amount_paid) — column doesn't exist in payments schema
// Instead, check payment_status to determine if already paid
$payment_check_query = "SELECT payment_status FROM payments WHERE registration_id = ? ORDER BY id DESC LIMIT 1";
$pstmt = mysqli_prepare($conn, $payment_check_query);
mysqli_stmt_bind_param($pstmt, "i", $registration_id);
mysqli_stmt_execute($pstmt);
$payment_result = mysqli_stmt_get_result($pstmt);
$payment_row    = mysqli_fetch_assoc($payment_result);
$already_paid   = ($payment_row && $payment_row['payment_status'] === 'paid');

$price   = $row['price'];
$balance = $already_paid ? 0 : $price;
?>

<div class="payment-container">
    <h2>Complete Your Payment</h2>
    <?php if ($already_paid): ?>
        <p style="color:green; text-align:center; font-weight:bold;">✅ Payment already completed!</p>
    <?php else: ?>
    <form method="POST" action="">
        <label for="amount_due">Amount Due (₹):</label>
        <input type="number" id="amount_due" name="amount_due" value="<?php echo $balance; ?>" readonly>

        <label for="payment_method">Payment Method:</label>
        <select id="payment_method" name="payment_method" required onchange="showPaymentFields()">
            <option value="">Select Payment Method</option>
            <option value="Credit Card">Credit Card</option>
            <option value="Debit Card">Debit Card</option>
            <option value="UPI">UPI</option>
            <option value="Net Banking">Net Banking</option>
        </select>

        <div id="card_details" style="display:none;">
            <label>Card Number:</label>
            <input type="text" name="card_number" placeholder="Enter Card Number" maxlength="16">
            <label>Expiry Date (MM/YY):</label>
            <input type="text" name="expiry_date" placeholder="MM/YY" maxlength="5">
            <label>CVV:</label>
            <input type="text" name="cvv" placeholder="Enter CVV" maxlength="3">
        </div>

        <div id="upi_details" style="display:none;">
            <label>UPI ID:</label>
            <input type="text" name="upi_id" placeholder="Enter UPI ID">
        </div>

        <button type="submit" name="make_payment" class="submit-btn">Make Payment</button>
    </form>
    <?php endif; ?>
</div>

<style>
body { font-family: Arial, sans-serif; background-color: #f4f4f9; margin: 0; padding: 0; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
.payment-container { background-color: #fff; border-radius: 8px; padding: 30px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); width: 100%; max-width: 600px; }
h2 { font-size: 24px; text-align: center; margin-bottom: 20px; }
form { display: flex; flex-direction: column; }
label { margin-bottom: 8px; font-weight: bold; color: #333; }
input[type="number"], input[type="text"], select { padding: 12px; margin-bottom: 20px; border: 1px solid #ccc; border-radius: 4px; font-size: 16px; width: 100%; }
.submit-btn { background-color: #4CAF50; color: white; padding: 15px; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; transition: background-color 0.3s; width: 100%; }
.submit-btn:hover { background-color: #45a049; }
</style>

<script>
function showPaymentFields() {
    var method = document.getElementById("payment_method").value;
    document.getElementById("card_details").style.display = (method === "Credit Card" || method === "Debit Card") ? "block" : "none";
    document.getElementById("upi_details").style.display  = (method === "UPI") ? "block" : "none";
}
</script>

<?php
if (isset($_POST['make_payment'])) {
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
    $card_number    = isset($_POST['card_number'])  ? mysqli_real_escape_string($conn, $_POST['card_number'])  : NULL;
    $expiry_date    = isset($_POST['expiry_date'])   ? mysqli_real_escape_string($conn, $_POST['expiry_date'])  : NULL;
    $cvv            = isset($_POST['cvv'])           ? mysqli_real_escape_string($conn, $_POST['cvv'])          : NULL;
    $upi_id         = isset($_POST['upi_id'])        ? mysqli_real_escape_string($conn, $_POST['upi_id'])       : NULL;

    // ✅ FIX: removed 'amount_paid' from INSERT — column doesn't exist in payments schema
    $payment_query = "INSERT INTO payments (registration_id, event_id, payment_method, card_number, expiry_date, cvv, upi_id, payment_status)
                      VALUES ('$registration_id', '{$row['event_id']}', '$payment_method', " .
                      ($card_number ? "'$card_number'" : "NULL") . ", " .
                      ($expiry_date ? "'$expiry_date'" : "NULL") . ", " .
                      ($cvv        ? "'$cvv'"         : "NULL") . ", " .
                      ($upi_id     ? "'$upi_id'"      : "NULL") . ", 'paid')";

    if (mysqli_query($conn, $payment_query)) {
        echo "<script>alert('Payment successful! Your registration is confirmed.'); window.location.href = 'event_categories.php';</script>";
    } else {
        echo "Payment failed: " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>
