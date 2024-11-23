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

  // Initial load of courses
  loadCourses();
});