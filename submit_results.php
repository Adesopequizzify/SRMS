<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$matricNumber = $_POST['matricNumber'];
$courses = isset($_POST['courses']) ? $_POST['courses'] : [];

if (empty($matricNumber) || empty($courses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid data submitted']);
    exit;
}

try {
    // Get student information
    $stmt = $pdo->prepare("SELECT id, first_name, last_name, matric_number FROM students WHERE matric_number = ?");
    $stmt->execute([$matricNumber]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        echo json_encode(['success' => false, 'message' => 'Student not found']);
        exit;
    }

    $results = [
        'student_name' => $student['first_name'] . ' ' . $student['last_name'],
        'matric_number' => $student['matric_number'],
        'courses' => []
    ];

    $totalGradePoints = 0;
    $totalCourses = 0;

    foreach ($courses as $courseId => $score) {
        // Ensure courseId is not null or empty
        if (empty($courseId)) {
            continue;
        }

        $stmt = $pdo->prepare("SELECT course_name, course_code, grade_thresholds FROM courses WHERE id = ?");
        $stmt->execute([$courseId]);
        $course = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($course) {
            $gradeThresholds = json_decode($course['grade_thresholds'], true);
            $grade = calculateGrade($score, $gradeThresholds);
            $passFail = ($grade !== 'F') ? 'Pass' : 'Fail';

            $results['courses'][] = [
                'course_id' => $courseId,
                'course_code' => $course['course_code'],
                'course_name' => $course['course_name'],
                'score' => $score,
                'grade' => $grade,
                'pass_fail' => $passFail
            ];

            $totalGradePoints += getGradePoint($grade);
            $totalCourses++;
        }
    }

    $gpa = $totalCourses > 0 ? round($totalGradePoints / $totalCourses, 2) : 0;
    $results['gpa'] = $gpa;
    $results['final_remark'] = getFinalRemark($gpa);

    // Store results in session for confirmation
    $_SESSION['pending_results'] = $results;

    echo json_encode(['success' => true, 'results' => $results]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

function calculateGrade($score, $gradeThresholds) {
    foreach ($gradeThresholds as $grade => $threshold) {
        if ($score >= $threshold) {
            return $grade;
        }
    }
    return 'F';
}

function getGradePoint($grade) {
    $gradePoints = ['A' => 5, 'B' => 4, 'C' => 3, 'D' => 2, 'E' => 1, 'F' => 0];
    return isset($gradePoints[$grade]) ? $gradePoints[$grade] : 0;
}

function getFinalRemark($gpa) {
    if ($gpa >= 4.5) return 'First Class';
    if ($gpa >= 3.5) return 'Second Class Upper';
    if ($gpa >= 2.5) return 'Second Class Lower';
    if ($gpa >= 1.5) return 'Third Class';
    return 'Fail';
}

