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

$matricNumber = isset($_GET['matricNumber']) ? $_GET['matricNumber'] : '';

if (empty($matricNumber)) {
    echo json_encode(['success' => false, 'message' => 'Matric number is required']);
    exit;
}

try {
    // Use $pdo instead of $db
    if (!isset($pdo) || !($pdo instanceof PDO)) {
        throw new Exception('Database connection failed');
    }

    $stmt = $pdo->prepare("SELECT id, first_name, last_name, gender, department FROM students WHERE matric_number = ?");
    $stmt->execute([$matricNumber]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($student) {
        echo json_encode(['success' => true, 'student' => $student]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Student not found']);
    }
} catch (Exception $e) {
    error_log('Database error in get_student_info.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while fetching student information']);
}