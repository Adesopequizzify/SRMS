<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

try {
    // Simplified query to get all courses
    $query = "SELECT * FROM courses ORDER BY department, course_code";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Group courses by department
    $groupedCourses = [];
    foreach ($courses as $course) {
        $department = $course['department'];
        if (!isset($groupedCourses[$department])) {
            $groupedCourses[$department] = [];
        }
        $groupedCourses[$department][] = [
            'id' => $course['id'],
            'course_code' => $course['course_code'],
            'course_name' => $course['course_name']
        ];
    }

    echo json_encode([
        'success' => true, 
        'courses' => $groupedCourses
    ]);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}

