<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $courseName = trim($_POST['courseName']);
    $courseCode = trim($_POST['courseCode']);
    $department = $_POST['department'];
    $gradeThresholds = [
        'A' => intval($_POST['gradeA']),
        'B' => intval($_POST['gradeB']),
        'C' => intval($_POST['gradeC']),
        'D' => intval($_POST['gradeD']),
        'E' => intval($_POST['gradeE']),
        'F' => intval($_POST['gradeF'])
    ];

    if (empty($courseName) || empty($courseCode) || empty($department) || 
        empty($gradeThresholds['A']) || empty($gradeThresholds['B']) || 
        empty($gradeThresholds['C']) || empty($gradeThresholds['D']) || 
        empty($gradeThresholds['E']) || empty($gradeThresholds['F'])) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit();
    }

    // Validate grade thresholds
    if ($gradeThresholds['A'] <= $gradeThresholds['B'] || 
        $gradeThresholds['B'] <= $gradeThresholds['C'] || 
        $gradeThresholds['C'] <= $gradeThresholds['D'] || 
        $gradeThresholds['D'] <= $gradeThresholds['E'] || 
        $gradeThresholds['E'] <= $gradeThresholds['F']) {
        echo json_encode(['success' => false, 'message' => 'Invalid grade thresholds']);
        exit();
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO courses (course_name, course_code, department, grade_thresholds) VALUES (?, ?, ?, ?)");
        $stmt->execute([$courseName, $courseCode, $department, json_encode($gradeThresholds)]);

        echo json_encode(['success' => true, 'message' => 'Course added successfully']);
    } catch (PDOException $e) {
        if ($e->getCode() == '23000') {
            echo json_encode(['success' => false, 'message' => 'Course code already exists']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error adding course: ' . $e->getMessage()]);
        }
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>