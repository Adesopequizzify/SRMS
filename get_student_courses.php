<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$matricNumber = isset($_GET['matricNumber']) ? $_GET['matricNumber'] : '';

if (empty($matricNumber)) {
    echo json_encode(['success' => false, 'message' => 'Matric number is required']);
    exit;
}

try {
    // Get student information
    $stmt = $pdo->prepare("SELECT id, first_name, last_name FROM students WHERE matric_number = ?");
    $stmt->execute([$matricNumber]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        echo json_encode(['success' => false, 'message' => 'Student not found']);
        exit;
    }

    // Get registered courses for the student
    $stmt = $pdo->prepare("
        SELECT c.id, c.course_name, c.course_code, c.grade_thresholds
        FROM courses c
        JOIN course_registrations cr ON c.id = cr.course_id
        WHERE cr.student_id = ?
    ");
    $stmt->execute([$student['id']]);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Decode grade thresholds JSON
    foreach ($courses as &$course) {
        $course['grade_thresholds'] = json_decode($course['grade_thresholds'], true);
    }

    echo json_encode([
        'success' => true,
        'student' => $student,
        'courses' => $courses
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}