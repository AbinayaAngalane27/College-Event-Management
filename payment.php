<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$conn = mysqli_connect("localhost", "root", "", "college_event_management");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// -------------------------------------------------------
// Require a valid registration_id in GET
// -------------------------------------------------------
if (!isset($_GET['registration_id']) || empty($_GET['registration_id'])) {
    ?>
    <!DOCTYPE html><html><head><meta charset="UTF-8"><title>Error</title></head>
    <body style="font-family:Arial;text-align:center;padding:60px;">
        <h2 style="color:#c62828;">Registration ID is missing.</h2>
        <p>Please complete your registration first.</p>
        <a href="event_categories.php" style="color:#00796b;">Back to Events</a>
        <script>setTimeout(()=>{ window.location.href='event_categories.php'; }, 3000);</script>
    </body></html>
    <?php
    exit;
}

$registration_id = intval($_GET['registration_id']);

// Fetch registration + event details
$query = "SELECT r.*, e.price, e.title AS event_title
          FROM registrations r
          JOIN events e ON r.event_id = e.id
          WHERE r.id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $registration_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result || mysqli_num_rows($result) == 0) {
    ?>
    <!DOCTYPE html><html><head><meta charset="UTF-8"><title>Error</title></head>
    <body style="font-family:Arial;text-align:center;padding:60px;">
        <h2 style="color:#c62828;">Invalid Registration ID.</h2>
        <a href="event_categories.php" style="color:#00796b;">Back to Events</a>
        <script>setTimeout(()=>{ window.location.href='event_categories.php'; }, 3000);</script>
    </body></html>
    <?php
    exit;
}

$row = mysqli_fetch_assoc($result);

// Total already paid
$pay_stmt = mysqli_prepare($conn, "SELECT COALESCE(SUM(amount_paid),0) AS total_paid FROM payments WHERE registration_id = ?");
mysqli_stmt_bind_param($pay_stmt, "i", $registration_id);
mysqli_stmt_execute($pay_stmt);
$pay_result = mysqli_stmt_get_result($pay_stmt);
$pay_row    = mysqli_fetch_assoc($pay_result);
$total_paid = floatval($pay_row['total_paid']);
$price      = floatval($row['price']);
$balance    = $price - $total_paid;

// -------------------------------------------------------
// Handle payment submission
// -------------------------------------------------------
$pay_message = "";
$pay_success = false;

