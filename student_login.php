<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $matricNumber = $_POST['matricNumber'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM students WHERE matric_number = ?");
    $stmt->execute([$matricNumber]);
    $student = $stmt->fetch();

    if ($student && password_verify($password, $student['password'])) {
        $_SESSION['student_id'] = $student['id'];
        $_SESSION['student_matric'] = $student['matric_number'];
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid matric number or password']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>