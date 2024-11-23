<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$department = isset($_GET['department']) ? $_GET['department'] : '';

try {
    $query = "SELECT id, course_code, course_name FROM courses";
    $params = [];

    if (!empty($department)) {
        $query .= " WHERE department = ?";
        $params[] = $department;
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'courses' => $courses]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

