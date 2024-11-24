$(document).ready(function() {
  let currentPage = 1;
  const resultsPerPage = 10;

  // Load results on page load
  fetchResults();

  // Populate courses based on department selection
  $('#departmentFilter').change(function() {
    const department = $(this).val();
    $.ajax({
      url: 'get_courses.php',
      method: 'GET',
      data: {
        department: department,
        academic_year_id: $('#academicYearFilter').val(),
        session_id: $('#sessionFilter').val()
      },
      dataType: 'json',
      success: function(response) {
        let options = '<option value="">All Courses</option>';
        response.courses.forEach(function(course) {
          options += `<option value="${course.id}">${course.course_code} - ${course.course_name}</option>`;
        });
        $('#courseFilter').html(options);
        fetchResults(); // Fetch results after updating courses
      },
      error: function(xhr, status, error) {
        showNotification('Error fetching courses: ' + error, false);
      }
    });
  });

  // Fetch results when filters change
  $('#courseFilter, #academicYearFilter, #sessionFilter').change(fetchResults);

  // Implement real-time search for matric number
  $('#matricSearch').on('input', debounce(fetchResults, 300));

  function fetchResults() {
    const department = $('#departmentFilter').val();
    const courseId = $('#courseFilter').val();
    const matricNumber = $('#matricSearch').val();
    const academicYearId = $('#academicYearFilter').val();
    const sessionId = $('#sessionFilter').val();

    $.ajax({
      url: 'get_filtered_results.php',
      method: 'GET',
      data: {
        department: department,
        course_id: courseId,
        matric_number: matricNumber,
        academic_year_id: academicYearId,
        session_id: sessionId,
        page: currentPage,
        per_page: resultsPerPage
      },
      dataType: 'json',
      success: function(response) {
        displayResults(response.results);
        updatePagination(response.total_pages);
      },
      error: function(xhr, status, error) {
        showNotification('Error fetching results: ' + error, false);
      }
    });
  }

  function displayResults(results) {
    let tableHtml = `
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Name</th>
                        <th>Matric Number</th>
                        <th>Department</th>
                        <th>GPA</th>
                        <th>Final Remark</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
        `;

    results.forEach(function(result, index) {
      tableHtml += `
                <tr>
                    <td>${(currentPage - 1) * resultsPerPage + index + 1}</td>
                    <td>${result.first_name} ${result.last_name}</td>
                    <td>${result.matric_number}</td>
                    <td>${result.department}</td>
                    <td>${result.gpa !== null ? result.gpa.toFixed(2) : 'N/A'}</td>
                    <td>${result.final_remark !== null ? result.final_remark : 'N/A'}</td>
                    <td><button class="btn btn-sm btn-info view-details" data-student-id="${result.id}">View Details</button></td>
                </tr>
            `;
    });

    tableHtml += '</tbody></table>';
    $('#resultsTable').html(tableHtml);
  }

  function updatePagination(totalPages) {
    let paginationHtml = '';
    for (let i = 1; i <= totalPages; i++) {
      paginationHtml += `
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `;
    }
    $('#pagination').html(paginationHtml);
  }

  // Pagination click event
  $(document).on('click', '.page-link', function(e) {
    e.preventDefault();
    currentPage = parseInt($(this).data('page'));
    fetchResults();
  });

  // View student details
  $(document).on('click', '.view-details', function() {
    const studentId = $(this).data('student-id');
    const academicYearId = $('#academicYearFilter').val();
    const sessionId = $('#sessionFilter').val();
    console.log('View details clicked for student ID:', studentId); // Debug log
    $.ajax({
      url: 'get_student_details.php',
      method: 'GET',
      data: {
        student_id: studentId,
        academic_year_id: academicYearId,
        session_id: sessionId
      },
      dataType: 'json',
      success: function(response) {
        console.log('Student details response:', response); // Debug log
        if (response.success) {
          displayStudentDetails(response.details);
        } else {
          showNotification(response.message || 'Error fetching student details', false);
        }
      },
      error: function(xhr, status, error) {
        console.error('Error fetching student details:', error); // Debug log
        showNotification('Error fetching student details: ' + error, false);
      }
    });
  });

  function displayStudentDetails(details) {
    console.log('Displaying student details:', details); // Debug log
    let detailsHtml = `
            <h4>${details.first_name} ${details.last_name}</h4>
            <p><strong>Matric Number:</strong> ${details.matric_number}</p>
            <p><strong>Department:</strong> ${details.department}</p>
            <p><strong>Academic Year:</strong> ${details.academic_year || 'N/A'}</p>
            <p><strong>Session:</strong> ${details.session || 'N/A'}</p>
            <p><strong>Overall GPA:</strong> ${details.gpa !== null ? details.gpa.toFixed(2) : 'N/A'}</p>
            <p><strong>Final Remark:</strong> ${details.final_remark || 'N/A'}</p>
            <h5>Course Results:</h5>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Course Code</th>
                        <th>Course Name</th>
                        <th>Score</th>
                        <th>Grade</th>
                    </tr>
                </thead>
                <tbody>
        `;

    if (details.courses && details.courses.length > 0) {
      details.courses.forEach(function(course) {
        detailsHtml += `
                <tr>
                    <td>${course.course_code}</td>
                    <td>${course.course_name}</td>
                    <td>${course.score}</td>
                    <td>${course.grade}</td>
                </tr>
            `;
      });
    } else {
      detailsHtml += '<tr><td colspan="4">No course results found</td></tr>';
    }

    detailsHtml += '</tbody></table>';
    $('#studentDetailsContent').html(detailsHtml);
    $('#studentDetailsModal').modal('show');
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

  // Debounce function to limit the rate at which a function can fire
  function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
      const later = () => {
        clearTimeout(timeout);
        func(...args);
      };
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
    };
  }

  // Load Academic Years
  function loadAcademicYears() {
    $.ajax({
      url: 'get_academic_years.php',
      method: 'GET',
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          let options = '<option value="">All Academic Years</option>';
          response.academic_years.forEach(function(year) {
            options += `<option value="${year.id}">${year.year}</option>`;
          });
          $('#academicYearFilter').html(options);
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
          let options = '<option value="">All Sessions</option>';
          response.sessions.forEach(function(session) {
            options += `<option value="${session.id}">${session.name}</option>`;
          });
          $('#sessionFilter').html(options);
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

