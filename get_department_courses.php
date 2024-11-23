<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

// Ensure no output before this point
if (ob_get_length()) ob_clean();

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$department = isset($_GET['department']) ? $_GET['department'] : '';

if (empty($department)) {
    echo json_encode(['success' => false, 'message' => 'Department is required']);
    exit;
}

try {
    // Use $pdo instead of $db to match your db.php configuration
    if (!isset($pdo) || !($pdo instanceof PDO)) {
        throw new Exception('Database connection failed');
    }

    $stmt = $pdo->prepare("SELECT id, course_name, course_code FROM courses WHERE department = ?");
    $stmt->execute([$department]);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($courses) {
        echo json_encode(['success' => true, 'courses' => $courses]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No courses found for this department']);
    }
} catch (Exception $e) {
    error_log('Database error in get_department_courses.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while fetching courses']);
}