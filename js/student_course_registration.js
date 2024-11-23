$(document).ready(function() {
  $('#matricNumber').on('change', function() {
    fetchStudentInfo();
  });

  $('#academicYear, #session').on('change', function() {
    if ($('#matricNumber').val()) {
      loadDepartmentCourses();
    }
  });

  function fetchStudentInfo() {
    const matricNumber = $('#matricNumber').val();
    if (matricNumber) {
      $.ajax({
        url: 'get_student_info.php',
        method: 'GET',
        data: { matricNumber: matricNumber },
        dataType: 'json',
        success: function(response) {
          if (response.success) {
            $('#studentName').val(response.student.first_name + ' ' + response.student.last_name);
            $('#department').val(response.student.department);
            $('#studentInfo').show();
            $('#matricNumber').data('student-id', response.student.id);
            loadDepartmentCourses();
          } else {
            showNotification(response.message, false);
            resetForm();
          }
        },
        error: function(xhr, status, error) {
          console.error('AJAX Error:', status, error);
          console.error('Response Text:', xhr.responseText);
          showNotification('An error occurred while fetching student information. Please try again.', false);
          resetForm();
        }
      });
    } else {
      resetForm();
    }
  }

  function loadDepartmentCourses() {
    const department = $('#department').val();
    const academicYearId = $('#academicYear').val();
    const sessionId = $('#session').val();

    if (department && academicYearId && sessionId) {
      $.ajax({
        url: 'get_department_courses.php',
        method: 'GET',
        data: {
          department: department,
          academic_year_id: academicYearId,
          session_id: sessionId
        },
        dataType: 'json',
        success: function(response) {
          if (response.success) {
            let courseHtml = '';
            response.courses.forEach(function(course) {
              courseHtml += `
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" value="${course.id}" id="course${course.id}" name="courses[]">
                  <label class="form-check-label" for="course${course.id}">
                    ${course.course_code} - ${course.course_name}
                  </label>
                </div>
              `;
            });
            $('#courseCheckboxes').html(courseHtml);
            $('#courseList').show();
            $('button[type="submit"]').show();
          } else {
            showNotification(response.message, false);
          }
        },
        error: function(xhr, status, error) {
          console.error('AJAX Error:', status, error);
          console.error('Response Text:', xhr.responseText);
          showNotification('An error occurred while loading courses. Please try again.', false);
        }
      });
    } else {
      $('#courseList').hide();
      $('button[type="submit"]').hide();
    }
  }

  $('#courseRegistrationForm').submit(function(e) {
    e.preventDefault();
    const studentId = $('#matricNumber').data('student-id');
    const academicYearId = $('#academicYear').val();
    const sessionId = $('#session').val();
    const selectedCourses = $('input[name="courses[]"]:checked').map(function() {
      return this.value;
    }).get();

    if (selectedCourses.length === 0) {
      showNotification('Please select at least one course', false);
      return;
    }

    $.ajax({
      url: 'register_courses.php',
      method: 'POST',
      data: {
        studentId: studentId,
        academicYearId: academicYearId,
        sessionId: sessionId,
        courses: JSON.stringify(selectedCourses)
      },
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          showNotification(response.message, true);
          resetForm();
        } else {
          showNotification(response.message, false);
        }
      },
      error: function(xhr, status, error) {
        console.error('AJAX Error:', status, error);
        console.error('Response Text:', xhr.responseText);
        showNotification('An error occurred while registering courses. Please try again.', false);
      }
    });
  });

  function resetForm() {
    $('#studentInfo').hide();
    $('#courseList').hide();
    $('button[type="submit"]').hide();
    $('#courseRegistrationForm')[0].reset();
  }

  function showNotification(message, success) {
    const popup = $('#customPopup');
    popup.removeClass('success error').addClass(success ? 'success' : 'error');
    popup.find('.custom-popup-message').text(message);
    popup.fadeIn(300);

    setTimeout(() => {
      popup.fadeOut(300);
    }, 5000);
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

  // Initial load of academic years and sessions
  loadAcademicYears();
  loadSessions();
});