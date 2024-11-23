// script.js
$(document).ready(function() {
  // Admin login form submission
  $('#adminLoginForm').submit(function(e) {
    e.preventDefault();
    const username = $('#adminUsername').val();
    const password = $('#adminPassword').val();

    $.ajax({
      url: 'admin_login.php',
      method: 'POST',
      data: { username: username, password: password },
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          window.location.href = 'admin_dashboard.php';
        } else {
          alert(response.message);
        }
      },
      error: function() {
        alert('An error occurred. Please try again.');
      }
    });
  });

  // Student login form submission
  $('#studentLoginForm').submit(function(e) {
    e.preventDefault();
    const matricNumber = $('#matricNumber').val();
    const password = $('#studentPassword').val();

    $.ajax({
      url: 'student_login.php',
      method: 'POST',
      data: { matricNumber: matricNumber, password: password },
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          window.location.href = 'student_dashboard.php';
        } else {
          alert(response.message);
        }
      },
      error: function() {
        alert('An error occurred. Please try again.');
      }
    });
  });
});