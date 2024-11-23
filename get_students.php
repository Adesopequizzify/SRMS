<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

$academicYearId = isset($_GET['academic_year_id']) ? intval($_GET['academic_year_id']) : null;

try {
    $query = "SELECT DISTINCT s.* FROM students s
              LEFT JOIN course_registrations cr ON s.id = cr.student_id";
    
    $params = [];
    
    if ($academicYearId) {
        $query .= " WHERE cr.academic_year_id = ?";
        $params[] = $academicYearId;
    }
    
    $query .= " ORDER BY s.created_at DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'students' => $students]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error fetching students: ' . $e->getMessage()]);
}
?>