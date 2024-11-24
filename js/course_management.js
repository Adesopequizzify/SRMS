$(document).ready(function() {
    // Add Course Form Submission
    $('#addCourseForm').submit(function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        $.ajax({
            url: 'add_course.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showNotification(response.message, true);
                    $('#addCourseForm')[0].reset();
                    loadCourses(); // Reload the course list
                } else {
                    showNotification(response.message, false);
                }
            },
            error: function(xhr, status, error) {
                showNotification('An error occurred: ' + error, false);
            }
        });
    });

    // Load Courses Function
    function loadCourses() {
        $.ajax({
            url: 'get_courses.php',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    let courseHtml = '';
                    if (Object.keys(response.courses).length === 0) {
                        courseHtml = '<p>No courses found</p>';
                    } else {
                        for (let department in response.courses) {
                            courseHtml += `
                                <div class="department-section mb-4">
                                    <h6 class="department-title mb-2">${department}</h6>
                                    <ul class="list-unstyled">
                            `;
                            response.courses[department].forEach(function(course) {
                                courseHtml += `
                                    <li class="course-item mb-2 p-2 border rounded">
                                        <strong>${course.course_code}</strong> - ${course.course_name}
                                    </li>
                                `;
                            });
                            courseHtml += `
                                    </ul>
                                </div>
                            `;
                        }
                    }
                    $('#courseList').html(courseHtml);
                } else {
                    showNotification(response.message, false);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading courses:', error);
                showNotification('An error occurred while loading courses: ' + error, false);
            }
        });
    }

    // Load Academic Years
    function loadAcademicYears() {
        $.ajax({
            url: 'get_academic_years.php',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    let options = '<option value="">Select Academic Year</option>';
                    response.academic_years.forEach(function(year) {
                        options += `<option value="${year.id}">${year.year}</option>`;
                    });
                    $('#academicYear').html(options);
                } else {
                    showNotification(response.message, false);
                }
            },
            error: function(xhr, status, error) {
                showNotification('An error occurred while loading academic years: ' + error, false);
            }
        });
    }

    // Load Sessions
    function loadSessions() {
        $.ajax({
            url: 'get_sessions.php',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    let options = '<option value="">Select Session</option>';
                    response.sessions.forEach(function(session) {
                        options += `<option value="${session.id}">${session.name}</option>`;
                    });
                    $('#session').html(options);
                } else {
                    showNotification(response.message, false);
                }
            },
            error: function(xhr, status, error) {
                showNotification('An error occurred while loading sessions: ' + error, false);
            }
        });
    }

    // Initial load of everything
    loadAcademicYears();
    loadSessions();
    loadCourses(); // Load courses immediately

    // Show notification function
    function showNotification(message, isSuccess) {
        const popup = $('#customPopup');
        const popupMessage = $('.custom-popup-message');
        
        popupMessage.text(message);
        popup.removeClass('success error').addClass(isSuccess ? 'success' : 'error');
        popup.fadeIn();
        
        setTimeout(function() {
            popup.fadeOut();
        }, 3000);
    }
});

