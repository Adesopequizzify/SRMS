<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit();
}

// Fetch total number of students
$stmt = $pdo->query("SELECT COUNT(*) FROM students");
$totalStudents = $stmt->fetchColumn();

// Fetch total number of courses
$stmt = $pdo->query("SELECT COUNT(*) FROM courses");
$totalCourses = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - LUFEM School</title>
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
                <!-- Dashboard Overview -->
                <div id="dashboard-page" class="content-page active">
                    <h2>Dashboard Overview</h2>
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Total Students</h5>
                                    <p class="card-text display-4"><?php echo $totalStudents; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Total Courses</h5>
                                    <p class="card-text display-4"><?php echo $totalCourses; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="mt-4">Welcome to the LUFEM School Admin Dashboard. Use the sidebar to navigate through different sections.</p>
                </div>

                <!-- Add Student Form -->
                <div id="add-student-page" class="content-page">
                    <h2>Add New Student</h2>
                    <div class="card mt-4">
                        <div class="card-body">
                            <form id="addStudentForm">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="firstName" class="form-label">First Name</label>
                                        <input type="text" class="form-control" id="firstName" name="firstName" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="lastName" class="form-label">Last Name</label>
                                        <input type="text" class="form-control" id="lastName" name="lastName" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="gender" class="form-label">Gender</label>
                                        <select class="form-select" id="gender" name="gender" required>
                                            <option value="">Select Gender</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="department" class="form-label">Department</label>
                                        <select class="form-select" id="department" name="department" required>
                                            <option value="">Select Department</option>
                                            <option value="Multimedia Technology">Multimedia Technology</option>
                                            <option value="Business Informatics">Business Informatics</option>
                                            <option value="Software Engineering">Software Engineering</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="matricNumber" class="form-label">Matric Number</label>
                                    <input type="text" class="form-control" id="matricNumber" name="matricNumber" required>
                                </div>
                                <button type="submit" class="btn btn-success">Add Student</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Student List -->
                <div id="student-list-page" class="content-page">
                    <h2>Student List</h2>
                    <div class="card mt-4">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Matric Number</th>
                                            <th>First Name</th>
                                            <th>Last Name</th>
                                            <th>Gender</th>
                                            <th>Department</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="studentList">
                                        <!-- Student list will be populated here by JavaScript -->
                                    </tbody>
                                </table>
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
</body>
</html>