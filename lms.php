<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ulra";

// Function to get courses
function getCourses($conn) {
    try {
        $stmt = $conn->query("SHOW COLUMNS FROM course WHERE Field != 'id'");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch(PDOException $e) {
        return [];
    }
}

// Function to get sections for a specific course
function getSections($conn, $course) {
    try {
        $stmt = $conn->prepare("SELECT $course FROM course WHERE $course IS NOT NULL");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch(PDOException $e) {
        return [];
    }
}

// Handle AJAX requests
if(isset($_GET['action'])) {
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if($_GET['action'] == 'getCourses') {
            $courses = getCourses($conn);
            echo json_encode($courses);
            exit;
        } 
        else if($_GET['action'] == 'getSections' && isset($_GET['course'])) {
            $sections = getSections($conn, $_GET['course']);
            echo json_encode($sections);
            exit;
        }
    } catch(PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University Learning Management System</title>
    <style>
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            font-family: Arial, sans-serif;
        }
        .section-selector {
            margin: 15px 0;
        }
        select {
            padding: 8px;
            width: 200px;
            margin: 5px 0;
        }
        .message-display {
            border: 1px solid #ddd;
            min-height: 200px;
            margin: 15px 0;
            padding: 10px;
            background: #f9f9f9;
            border-radius: 4px;
        }
        .message-input-container {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-top: 15px;
        }
        input[type="text"] {
            flex: 1;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            padding: 8px 15px;
            background: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }
        button:hover {
            background: #0056b3;
        }
        .emoji-picker-button {
            padding: 5px 10px;
            font-size: 1.2em;
            background: none;
            border: 1px solid #ddd;
        }
        .emoji-picker {
            display: none;
            position: absolute;
            bottom: 100%;
            left: 0;
            background: white;
            border: 1px solid #ddd;
            padding: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 4px;
        }
        .message {
            padding: 8px;
            margin: 5px 0;
            background: white;
            border-radius: 4px;
            border-left: 3px solid #007bff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Message Box</h1>
        
        <div class="section-selector">
            <label for="courses">Select Course:</label>
            <select id="courses" onchange="loadSections()">
                <option value="">Select a course</option>
            </select>
        </div>
        
        <div class="section-selector">
            <label for="sections">Select Section:</label>
            <select id="sections">
                <option value="">Select a section</option>
            </select>
        </div>
        
        <label for="messageInput">Message:</label>
        <div class="message-display" id="messageDisplay"></div>
        
        <div class="message-input-container">
            <button class="emoji-picker-button" onclick="toggleEmojiPicker()">😊</button>
            <div class="emoji-picker" id="emojiPicker">
                <!-- Emoji picker will be populated by JavaScript -->
            </div>
            <input type="text" id="messageInput" placeholder="Type your message here..." oninput="suggestMessage()">
            <button onclick="postMessage()">Send</button>
        </div>
    </div>

    <script>
        // Load courses when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadCourses();
        });

        function loadCourses() {
            fetch('?action=getCourses')
                .then(response => response.json())
                .then(data => {
                    const courseSelect = document.getElementById('courses');
                    courseSelect.innerHTML = '<option value="">Select a course</option>';
                    
                    data.forEach(course => {
                        const option = document.createElement('option');
                        option.value = course;
                        option.textContent = course;
                        courseSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error loading courses:', error));
        }

        function loadSections() {
            const courseSelect = document.getElementById('courses');
            const selectedCourse = courseSelect.value;
            
            if (!selectedCourse) {
                return;
            }
            
            fetch(`?action=getSections&course=${encodeURIComponent(selectedCourse)}`)
                .then(response => response.json())
                .then(data => {
                    const sectionSelect = document.getElementById('sections');
                    sectionSelect.innerHTML = '<option value="">Select a section</option>';
                    
                    data.forEach(section => {
                        const option = document.createElement('option');
                        option.value = section;
                        option.textContent = section;
                        sectionSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error loading sections:', error));
        }

        function toggleEmojiPicker() {
            const emojiPicker = document.getElementById('emojiPicker');
            emojiPicker.style.display = emojiPicker.style.display === 'none' ? 'block' : 'none';
        }

        function suggestMessage() {
            // Implement message suggestion logic if needed
        }

        function postMessage() {
            const messageInput = document.getElementById('messageInput');
            const messageDisplay = document.getElementById('messageDisplay');
            const courseSelect = document.getElementById('courses');
            const sectionSelect = document.getElementById('sections');
            
            if (!messageInput.value.trim()) {
                return;
            }

            if (!courseSelect.value || !sectionSelect.value) {
                alert('Please select both course and section before sending a message.');
                return;
            }
            
            const messageDiv = document.createElement('div');
            messageDiv.className = 'message';
            messageDiv.textContent = `[${courseSelect.value} - ${sectionSelect.value}] ${messageInput.value}`;
            messageDisplay.appendChild(messageDiv);
            messageInput.value = '';
        }
    </script>
</body>
</html>
