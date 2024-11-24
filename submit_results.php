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
$academicYearId = intval($_POST['academic_year_id']);
$sessionId = intval($_POST['session_id']);
$courses = isset($_POST['courses']) ? json_decode($_POST['courses'], true) : [];

if (empty($matricNumber) || $academicYearId <= 0 || $sessionId <= 0 || empty($courses)) {
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

    // Verify that the academic_year_id exists
    $stmt = $pdo->prepare("SELECT id, year FROM academic_years WHERE id = ?");
    $stmt->execute([$academicYearId]);
    $academicYear = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$academicYear) {
        throw new Exception("Invalid academic year ID: $academicYearId");
    }

    // Verify that the session_id exists
    $stmt = $pdo->prepare("SELECT id, name FROM sessions WHERE id = ?");
    $stmt->execute([$sessionId]);
    $session = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$session) {
        throw new Exception("Invalid session ID: $sessionId");
    }

    $results = [
        'student_name' => $student['first_name'] . ' ' . $student['last_name'],
        'matric_number' => $student['matric_number'],
        'academic_year' => $academicYearId,
        'academic_year_name' => $academicYear['year'],
        'session' => $sessionId,
        'session_name' => $session['name'],
        'courses' => []
    ];

    $totalGradePoints = 0;
    $totalCourses = 0;

    foreach ($courses as $courseId => $score) {
        $courseStmt = $pdo->prepare("SELECT course_name, course_code, grade_thresholds FROM courses WHERE id = ?");
        $courseStmt->execute([$courseId]);
        $course = $courseStmt->fetch(PDO::FETCH_ASSOC);

        if ($course) {
            $gradeThresholds = json_decode($course['grade_thresholds'], true);
            $grade = calculateGrade($score, $gradeThresholds);
            $gradePoint = getGradePoint($grade);

            $results['courses'][] = [
                'course_id' => $courseId,
                'course_code' => $course['course_code'],
                'course_name' => $course['course_name'],
                'score' => $score,
                'grade' => $grade,
                'grade_point' => $gradePoint
            ];

            $totalGradePoints += $gradePoint;
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
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
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
    $gradePoints = ['A' => 4.0, 'B' => 3.5, 'C' => 3.0, 'D' => 2.5, 'E' => 2.0, 'F' => 0];
    return isset($gradePoints[$grade]) ? $gradePoints[$grade] : 0;
}

function getFinalRemark($gpa) {
    if ($gpa >= 3.5) return 'Distinction';
    if ($gpa >= 3.0) return 'Upper Credit';
    if ($gpa >= 2.5) return 'Lower Credit';
    if ($gpa >= 2.0) return 'Pass';
    return 'Fail';
}

