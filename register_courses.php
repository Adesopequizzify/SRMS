<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

if (ob_get_length()) ob_clean();

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$studentId = isset($_POST['studentId']) ? intval($_POST['studentId']) : 0;
$academicYearId = isset($_POST['academicYearId']) ? intval($_POST['academicYearId']) : 0;
$sessionId = isset($_POST['sessionId']) ? intval($_POST['sessionId']) : 0;
$courses = isset($_POST['courses']) ? json_decode($_POST['courses'], true) : [];

if ($studentId <= 0 || $academicYearId <= 0 || $sessionId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid student ID, academic year, or session']);
    exit;
}

if (empty($courses)) {
    echo json_encode(['success' => false, 'message' => 'No courses selected']);
    exit;
}

try {
    if (!isset($pdo) || !($pdo instanceof PDO)) {
        throw new Exception('Database connection failed');
    }

    $pdo->beginTransaction();

    $stmt = $pdo->prepare("SELECT id FROM students WHERE id = ?");
    $stmt->execute([$studentId]);
    if (!$stmt->fetch()) {
        throw new Exception('Student not found');
    }

    // Delete existing registrations for the specific academic year and session
    $stmt = $pdo->prepare("DELETE FROM course_registrations WHERE student_id = ? AND academic_year_id = ? AND session_id = ?");
    $stmt->execute([$studentId, $academicYearId, $sessionId]);

    // Insert new registrations
    $stmt = $pdo->prepare("INSERT INTO course_registrations (student_id, course_id, academic_year_id, session_id) VALUES (?, ?, ?, ?)");
    foreach ($courses as $courseId) {
        $stmt->execute([$studentId, $courseId, $academicYearId, $sessionId]);
    }

    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Courses registered successfully']);
} catch (Exception $e) {
    if (isset($pdo)) {
        $pdo->rollBack();
    }
    error_log('Error in course registration: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error registering courses: ' . $e->getMessage()]);
}