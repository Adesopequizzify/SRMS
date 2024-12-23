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
    <title>Student Course Registration - LUFEM School</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
  <div id="debugOutput" style="color: red; font-weight: bold; margin-top: 10px;"></div>
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
                <li class="">
                    <a href="/admin_dashboard.php" data-page="">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li>
<li>
    <a href="admin_dashboard.php?page=add-student">
        <i class="bi bi-person-plus"></i> Register New Student
    </a>
</li>
<li>
    <a href="admin_dashboard.php?page=student-list">
        <i class="bi bi-people"></i> Registered Students
    </a>
</li>
                <li>
                    <a href="course_registration.php">
                        <i class="bi bi-book"></i> Course Registration
                    </a>
                </li>
                <li class="active">
    <a href="student_course_registration.php">
        <i class="bi bi-pencil-square"></i> Student Course Registration
    </a>
</li>
                <li>
    <a href="result_entry.php">
        <i class="bi bi-pencil-square"></i> Insert New Result
    </a>
</li>
<li class="">
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
                <h2>Student Course Registration</h2>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <form id="courseRegistrationForm">
                                    <div class="mb-3">
                                        <label for="matricNumber" class="form-label">Matric Number</label>
                                        <input type="text" class="form-control" id="matricNumber" name="matricNumber" required>
                                    </div>
                                    <div id="studentInfo" style="display: none;">
                                        <div class="mb-3">
                                            <label for="studentName" class="form-label">Student Name</label>
                                            <input type="text" class="form-control" id="studentName" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label for="department" class="form-label">Department</label>
                                            <input type="text" class="form-control" id="department" readonly>
                                        </div>
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
                                    <div id="courseList" style="display: none;">
                                        <h5 class="mt-4">Available Courses</h5>
                                        <div id="courseCheckboxes"></div>
                                    </div>
                                    <button type="submit" class="btn btn-primary mt-3" style="display: none;">Register Courses</button>
                                </form>
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
    <script src="js/student_course_registration.js"></script>
</body>
</html>