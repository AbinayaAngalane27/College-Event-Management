<?php
session_start(); // Start session for managing user data if needed
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rejouir - contact Us</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav>
        <a href="index.php">Home</a>
        <a href="event_categories.php">Events</a>
        <a href="login.php">Login</a>
        <a href="contact.php">Contact</a>
    </nav>

    <div class="container">
        <h2>Get in Touch</h2>
        <p>We'd love to hear from you! For any inquiries, please feel free to reach out to us at the emails below:</p>
        <ul class="contact-emails">
            <li>info@rejouircollegeevents.com</li>
            <li>support@rejouircollegeevents.com</li>
            <li>events@rejouircollegeevents.com</li>
        </ul>
        <p>Follow us on our social media channels for the latest updates:</p>
        <ul class="social-media">
            <li><a href="#">Facebook</a></li>
            <li><a href="#">Twitter</a></li>
            <li><a href="#">Instagram</a></li>
        </ul>
    </div>
<style>
body {
    font-family: 'Arial', sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
}

header {
    background-color: #4cafa7;
    color: white;
    padding: 20px;
    text-align: center;
}
.container {
    max-width: 800px;
    margin: 40px auto;
    padding: 20px;
    background-color: white;
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    text-align: center; /* Center align text in the container */
}

h2 {
    margin-bottom: 20px;
    color: #333;
}

.social-media {
    list-style-type: none;
    padding: 0;
    margin: 20px 0;
}

.contact-emails li, .social-media li {
    margin: 10px 0;
    font-size: 18px;
}

.social-media li {
    display: inline;
    margin: 0 15px;
}

.social-media a {
    text-decoration: none;
    color: #4cafa7;
}
</style>


    <footer class="footer-animated">
        <p>&copy; 2K25 Rejouir. All rights reserved.</p>
    </footer>

</body>
</html>