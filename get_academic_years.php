<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

try {
    $stmt = $pdo->query("SELECT id, year FROM academic_years ORDER BY year DESC");
    $academic_years = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($academic_years)) {
        echo json_encode(['success' => false, 'message' => 'No academic years found']);
    } else {
        echo json_encode(['success' => true, 'academic_years' => $academic_years]);
    }
} catch (PDOException $e) {
    error_log('Database error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error fetching academic years: ' . $e->getMessage()]);
}