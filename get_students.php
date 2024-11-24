<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$studentId = isset($_GET['student_id']) ? intval($_GET['student_id']) : 0;
$academicYearId = isset($_GET['academic_year_id']) ? intval($_GET['academic_year_id']) : null;
$sessionId = isset($_GET['session_id']) ? intval($_GET['session_id']) : null;

if ($studentId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid student ID']);
    exit;
}

try {
    // Get student details
    $stmt = $pdo->prepare("
        SELECT s.*, o.gpa, o.final_remark,
               COALESCE(ay.year, 'N/A') as academic_year,
               COALESCE(sess.name, 'N/A') as session
        FROM students s 
        LEFT JOIN overall_results o ON s.id = o.student_id 
        LEFT JOIN academic_years ay ON o.academic_year_id = ay.id
        LEFT JOIN sessions sess ON o.session_id = sess.id
        WHERE s.id = ?
    ");
    $stmt->execute([$studentId]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        echo json_encode(['success' => false, 'message' => 'Student not found']);
        exit;
    }

    // Get course results
    $courseQuery = "SELECT c.course_code, c.course_name, r.score, r.grade 
                    FROM results r 
                    JOIN courses c ON r.course_id = c.id 
                    WHERE r.student_id = ?";
    $params = [$studentId];

    if ($academicYearId) {
        $courseQuery .= " AND r.academic_year_id = ?";
        $params[] = $academicYearId;
    }

    if ($sessionId) {
        $courseQuery .= " AND r.session_id = ?";
        $params[] = $sessionId;
    }

    $stmt = $pdo->prepare($courseQuery);
    $stmt->execute($params);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $student['courses'] = $courses;

    echo json_encode(['success' => true, 'details' => $student]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}