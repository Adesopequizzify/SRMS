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
                  <small>Academic Year: ${course.academic_year || 'N/A'}, Session: ${course.session || 'N/A'}</small>
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

  // Initial load of courses, academic years, and sessions
  loadCourses();
  loadAcademicYears();
  loadSessions();
});