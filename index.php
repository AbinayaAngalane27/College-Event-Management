<?php
// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'college_event_management';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rejouir 2K25 - Event Categories</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="chatbot.js"></script> <!-- Include chatbot JS -->
    <style>
        /* Chatbot Icon Style */
        #chatbot-icon {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            background-color: #007BFF;
            color: white;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            font-size: 24px;
            z-index: 100;
        }

        #chatbot {
            display: none;
            position: fixed;
            bottom: 80px;
            right: 20px;
            width: 300px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            z-index: 101;
        }

        .chatbot-header {
            background-color: #007BFF;
            color: white;
            padding: 10px;
            text-align: center;
        }

        .chatbot-header button {
            background: none;
            border: none;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        #chat-window {
            padding: 10px;
            height: 200px;
            overflow-y: auto;
            background-color: #fff;
        }

        #user-input {
            width: 100%;
            padding: 10px;
            border: none;
            border-top: 1px solid #ccc;
            font-size: 16px;
        }

        /* Chatbot open/close button hover effect */
        #chatbot-icon:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <!-- Chatbot Icon -->
    <div id="chatbot-icon" onclick="toggleChatbot()">
        <span>&#128172;</span> <!-- This is a speech bubble emoji -->
    </div>

    <!-- Chatbot Window -->
    <div id="chatbot" class="chatbot-container">
        <div class="chatbot-header">
            <span>Event Bot</span>
            <button onclick="closeChatbot()">X</button>
        </div>
        <div id="chat-window" class="chat-window"></div>
        <input type="text" id="user-input" placeholder="Ask about events..." onkeydown="if(event.key === 'Enter'){ sendMessage(); }">
    </div>

    <!-- Main Content -->
    <div class="overlay">
        <div class="header-text">
            <h1>Rejouir 2K25</h1>
        </div>
        <div class="welcome-text">
            <h2>Welcome to Rejouir</h2>
            <p>Explore and participate in the most exciting events at our college! Join us in celebrating creativity, talent, and knowledge.</p>
        </div>
    </div>

    <nav>
        <a href="index.php">Home</a>
        <a href="event_categories.php">Events</a>
        <a href="login.php">Login</a>
        <a href="contact.php">Contact</a>
    </nav>

    <footer class="footer-animated">
        <p>&copy; 2K25 Rejouir. All rights reserved.</p>
    </footer>

    <script>
        // Fetch event details using AJAX
function fetchEventDetails(query = '') {
    $.ajax({
        url: 'fetch_event_details.php', // Corrected filename
        method: 'GET',
        data: { query: query },
        success: function(response) {
            const chatWindow = document.getElementById('chat-window');
            const eventDetails = document.createElement('div');
            eventDetails.innerHTML = '<strong>You can ask about Events:</strong><br>' + response;
            chatWindow.appendChild(eventDetails);
        },
        error: function() {
            alert('Failed to load event details.');
        }
    });
}

// Modify toggleChatbot to fetch event details
function toggleChatbot() {
    const chatbot = document.getElementById('chatbot');
    if (chatbot.style.display === 'block') {
        chatbot.style.display = 'none';
    } else {
        chatbot.style.display = 'block';
        fetchEventDetails(); // Fetch and display event details when opening
    }
}

// Send message from user input
function sendMessage() {
    const userInput = document.getElementById('user-input').value.trim();
    if (userInput) {
        const chatWindow = document.getElementById('chat-window');
        const newMessage = document.createElement('div');
        newMessage.textContent = 'You: ' + userInput;
        chatWindow.appendChild(newMessage);
        
        document.getElementById('user-input').value = '';

        // Fetch event details based on user query
        fetchEventDetails(userInput);
    }
}

    </script>
</body>
</html>

<?php
$conn->close();
?>