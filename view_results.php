<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Results - LUFEM School</title>
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
                <li>
                    <a href="admin_dashboard.php">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="admin_dashboard.php#add-student">
                        <i class="bi bi-person-plus"></i> Add Student
                    </a>
                </li>
                <li>
                    <a href="admin_dashboard.php#student-list">
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
                        <i class="bi bi-file-earmark-text"></i> Result Entry
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
                <h2>View Results</h2>
                <div class="row mb-4">
                    <div class="col-md-4">
                        <select id="departmentFilter" class="form-select">
                            <option value="">All Departments</option>
                            <option value="Multimedia Technology">Multimedia Technology</option>
                            <option value="Business Informatics">Business Informatics</option>
                            <option value="Software Engineering">Software Engineering</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select id="courseFilter" class="form-select">
                            <option value="">All Courses</option>
                            <!-- Courses will be populated dynamically -->
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="text" id="matricSearch" class="form-control" placeholder="Search by Matric Number">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div id="resultsTable"></div>
                        <nav aria-label="Results pagination">
                            <ul class="pagination justify-content-center" id="pagination"></ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Details Modal -->
    <div class="modal fade" id="studentDetailsModal" tabindex="-1" aria-labelledby="studentDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="studentDetailsModalLabel">Student Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="studentDetailsContent">
                    <!-- Student details will be dynamically inserted here -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/dashboard.js"></script>
    <script src="js/view_results.js"></script>
</body>
</html>

