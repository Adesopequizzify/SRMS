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

    // Load student list if navigating to student-list page
    if (targetPage === 'student-list') {
      loadStudents();
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
    // Implement view student functionality here
    alert('View student details for ID: ' + studentId);
  });

  // Initial load of students if on student-list page
  if ($('#student-list-page').hasClass('active')) {
    loadStudents();
  }

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
          loadCourses();
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
              courseHtml += `<h6>${department}</h6><ul>`;
              response.courses[department].forEach(function(course) {
                courseHtml += `<li>${course.course_code} - ${course.course_name}</li>`;
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
});