$(document).ready(function() {
  // Sidebar toggle
  $('#sidebarCollapse').on('click', function() {
    $('#sidebar').toggleClass('active');
  });

  // Page navigation
  $('a[data-page]').on('click', function(e) {
    e.preventDefault();
    const targetPage = $(this).data('page');

    // Update active state in sidebar
    $('#sidebar ul li').removeClass('active');
    $(this).parent('li').addClass('active');

    // Hide all pages and show target page
    $('.content-page').removeClass('active');
    $(`#${targetPage}-page`).addClass('active');

    // Load specific content based on the page
    switch (targetPage) {
      case 'student-list':
        loadStudents();
        break;
      case 'course-registration':
        loadCourses();
        break;
      case 'result-entry':
        loadAcademicYears();
        loadSessions();
        break;
        // Add more cases for other pages as needed
    }
  });

  // Custom popup notification system
  window.showNotification = function(message, success = true) {
    const popup = $('#customPopup');
    popup.removeClass('success error').addClass(success ? 'success' : 'error');
    popup.find('.custom-popup-message').text(message);
    popup.fadeIn(300);

    setTimeout(() => {
      popup.fadeOut(300);
    }, 5000);
  };

  // Close popup when clicking close button
  $('.custom-popup-close').on('click', function() {
    $('#customPopup').fadeOut(300);
  });

  // Add Student Form Submission
  $('#addStudentForm').submit(function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    $.ajax({
      url: 'add_student.php',
      method: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          showNotification(response.message, true);
          $('#addStudentForm')[0].reset();
        } else {
          showNotification(response.message, false);
        }
      },
      error: function(xhr, status, error) {
        showNotification('An error occurred: ' + error, false);
      }
    });
  });

  // Load Students Function
  window.loadStudents = function() {
    $.ajax({
      url: 'get_students.php',
      method: 'GET',
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          let studentHtml = '';
          if (response.students.length === 0) {
            studentHtml = '<tr><td colspan="6" class="text-center">No students found</td></tr>';
          } else {
            response.students.forEach(function(student) {
              studentHtml += `
                <tr>
                  <td>${student.matric_number}</td>
                  <td>${student.first_name}</td>
                  <td>${student.last_name}</td>
                  <td>${student.gender}</td>
                  <td>${student.department}</td>
                  <td>
                    <button class="btn btn-sm btn-primary view-student" data-id="${student.id}">View</button>
                    <button class="btn btn-sm btn-info register-courses" data-id="${student.id}">Register Courses</button>
                  </td>
                </tr>
              `;
            });
          }
          $('#studentList').html(studentHtml);
        } else {
          showNotification(response.message, false);
        }
      },
      error: function(xhr, status, error) {
        showNotification('An error occurred while loading students: ' + error, false);
      }
    });
  };

  // View Student Details
  $(document).on('click', '.view-student', function() {
    const studentId = $(this).data('id');
    $.ajax({
      url: 'get_student_details.php',
      method: 'GET',
      data: { student_id: studentId },
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          // Implement a modal or a new page to display student details
          console.log(response.student);
          // For example:
          // $('#studentDetailsModal').modal('show');
          // $('#studentDetailsContent').html(...);
        } else {
          showNotification(response.message, false);
        }
      },
      error: function(xhr, status, error) {
        showNotification('An error occurred: ' + error, false);
      }
    });
  });

  // Register Courses for Student
  $(document).on('click', '.register-courses', function() {
    const studentId = $(this).data('id');
    // Redirect to the course registration page with the student ID
    window.location.href = `student_course_registration.php?student_id=${studentId}`;
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
              courseHtml += `<h6>${department}</h6><ul>`;
              response.courses[department].forEach(function(course) {
                courseHtml += `
                  <li>
                    ${course.course_code} - ${course.course_name}
                    <br>
                    <small>Academic Year: ${course.academic_year}, Session: ${course.session}</small>
                  </li>`;
              });
              courseHtml += '</ul>';
            }
          }
          $('#courseList').html(courseHtml);
        } else {
          showNotification(response.message, false);
        }
      },
      error: function(xhr, status, error) {
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
          $('.academic-year-select').html(options);
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
          $('.session-select').html(options);
        } else {
          showNotification(response.message, false);
        }
      },
      error: function(xhr, status, error) {
        showNotification('An error occurred while loading sessions: ' + error, false);
      }
    });
  }

  // Initial load of students if on student-list page
  if ($('#student-list-page').hasClass('active')) {
    loadStudents();
  }

  // Initial load of courses if on course-registration page
  if ($('#course-registration-page').hasClass('active')) {
    loadCourses();
  }

  // Initial load of academic years and sessions if on result-entry page
  if ($('#result-entry-page').hasClass('active')) {
    loadAcademicYears();
    loadSessions();
  }
});