if (isset($_POST['make_payment'])) {
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method'] ?? '');
    $amount_to_pay  = floatval($_POST['amount_due'] ?? 0);
    $card_number    = !empty($_POST['card_number'])  ? mysqli_real_escape_string($conn, $_POST['card_number'])  : NULL;
    $expiry_date    = !empty($_POST['expiry_date'])   ? mysqli_real_escape_string($conn, $_POST['expiry_date'])  : NULL;
    $cvv            = !empty($_POST['cvv'])            ? mysqli_real_escape_string($conn, $_POST['cvv'])           : NULL;
    $upi_id         = !empty($_POST['upi_id'])         ? mysqli_real_escape_string($conn, $_POST['upi_id'])        : NULL;
    $bank_name      = !empty($_POST['bank_name'])      ? mysqli_real_escape_string($conn, $_POST['bank_name'])     : NULL;

    if (empty($payment_method)) {
        $pay_message = "Please select a payment method.";
    } else {
        $ins = "INSERT INTO payments
                    (registration_id, event_id, payment_method, amount_paid, card_number, expiry_date, cvv, upi_id)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $ins_stmt = mysqli_prepare($conn, $ins);
        $event_id_val = intval($row['event_id']);
        mysqli_stmt_bind_param($ins_stmt, "iidsssss",
            $registration_id, $event_id_val, $payment_method, $amount_to_pay,
            $card_number, $expiry_date, $cvv, $upi_id);

        if (mysqli_stmt_execute($ins_stmt)) {
            $new_balance = $balance - $amount_to_pay;
            if ($new_balance <= 0) {
                $upd = mysqli_prepare($conn, "UPDATE registrations SET payment_status='paid' WHERE id=?");
                mysqli_stmt_bind_param($upd, "i", $registration_id);
                mysqli_stmt_execute($upd);
            }
            $pay_success = true;
            $balance     = max(0, $new_balance);
        } else {
            $pay_message = "Payment failed: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - College Event Management</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #e0f2f1, #f5f5f5);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 15px;
        }
        .payment-container {
            background: #fff;
            border-radius: 12px;
            padding: 40px 35px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
            width: 100%;
            max-width: 560px;
        }
        h2 {
            font-size: 1.8rem;
            text-align: center;
            color: #00695c;
            margin-bottom: 8px;
        }
        .event-name {
            text-align: center;
            color: #555;
            margin-bottom: 28px;
            font-size: 1rem;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            background: #f1f8f6;
            border-radius: 8px;
            padding: 14px 18px;
            margin-bottom: 22px;
            font-size: 1rem;
        }
        .info-row span:last-child { font-weight: 700; color: #00796b; }
        label {
            display: block;
            font-weight: 600;
            margin-bottom: 7px;
            color: #444;
            font-size: 0.95rem;
        }
        input[type="number"],
        input[type="text"],
        select {
            width: 100%;
            padding: 12px 14px;
            margin-bottom: 18px;
            border: 1.6px solid #b0bec5;
            border-radius: 6px;
            font-size: 1rem;
            color: #333;
            transition: border-color 0.2s;
        }
        input:focus, select:focus {
            border-color: #00796b;
            box-shadow: 0 0 5px rgba(0,121,107,0.35);
            outline: none;
        }
        .method-section { display: none; }
        .submit-btn {
            width: 100%;
            padding: 15px;
            background: #00796b;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.3s;
            letter-spacing: 0.5px;
            margin-top: 6px;
        }
        .submit-btn:hover { background: #004d40; }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 16px;
            color: #00796b;
            text-decoration: none;
            font-size: 0.95rem;
        }
        .back-link:hover { text-decoration: underline; }
        .alert-success {
            background: #e8f5e9;
            border: 1px solid #a5d6a7;
            color: #2e7d32;
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 600;
        }
        .alert-error {
            background: #ffebee;
            border: 1px solid #ef9a9a;
            color: #c62828;
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 600;
        }
        @media (max-width: 600px) {
            .payment-container { padding: 28px 18px; }
        }
    </style>
</head>
<body>
<div class="payment-container">
    <h2>💳 Complete Payment</h2>
    <p class="event-name">Event: <strong><?php echo htmlspecialchars($row['event_title']); ?></strong></p>

    <?php if ($pay_success): ?>
        <div class="alert-success">
            ✅ Payment successful! Redirecting…
        </div>
        <script>setTimeout(()=>{ window.location.href='event_categories.php'; }, 2500);</script>
    <?php elseif (!empty($pay_message)): ?>
        <div class="alert-error"><?php echo htmlspecialchars($pay_message); ?></div>
    <?php endif; ?>

    <?php if (!$pay_success): ?>

    <div class="info-row">
        <span>Total Fee</span>
        <span>₹<?php echo number_format($price, 2); ?></span>
    </div>
    <div class="info-row">
        <span>Amount Paid</span>
        <span>₹<?php echo number_format($total_paid, 2); ?></span>
    </div>
    <div class="info-row">
        <span>Balance Due</span>
        <span>₹<?php echo number_format($balance, 2); ?></span>
    </div>

    <form method="POST" action="">

        <label for="amount_due">Amount to Pay (₹):</label>
        <input type="number" id="amount_due" name="amount_due"
               value="<?php echo number_format($balance, 2, '.', ''); ?>"
               min="1" max="<?php echo $balance; ?>" step="0.01" readonly>

        <label for="payment_method">Payment Method:</label>
        <select id="payment_method" name="payment_method" required onchange="showPaymentFields()">
            <option value="">-- Select Payment Method --</option>
            <option value="Credit Card">Credit Card</option>
            <option value="Debit Card">Debit Card</option>
            <option value="UPI">UPI</option>
            <option value="Net Banking">Net Banking</option>
        </select>

        <!-- Credit / Debit Card -->
        <div id="card_details" class="method-section">
            <label for="card_number">Card Number:</label>
            <input type="text" id="card_number" name="card_number"
                   placeholder="16-digit card number" maxlength="16">

            <label for="expiry_date">Expiry Date (MM/YY):</label>
            <input type="text" id="expiry_date" name="expiry_date"
                   placeholder="MM/YY" maxlength="5">

            <label for="cvv">CVV:</label>
            <input type="text" id="cvv" name="cvv"
                   placeholder="3-digit CVV" maxlength="3">
        </div>

        <!-- UPI -->
        <div id="upi_details" class="method-section">
            <label for="upi_id">UPI ID:</label>
            <input type="text" id="upi_id" name="upi_id"
                   placeholder="e.g. name@upi">
        </div>

        <!-- Net Banking -->
        <div id="netbanking_details" class="method-section">
            <label for="bank_name">Select Bank:</label>
            <select id="bank_name" name="bank_name">
                <option value="">-- Choose Your Bank --</option>
                <option>State Bank of India</option>
                <option>HDFC Bank</option>
                <option>ICICI Bank</option>
                <option>Axis Bank</option>
                <option>Kotak Mahindra Bank</option>
                <option>Punjab National Bank</option>
                <option>Bank of Baroda</option>
                <option>Other</option>
            </select>
        </div>

        <button type="submit" name="make_payment" class="submit-btn">Make Payment</button>
    </form>

    <?php endif; ?>

    <a href="event_categories.php" class="back-link">← Back to Events</a>
</div>

<script>
function showPaymentFields() {
    const method = document.getElementById('payment_method').value;
    document.getElementById('card_details').style.display        = '';
    document.getElementById('upi_details').style.display         = '';
    document.getElementById('netbanking_details').style.display  = '';

    if (method === 'Credit Card' || method === 'Debit Card') {
        document.getElementById('card_details').style.display = 'block';
    } else if (method === 'UPI') {
        document.getElementById('upi_details').style.display = 'block';
    } else if (method === 'Net Banking') {
        document.getElementById('netbanking_details').style.display = 'block';
    }
}
</script>

<?php mysqli_close($conn); ?>
</body>
</html>
