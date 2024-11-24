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
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

try {
    // Get student information
    $stmt = $pdo->prepare("SELECT id, first_name, last_name, department FROM students WHERE matric_number = ?");
    $stmt->execute([$matricNumber]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        echo json_encode(['success' => false, 'message' => 'Student not found']);
        exit;
    }

    // Get available academic years for the student
    $stmt = $pdo->prepare("
        SELECT DISTINCT ay.id, ay.year
        FROM academic_years ay
        JOIN course_registrations cr ON ay.id = cr.academic_year_id
        WHERE cr.student_id = ?
        ORDER BY ay.year DESC
    ");
    $stmt->execute([$student['id']]);
    $academicYears = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get available sessions for the student
    $stmt = $pdo->prepare("
        SELECT DISTINCT s.id, s.name
        FROM sessions s
        JOIN course_registrations cr ON s.id = cr.session_id
        WHERE cr.student_id = ?
        ORDER BY s.name
    ");
    $stmt->execute([$student['id']]);
    $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'student' => $student,
        'academic_years' => $academicYears,
        'sessions' => $sessions
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

