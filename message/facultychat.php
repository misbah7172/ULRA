<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Chat Interface</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <link rel="stylesheet" href="facultychat.css">
</head>
<body>
    <div class="container">
        <!-- Left Panel -->
        <div class="left-panel">
            <div class="search-container">
                <input type="text" class="search-bar" placeholder="Search faculty...">
                <select class="course-filter">
                    <option value="">All Courses</option>
                    <option value="CS101">CS101 - Intro to Programming</option>
                    <option value="CS201">CS201 - Data Structures</option>
                    <option value="CS301">CS301 - Algorithms</option>
                </select>
            </div>
            <div class="faculty-list">
                <!-- Faculty items will be dynamically added here -->
            </div>
        </div>

        <!-- Right Panel -->
        <div class="right-panel">
            <div class="chat-header">
                <img src="/api/placeholder/48/48" alt="Faculty Avatar" class="chat-header-avatar">
                <div class="chat-header-info">
                    <h2>Dr. Sarah Johnson</h2>
                    <p>CS101 - Introduction to Programming</p>
                </div>
            </div>
            <div class="chat-messages">
                <!-- Messages will be dynamically added here -->
            </div>
            <div class="chat-input">
                <textarea class="message-box" placeholder="Type a message..."></textarea>
                <div class="chat-actions">
                    <button class="action-button">
                        <i class="fas fa-paperclip"></i>
                    </button>
                    <button class="action-button">
                        <i class="fas fa-smile"></i>
                    </button>
                    <button class="action-button send-button">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Sample faculty data
        const facultyData = [
            { id: 1, name: 'Dr. Sarah Johnson', courseId: 'CS101', course: 'Introduction to Programming' },
            { id: 2, name: 'Prof. Michael Chen', courseId: 'CS201', course: 'Data Structures' },
            { id: 3, name: 'Dr. Emily Brown', courseId: 'CS301', course: 'Algorithms' },
            { id: 4, name: 'Prof. David Wilson', courseId: 'CS101', course: 'Introduction to Programming' }
        ];

        // Sample messages data
        const messagesData = [
            { id: 1, text: 'Hello! I have a question about the latest assignment.', sent: true, timestamp: '10:30 AM' },
            { id: 2, text: 'Of course! What would you like to know?', sent: false, timestamp: '10:31 AM' },
            { id: 3, text: 'I\'m having trouble with the loop implementation in Question 3.', sent: true, timestamp: '10:32 AM' },
            { id: 4, text: 'Let me help you with that. Can you share your current code?', sent: false, timestamp: '10:33 AM' }
        ];

        // Function to render faculty list
        function renderFacultyList() {
            const facultyList = document.querySelector('.faculty-list');
            facultyList.innerHTML = facultyData.map(faculty => `
                <div class="faculty-item" data-id="${faculty.id}">
                    <img src="/api/placeholder/40/40" alt="${faculty.name}" class="faculty-avatar">
                    <div class="faculty-info">
                        <div class="faculty-name">${faculty.name}</div>
                        <div class="course-id">${faculty.courseId} - ${faculty.course}</div>
                    </div>
                </div>
            `).join('');
        }

        // Function to render messages
        function renderMessages() {
            const messagesContainer = document.querySelector('.chat-messages');
            messagesContainer.innerHTML = messagesData.map(message => `
                <div class="message ${message.sent ? 'sent' : 'received'}">
                    ${message.text}
                    <div class="message-time">${message.timestamp}</div>
                </div>
            `).join('');
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        // Function to add new message
        function addMessage(text, sent = true) {
            const newMessage = {
                id: messagesData.length + 1,
                text,
                sent,
                timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
            };
            messagesData.push(newMessage);
            renderMessages();
        }

        // Event Listeners
        document.addEventListener('DOMContentLoaded', () => {
            renderFacultyList();
            renderMessages();

            // Search functionality
            const searchBar = document.querySelector('.search-bar');
            const courseFilter = document.querySelector('.course-filter');

            searchBar.addEventListener('input', (e) => {
                const searchTerm = e.target.value.toLowerCase();
                const courseId = courseFilter.value;
                
                document.querySelectorAll('.faculty-item').forEach(item => {
                    const faculty = facultyData.find(f => f.id === parseInt(item.dataset.id));
                    const nameMatch = faculty.name.toLowerCase().includes(searchTerm);
                    const courseMatch = !courseId || faculty.courseId === courseId;
                    
                    item.style.display = nameMatch && courseMatch ? 'flex' : 'none';
                });
            });

            courseFilter.addEventListener('change', () => {
                searchBar.dispatchEvent(new Event('input'));
            });

            // Send message functionality
            const messageBox = document.querySelector('.message-box');
            const sendButton = document.querySelector('.send-button');

            function sendMessage() {
                const text = messageBox.value.trim();
                if (text) {
                    addMessage(text);
                    messageBox.value = '';
                }
            }

            sendButton.addEventListener('click', sendMessage);
            messageBox.addEventListener('keypress', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendMessage();
                }
            });

            // Faculty selection
            document.querySelectorAll('.faculty-item').forEach(item => {
                item.addEventListener('click', () => {
                    document.querySelectorAll('.faculty-item').forEach(i => i.classList.remove('active'));
                    item.classList.add('active');
                });
            });
        });

        // Mobile menu toggle
        if (window.innerWidth <= 768) {
            const container = document.querySelector('.container');
            const mobileButton = document.createElement('button');
            mobileButton.className = 'mobile-menu-button';
            mobileButton.innerHTML = '<i class="fas fa-bars"></i>';
            container.prepend(mobileButton);

            mobileButton.addEventListener('click', () => {
                const leftPanel = document.querySelector('.left-panel');
                leftPanel.classList.toggle('active');
            });
        }
    </script>
</body>
</html>