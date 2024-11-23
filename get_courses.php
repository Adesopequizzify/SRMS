<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

try {
    $query = "
        SELECT c.id, c.course_code, c.course_name, c.department, ay.year as academic_year, s.name as session
        FROM courses c
        LEFT JOIN course_offerings co ON c.id = co.course_id
        LEFT JOIN academic_years ay ON co.academic_year_id = ay.id
        LEFT JOIN sessions s ON co.session_id = s.id
        ORDER BY c.department, c.course_code
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $groupedCourses = [];
    foreach ($courses as $course) {
        $department = $course['department'];
        if (!isset($groupedCourses[$department])) {
            $groupedCourses[$department] = [];
        }
        $groupedCourses[$department][] = $course;
    }

    echo json_encode(['success' => true, 'courses' => $groupedCourses]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}