<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit();
}

// Fetch academic years
$stmt = $pdo->query("SELECT * FROM academic_years ORDER BY year DESC");
$academicYears = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch sessions
$stmt = $pdo->query("SELECT * FROM sessions ORDER BY name");
$sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Registration - LUFEM School</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
    <div id="customPopup" class="custom-popup">
        <div class="custom-popup-header">
            <span class="custom-popup-title">Notification</span>
            <span class="custom-popup-close">&times;</span>
        </div>
        <div class="custom-popup-message"></div>
    </div>

    <div class="wrapper">
        <!-- Sidebar -->
       <nav id="sidebar">
            <div class="sidebar-header">
                <h3>LUFEM School</h3>
            </div>

            <ul class="list-unstyled components">
                <li class="active">
                    <a href="#" data-page="dashboard">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="#" data-page="add-student">
                        <i class="bi bi-person-plus"></i> Add Student
                    </a>
                </li>
                <li>
                    <a href="#" data-page="student-list">
                        <i class="bi bi-people"></i> Student List
                    </a>
                </li>
                <li>
                    <a href="course_registration.php">
                        <i class="bi bi-book"></i> Course Registration
                    </a>
                </li>
                <li>
    <a href="student_course_registration.php">
        <i class="bi bi-pencil-square"></i> Student Course Registration
    </a>
</li>
                <li>
    <a href="result_entry.php">
        <i class="bi bi-pencil-square"></i> Result Entry
    </a>
</li>
<li class="active">
                    <a href="view_results.php">
                        <i class="bi bi-table"></i> View Results
                    </a>
                </li>
                <li>
                    <a href="logout.php">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </li>
            </ul>
        </nav>


        <!-- Page Content -->
        <div id="content">
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-success">
                        <i class="bi bi-list"></i>
                    </button>
                    <span class="navbar-text ms-auto">
                        Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>
                    </span>
                </div>
            </nav>

            <!-- Main Content Area -->
            <div class="container-fluid mt-4">
                <h2>Course Registration</h2>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Add New Course</h5>
                                <form id="addCourseForm">
                                    <div class="mb-3">
                                        <label for="courseName" class="form-label">Course Name</label>
                                        <input type="text" class="form-control" id="courseName" name="courseName" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="courseCode" class="form-label">Course Code</label>
                                        <input type="text" class="form-control" id="courseCode" name="courseCode" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="department" class="form-label">Department</label>
                                        <select class="form-select" id="department" name="department" required>
                                            <option value="">Select Department</option>
                                            <option value="Multimedia Technology">Multimedia Technology</option>
                                            <option value="Business Informatics">Business Informatics</option>
                                            <option value="Software Engineering">Software Engineering</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="academicYear" class="form-label">Academic Year</label>
                                        <select class="form-select" id="academicYear" name="academicYear" required>
                                            <option value="">Select Academic Year</option>
                                            <?php foreach ($academicYears as $year): ?>
                                                <option value="<?php echo htmlspecialchars($year['id']); ?>">
                                                    <?php echo htmlspecialchars($year['year']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="session" class="form-label">Session</label>
                                        <select class="form-select" id="session" name="session" required>
                                            <option value="">Select Session</option>
                                            <?php foreach ($sessions as $session): ?>
                                                <option value="<?php echo htmlspecialchars($session['id']); ?>">
                                                    <?php echo htmlspecialchars($session['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Grade Thresholds</label>
                                        <div class="row g-2">
                                            <div class="col-md-4">
                                                <input type="number" class="form-control" name="gradeA" placeholder="A %" required min="0" max="100">
                                            </div>
                                            <div class="col-md-4">
                                                <input type="number" class="form-control" name="gradeB" placeholder="B %" required min="0" max="100">
                                            </div>
                                            <div class="col-md-4">
                                                <input type="number" class="form-control" name="gradeC" placeholder="C %" required min="0" max="100">
                                            </div>
                                            <div class="col-md-4">
                                                <input type="number" class="form-control" name="gradeD" placeholder="D %" required min="0" max="100">
                                            </div>
                                            <div class="col-md-4">
                                                <input type="number" class="form-control" name="gradeE" placeholder="E %" required min="0" max="100">
                                            </div>
                                            <div class="col-md-4">
                                                <input type="number" class="form-control" name="gradeF" placeholder="F %" required min="0" max="100">
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Add Course</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Course List</h5>
                                <div id="courseList"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/dashboard.js"></script>
    <script src="js/course_management.js"></script>
</body>
</html>