$(document).ready(function() {
  $('#matricNumber').on('change', function() {
    const matricNumber = $(this).val();
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
            $('#gender').val(response.student.gender);
            $('#studentInfo').show();
            loadDepartmentCourses(response.student.department);
            $('#matricNumber').data('student-id', response.student.id);
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
  });

  function loadDepartmentCourses(department) {
    $.ajax({
      url: 'get_department_courses.php',
      method: 'GET',
      data: { department: department },
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
  }

  $('#courseRegistrationForm').submit(function(e) {
    e.preventDefault();
    const studentId = $('#matricNumber').data('student-id');
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
});