<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

if (!isset($_SESSION['pending_results'])) {
    echo json_encode(['success' => false, 'message' => 'No pending results to confirm']);
    exit;
}

$results = $_SESSION['pending_results'];

try {
    $pdo->beginTransaction();

    // Get student ID
    $stmt = $pdo->prepare("SELECT id FROM students WHERE matric_number = ?");
    $stmt->execute([$results['matric_number']]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        throw new Exception('Student not found');
    }

    // Insert results
    $stmt = $pdo->prepare("INSERT INTO results (student_id, course_id, score, grade) VALUES (?, ?, ?, ?)");
    foreach ($results['courses'] as $course) {
        if (!isset($course['course_id']) || $course['course_id'] === null) {
            throw new Exception('Invalid course ID');
        }
        $stmt->execute([
            $student['id'],
            $course['course_id'],
            $course['score'],
            $course['grade']
        ]);
    }

    // Insert overall result
    $stmt = $pdo->prepare("INSERT INTO overall_results (student_id, gpa, final_remark) VALUES (?, ?, ?)");
    $stmt->execute([
        $student['id'],
        $results['gpa'],
        $results['final_remark']
    ]);

    $pdo->commit();
    unset($_SESSION['pending_results']);

    echo json_encode(['success' => true, 'message' => 'Results submitted successfully']);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Error submitting results: ' . $e->getMessage()]);
}

