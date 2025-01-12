<?php
// get_teacher_data.php
session_start();
require_once 'db_connection.php';

$teacher_id = $_SESSION['id'];
$query = "SELECT name, email FROM teacher WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
$teacher_data = $result->fetch_assoc();
echo json_encode($teacher_data);

// get_dashboard_stats.php
session_start();
require_once 'db_connection.php';

$teacher_id = $_SESSION['id'];

// Get total students
$query = "SELECT COUNT(*) as total FROM student";
$result = $conn->query($query);
$total_students = $result->fetch_assoc()['total'];

// Get unread messages
$query = "SELECT COUNT(*) as unread FROM messages WHERE receiver_id = ? AND receiver_type = 'teacher' AND read_status = 0";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$unread_messages = $stmt->get_result()->fetch_assoc()['unread'];

// Get total sections
$query = "SELECT COUNT(DISTINCT section) as sections FROM student";
$result = $conn->query($query);
$total_sections = $result->fetch_assoc()['sections'];

$stats = [
    'totalStudents' => $total_students,
    'unreadMessages' => $unread_messages,
    'totalSections' => $total_sections
];

echo json_encode($stats);

// get_chat_messages.php
session_start();
require_once 'db_connection.php';

$teacher_id = $_SESSION['id'];
$query = "SELECT m.*, s.name as student_name 
          FROM messages m 
          LEFT JOIN student s ON m.sender_id = s.id AND m.sender_type = 'student'
          WHERE (receiver_id = ? AND receiver_type = 'teacher')
          OR (sender_id = ? AND sender_type = 'teacher')
          ORDER BY m.timestamp ASC";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $teacher_id, $teacher_id);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

echo json_encode($messages);

// send_message.php
session_start();
require_once 'db_connection.php';

$data = json_decode(file_get_contents('php://input'), true);
$teacher_id = $_SESSION['teacher_id'];
$message = $data['message'];
$timestamp = date('Y-m-d H:i:s');

$query = "INSERT INTO messages (sender_id, sender_type, receiver_id, receiver_type, message, timestamp)
          VALUES (?, 'teacher', ?, 'student', ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("iiss", $teacher_id, $student_id, $message, $timestamp);

$response = ['success' => $stmt->execute()];
echo json_encode($response);

// db_connection.php
$host = 'localhost';
$username = 'your_username';
$password = 'your_password';
$database = 'ulra';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        .container {
            display: grid;
            grid-template-columns: 250px 1fr;
            min-height: 100vh;
        }

        .sidebar {
            background: #2c3e50;
            color: white;
            padding: 20px;
        }

        .sidebar .profile {
            text-align: center;
            padding: 20px 0;
            border-bottom: 1px solid #34495e;
        }

        .sidebar .nav-links {
            margin-top: 20px;
        }

        .sidebar .nav-links a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
        }

        .sidebar .nav-links a:hover {
            background: #34495e;
        }

        .main-content {
            padding: 20px;
            background: #f5f6fa;
        }

        .header {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .chat-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            height: 500px;
            display: grid;
            grid-template-rows: auto 1fr auto;
        }

        .chat-header {
            padding: 15px;
            background: #2c3e50;
            color: white;
            border-radius: 10px 10px 0 0;
        }

        .chat-messages {
            padding: 15px;
            overflow-y: auto;
        }

        .message {
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
        }

        .received {
            background: #f1f1f1;
            margin-right: 50px;
        }

        .sent {
            background: #3498db;
            color: white;
            margin-left: 50px;
        }

        .chat-input {
            padding: 15px;
            border-top: 1px solid #eee;
            display: flex;
            gap: 10px;
        }

        .chat-input input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .chat-input button {
            padding: 10px 20px;
            background: #2c3e50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .chat-input button:hover {
            background: #34495e;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <div class="profile">
                <h2>Welcome, <span id="teacherName">Teacher</span></h2>
                <p id="teacherEmail">teacher@email.com</p>
            </div>
            <div class="nav-links">
                <a href="#dashboard">Dashboard</a>
                <a href="#students">Students</a>
                <a href="#messages">Messages</a>
                <a href="#settings">Settings</a>
                <a href="#logout" onclick="logout()">Logout</a>
            </div>
        </div>
        <div class="main-content">
            <div class="header">
                <h1>Teacher Dashboard</h1>
            </div>
            <div class="dashboard-cards">
                <div class="card">
                    <h3>Total Students</h3>
                    <p id="totalStudents">Loading...</p>
                </div>
                <div class="card">
                    <h3>Messages</h3>
                    <p id="unreadMessages">Loading...</p>
                </div>
                <div class="card">
                    <h3>Sections</h3>
                    <p id="totalSections">Loading...</p>
                </div>
            </div>
            <div class="chat-container">
                <div class="chat-header">
                    <h3>Chat Messages</h3>
                </div>
                <div class="chat-messages" id="chatMessages">
                    <!-- Messages will be loaded here -->
                </div>
                <div class="chat-input">
                    <input type="text" id="messageInput" placeholder="Type your message...">
                    <button onclick="sendMessage()">Send</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Load teacher data on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadTeacherData();
            loadDashboardStats();
            loadChatMessages();
        });

        function loadTeacherData() {
            fetch('get_teacher_data.php')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('teacherName').textContent = data.name;
                    document.getElementById('teacherEmail').textContent = data.email;
                });
        }

        function loadDashboardStats() {
            fetch('get_dashboard_stats.php')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('totalStudents').textContent = data.totalStudents;
                    document.getElementById('unreadMessages').textContent = data.unreadMessages;
                    document.getElementById('totalSections').textContent = data.totalSections;
                });
        }

        function loadChatMessages() {
            fetch('get_chat_messages.php')
                .then(response => response.json())
                .then(messages => {
                    const chatContainer = document.getElementById('chatMessages');
                    chatContainer.innerHTML = '';
                    
                    messages.forEach(message => {
                        const messageDiv = document.createElement('div');
                        messageDiv.className = `message ${message.sender_type === 'teacher' ? 'sent' : 'received'}`;
                        messageDiv.textContent = message.message;
                        chatContainer.appendChild(messageDiv);
                    });
                    
                    chatContainer.scrollTop = chatContainer.scrollHeight;
                });
        }

        function sendMessage() {
            const input = document.getElementById('messageInput');
            const message = input.value.trim();
            
            if (message) {
                fetch('send_message.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ message: message })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        input.value = '';
                        loadChatMessages();
                    }
                });
            }
        }

        function logout() {
            fetch('logout.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = 'login.php';
                    }
                });
        }
    </script>
</body>
</html>