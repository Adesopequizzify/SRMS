<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$department = isset($_GET['department']) ? $_GET['department'] : '';
$courseId = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;
$searchTerm = isset($_GET['matric_number']) ? $_GET['matric_number'] : '';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$perPage = isset($_GET['per_page']) ? intval($_GET['per_page']) : 10;

try {
    $query = "SELECT s.id, s.first_name, s.last_name, s.matric_number, s.department, o.gpa, o.final_remark
              FROM students s
              LEFT JOIN overall_results o ON s.id = o.student_id
              WHERE 1=1";
    $countQuery = "SELECT COUNT(*) FROM students s WHERE 1=1";
    $params = [];

    if (!empty($department)) {
        $query .= " AND s.department = ?";
        $countQuery .= " AND s.department = ?";
        $params[] = $department;
    }

    if ($courseId > 0) {
        $query .= " AND s.id IN (SELECT student_id FROM course_registrations WHERE course_id = ?)";
        $countQuery .= " AND s.id IN (SELECT student_id FROM course_registrations WHERE course_id = ?)";
        $params[] = $courseId;
    }

    if (!empty($searchTerm)) {
        $query .= " AND (s.matric_number LIKE ? OR s.first_name LIKE ? OR s.last_name LIKE ?)";
        $countQuery .= " AND (s.matric_number LIKE ? OR s.first_name LIKE ? OR s.last_name LIKE ?)";
        $params[] = "%$searchTerm%";
        $params[] = "%$searchTerm%";
        $params[] = "%$searchTerm%";
    }

    $query .= " ORDER BY o.gpa DESC";
    $query .= " LIMIT " . (($page - 1) * $perPage) . ", $perPage";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $countStmt = $pdo->prepare($countQuery);
    $countStmt->execute($params);
    $totalCount = $countStmt->fetchColumn();

    $totalPages = ceil($totalCount / $perPage);

    echo json_encode([
        'success' => true,
        'results' => $results,
        'total_pages' => $totalPages,
        'current_page' => $page
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

