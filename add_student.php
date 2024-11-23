<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']);
    $gender = $_POST['gender'];
    $matricNumber = trim($_POST['matricNumber']);
    $department = $_POST['department'];

    if (empty($firstName) || empty($lastName) || empty($gender) || empty($matricNumber) || empty($department)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit();
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO students (first_name, last_name, gender, matric_number, department) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$firstName, $lastName, $gender, $matricNumber, $department]);

        echo json_encode(['success' => true, 'message' => 'Student added successfully']);
    } catch (PDOException $e) {
        if ($e->getCode() == '23000') {
            echo json_encode(['success' => false, 'message' => 'Matric number already exists']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error adding student: ' . $e->getMessage()]);
        }
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>