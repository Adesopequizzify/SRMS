<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$department = isset($_GET['department']) ? $_GET['department'] : '';
$academicYearId = isset($_GET['academic_year_id']) ? intval($_GET['academic_year_id']) : 0;
$sessionId = isset($_GET['session_id']) ? intval($_GET['session_id']) : 0;

if (empty($department) || $academicYearId <= 0 || $sessionId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT c.id, c.course_code, c.course_name 
        FROM courses c
        JOIN course_offerings co ON c.id = co.course_id
        WHERE c.department = ? AND co.academic_year_id = ? AND co.session_id = ?
        ORDER BY c.course_code
    ");
    $stmt->execute([$department, $academicYearId, $sessionId]);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($courses)) {
        echo json_encode(['success' => false, 'message' => 'No courses found for the selected criteria']);
    } else {
        echo json_encode(['success' => true, 'courses' => $courses]);
    }
} catch (PDOException $e) {
    error_log('Database error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error fetching courses: ' . $e->getMessage()]);
}