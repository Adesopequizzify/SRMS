<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$studentId = isset($_GET['student_id']) ? intval($_GET['student_id']) : 0;

if ($studentId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid student ID']);
    exit;
}

try {
    // Get student details
    $stmt = $pdo->prepare("SELECT s.*, o.gpa, o.final_remark 
                           FROM students s 
                           LEFT JOIN overall_results o ON s.id = o.student_id 
                           WHERE s.id = ?");
    $stmt->execute([$studentId]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        echo json_encode(['success' => false, 'message' => 'Student not found']);
        exit;
    }

    // Get course results
    $stmt = $pdo->prepare("SELECT c.course_code, c.course_name, r.score, r.grade 
                           FROM results r 
                           JOIN courses c ON r.course_id = c.id 
                           WHERE r.student_id = ?");
    $stmt->execute([$studentId]);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $student['courses'] = $courses;

    echo json_encode(['success' => true, 'details' => $student]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

