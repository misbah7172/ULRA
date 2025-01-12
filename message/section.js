function loadMessages() {
    const section = document.getElementById('sections').value;
    const messageDisplay = document.getElementById('messageDisplay');
    messageDisplay.innerHTML = '';

    messages[section].forEach(msg => {
        const messageElement = document.createElement('div');
        messageElement.classList.add('message');
        messageElement.innerHTML = `<strong>${msg.sender}</strong>: ${msg.text} <span class="timestamp">${msg.timestamp}</span>`;
        messageDisplay.appendChild(messageElement);
    });
}

function postMessage() {
    const section = document.getElementById('sections').value;
    const messageInput = document.getElementById('messageInput');
    const messageText = messageInput.value.trim();

    if (messageText) {
        const newMessage = {
            sender: 'User', // Replace with actual user name
            text: messageText,
            timestamp: new Date().toLocaleString()
        };

        messages[section].push(newMessage);
        messageInput.value = ''; // Clear input
        loadMessages(); // Refresh message display
    }
}

function suggestMessage() {
    // Placeholder for auto-suggestions
    // This could be enhanced with an actual suggestion algorithm
}

let messages = {
    math: [],
    physics: []
};

// Emoji categories and their emojis
const emojiCategories = {
    'Smileys': ['😀', '😃', '😄', '😁', '😅', '😂', '🤣', '😊', '😇', '🙂', '😉', '😌', '😍', '🥰', '😘'],
    'Gestures': ['👍', '👎', '👋', '🤝', '✌️', '🤞', '🤟', '🤘', '👌', '🤌'],
    'Education': ['📚', '📖', '✏️', '📝', '📓', '🎓', '🔬', '🔭', '📐', '📏'],
    'Objects': ['💻', '📱', '⌚️', '📷', '🎮', '🎨', '🎭', '🎪', '🎫', '🎟️'],
    'Symbols': ['❤️', '💔', '💫', '💥', '💢', '💦', '💨', '🕉️', '☮️', '✝️']
};

// Initialize emoji picker
function initializeEmojiPicker() {
    const emojiPicker = document.getElementById('emojiPicker');
    
    for (const [category, emojis] of Object.entries(emojiCategories)) {
        const categoryDiv = document.createElement('div');
        categoryDiv.className = 'emoji-category';
        
        const categoryTitle = document.createElement('div');
        categoryTitle.className = 'emoji-category-title';
        categoryTitle.textContent = category;
        
        const emojiGrid = document.createElement('div');
        emojiGrid.className = 'emoji-grid';
        
        emojis.forEach(emoji => {
            const emojiSpan = document.createElement('span');
            emojiSpan.className = 'emoji';
            emojiSpan.textContent = emoji;
            emojiSpan.onclick = () => insertEmoji(emoji);
            emojiGrid.appendChild(emojiSpan);
        });
        
        categoryDiv.appendChild(categoryTitle);
        categoryDiv.appendChild(emojiGrid);
        emojiPicker.appendChild(categoryDiv);
    }
}

// Toggle emoji picker
function toggleEmojiPicker() {
    const emojiPicker = document.getElementById('emojiPicker');
    emojiPicker.classList.toggle('active');
}

// Insert emoji into message input
function insertEmoji(emoji) {
    const messageInput = document.getElementById('messageInput');
    const cursorPos = messageInput.selectionStart;
    const textBefore = messageInput.value.substring(0, cursorPos);
    const textAfter = messageInput.value.substring(cursorPos);
    
    messageInput.value = textBefore + emoji + textAfter;
    messageInput.focus();
    const newCursorPos = cursorPos + emoji.length;
    messageInput.setSelectionRange(newCursorPos, newCursorPos);
}

// Close emoji picker when clicking outside
document.addEventListener('click', (event) => {
    const emojiPicker = document.getElementById('emojiPicker');
    const emojiButton = document.querySelector('.emoji-picker-button');
    
    if (!emojiPicker.contains(event.target) && !emojiButton.contains(event.target)) {
        emojiPicker.classList.remove('active');
    }
});

// Existing functions
function loadMessages() {
    const section = document.getElementById('sections').value;
    const messageDisplay = document.getElementById('messageDisplay');
    messageDisplay.innerHTML = '';
    messages[section].forEach(msg => {
        const messageElement = document.createElement('div');
        messageElement.classList.add('message');
        messageElement.innerHTML = `<strong>${msg.sender}</strong>: ${msg.text} <span class="timestamp">${msg.timestamp}</span>`;
        messageDisplay.appendChild(messageElement);
    });
}

function postMessage() {
    const section = document.getElementById('sections').value;
    const messageInput = document.getElementById('messageInput');
    const messageText = messageInput.value.trim();
    if (messageText) {
        const newMessage = {
            sender: 'User',
            text: messageText,
            timestamp: new Date().toLocaleString()
        };
        messages[section].push(newMessage);
        messageInput.value = '';
        loadMessages();
    }
}

function suggestMessage() {
    // Placeholder for auto-suggestions
}

// Initialize emoji picker when page loads
window.onload = initializeEmojiPicker;