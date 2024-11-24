<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$matricNumber = isset($_GET['matricNumber']) ? $_GET['matricNumber'] : '';
$academicYearId = isset($_GET['academic_year_id']) ? intval($_GET['academic_year_id']) : 0;
$sessionId = isset($_GET['session_id']) ? intval($_GET['session_id']) : 0;

if (empty($matricNumber) || $academicYearId <= 0 || $sessionId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

try {
    // Get student information
    $stmt = $pdo->prepare("SELECT id FROM students WHERE matric_number = ?");
    $stmt->execute([$matricNumber]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        echo json_encode(['success' => false, 'message' => 'Student not found']);
        exit;
    }

    // Get student courses
    $stmt = $pdo->prepare("
        SELECT c.id, c.course_code, c.course_name, c.grade_thresholds
        FROM courses c
        JOIN course_registrations cr ON c.id = cr.course_id
        WHERE cr.student_id = ? AND cr.academic_year_id = ? AND cr.session_id = ?
    ");
    $stmt->execute([$student['id'], $academicYearId, $sessionId]);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Decode grade thresholds JSON
    foreach ($courses as &$course) {
        $course['grade_thresholds'] = json_decode($course['grade_thresholds'], true);
    }

    echo json_encode([
        'success' => true,
        'courses' => $courses
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

