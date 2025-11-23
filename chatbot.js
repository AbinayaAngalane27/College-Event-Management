function toggleChatbot() {
    const chatbot = document.getElementById('chatbot');
    if (chatbot.style.display === 'block') {
        chatbot.style.display = 'none';
    } else {
        chatbot.style.display = 'block';

        // Display initial message
        const chatWindow = document.getElementById('chat-window');
        chatWindow.innerHTML = '<div><strong>Bot:</strong> You asked about the events.</div>';
    }
}

function sendMessage() {
    const userInput = document.getElementById('user-input').value.trim();
    if (userInput) {
        const chatWindow = document.getElementById('chat-window');
        chatWindow.innerHTML += `<div><strong>You:</strong> ${userInput}</div>`;

        // Fetch events only after user input
        fetchEventDetails(userInput);

        document.getElementById('user-input').value = '';
    }
}

// Fetch event details using AJAX
function fetchEventDetails(query) {
    $.ajax({
        url: 'fetch_event_detail.php',
        method: 'GET',
        data: { query: query }, // Pass user query to the server
        success: function(response) {
            const chatWindow = document.getElementById('chat-window');
            chatWindow.innerHTML += `<div><strong>Bot:</strong> ${response}</div>`;
            chatWindow.scrollTop = chatWindow.scrollHeight; // Scroll to the bottom
        },
        error: function() {
            alert('Failed to load event details.');
        }
    });
}
