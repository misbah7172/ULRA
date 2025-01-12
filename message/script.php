<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ulra";

// Create connection
$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Function to get courses taken by a specific user
function getUserCourses($conn, $userId) {
    try {
        $stmt = $conn->prepare("SELECT * FROM course WHERE id = ?");
        $stmt->execute([$userId]);

        $courses = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            foreach ($row as $course => $section) {
                if ($course != 'id' && $section != null) {
                    $courses[] = ['course' => $course, 'section' => $section];
                }
            }
        }
        return $courses;
    } catch (PDOException $e) {
        return [];
    }
}

// Function to get messages from a specific course and section
function getCourseMessages($conn, $course, $section) {
    try {
        $stmt = $conn->prepare("SELECT message FROM $course WHERE section = ? ORDER BY timestamp DESC");
        $stmt->execute([$section]);

        $messages = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $messages[] = $row['message'];
        }
        return $messages;
    } catch (PDOException $e) {
        return [];
    }
}

// Handle AJAX requests
if (isset($_GET['action'])) {
    try {
        if ($_GET['action'] == 'getUserCourses' && isset($_GET['user_id'])) {
            // Get the courses taken by the user
            $userId = intval($_GET['user_id']);
            $courses = getUserCourses($conn, $userId);
            echo json_encode($courses);
            exit;
        } 
        
        else if ($_GET['action'] == 'getCourseMessages' && isset($_GET['course']) && isset($_GET['section'])) {
            // Get the messages for the selected course and section
            $course = $_GET['course'];
            $section = $_GET['section'];
            $messages = getCourseMessages($conn, $course, $section);
            echo json_encode($messages);
            exit;
        }
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    }
}
?>
