$(document).ready(function() {
    $('#matricNumber').on('change', function() {
        const matricNumber = $(this).val();
        if (matricNumber) {
            $.ajax({
                url: 'get_student_courses.php',
                method: 'GET',
                data: { matricNumber: matricNumber },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#studentName').val(response.student.first_name + ' ' + response.student.last_name);
                        $('#studentInfo').show();
                        displayCourseResults(response.courses);
                        $('button[type="submit"]').show();
                    } else {
                        showNotification(response.message, false);
                        resetForm();
                    }
                },
                error: function(xhr, status, error) {
                    showNotification('An error occurred: ' + error, false);
                    resetForm();
                }
            });
        } else {
            resetForm();
        }
    });

    function displayCourseResults(courses) {
        let courseHtml = '<h5 class="mt-4">Course Results</h5>';
        courses.forEach(function(course) {
            courseHtml += `
                <div class="mb-3">
                    <label for="course${course.id}" class="form-label">${course.course_code} - ${course.course_name}</label>
                    <input type="number" class="form-control course-score" id="course${course.id}" name="courses[${course.id}]" min="0" max="100" required data-course-id="${course.id}" data-grade-thresholds='${JSON.stringify(course.grade_thresholds)}'>
                    <div class="mt-2">
                        <span class="badge bg-secondary grade-display" id="grade${course.id}"></span>
                        <span class="badge bg-secondary pass-fail-display" id="passFail${course.id}"></span>
                    </div>
                </div>
            `;
        });
        $('#courseResults').html(courseHtml).show();

        $('.course-score').on('input', function() {
            const courseId = $(this).data('course-id');
            const score = parseFloat($(this).val());
            const gradeThresholds = $(this).data('grade-thresholds');
            updateGrade(courseId, score, gradeThresholds);
        });
    }

    function updateGrade(courseId, score, gradeThresholds) {
        let grade = 'F';
        let passFail = 'Fail';
        for (const [gradeKey, threshold] of Object.entries(gradeThresholds)) {
            if (score >= threshold) {
                grade = gradeKey;
                if (gradeKey !== 'F') {
                    passFail = 'Pass';
                }
                break;
            }
        }
        $(`#grade${courseId}`).text(`Grade: ${grade}`);
        $(`#passFail${courseId}`).text(passFail)
            .removeClass('bg-success bg-danger')
            .addClass(passFail === 'Pass' ? 'bg-success' : 'bg-danger');
    }

    $('#resultEntryForm').submit(function(e) {
        e.preventDefault();
        const formData = $(this).serializeArray();
        const courseData = {};
        formData.forEach(function(item) {
            if (item.name.startsWith('courses[')) {
                const courseId = item.name.match(/\d+/)[0];
                courseData[courseId] = item.value;
            }
        });
        
        $.ajax({
            url: 'submit_results.php',
            method: 'POST',
            data: {
                matricNumber: $('#matricNumber').val(),
                courses: JSON.stringify(courseData)
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showResultPreview(response.results);
                } else {
                    showNotification(response.message, false);
                }
            },
            error: function(xhr, status, error) {
                showNotification('An error occurred: ' + error, false);
            }
        });
    });

    function showResultPreview(results) {
        let previewHtml = `
            <h4>Student: ${results.student_name}</h4>
            <h5>Matric Number: ${results.matric_number}</h5>
            <table class="table">
                <thead>
                    <tr>
                        <th>Course Code</th>
                        <th>Course Name</th>
                        <th>Score</th>
                        <th>Grade</th>
                        <th>Pass/Fail</th>
                    </tr>
                </thead>
                <tbody>
        `;
        results.courses.forEach(function(course) {
            previewHtml += `
                <tr>
                    <td>${course.course_code}</td>
                    <td>${course.course_name}</td>
                    <td>${course.score}</td>
                    <td>${course.grade}</td>
                    <td>${course.pass_fail}</td>
                </tr>
            `;
        });
        previewHtml += `
                </tbody>
            </table>
            <h5>Overall GPA: ${results.gpa}</h5>
            <h5>Final Remark: ${results.final_remark}</h5>
        `;
        $('#resultPreviewContent').html(previewHtml);
        $('#resultPreviewModal').modal('show');
    }

    $('#confirmResultSubmission').click(function() {
        $.ajax({
            url: 'confirm_results.php',
            method: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showNotification('Results submitted successfully', true);
                    $('#resultPreviewModal').modal('hide');
                    resetForm();
                } else {
                    showNotification(response.message, false);
                }
            },
            error: function(xhr, status, error) {
                showNotification('An error occurred: ' + error, false);
            }
        });
    });

    function resetForm() {
        $('#studentInfo').hide();
        $('#courseResults').hide();
        $('button[type="submit"]').hide();
        $('#resultEntryForm')[0].reset();
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

