<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

try {
    $stmt = $pdo->query("SELECT id, name FROM sessions ORDER BY name");
    $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'sessions' => $sessions]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error fetching sessions: ' . $e->getMessage()]);
}