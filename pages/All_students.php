<?php

include '../config/env_school.php';
if (empty($_SESSION)) {
    header("Location: Sign_in.php");
    exit();
}
// Fetch all students and teachers
$students = $conn->query("SELECT * FROM students")->fetchAll();
$teachers = $conn->query("SELECT * FROM teachers")->fetchAll();

// Get students with their teacher details (now includes teacher_id)
$stmt = $conn->prepare("
    SELECT 
        s.student_id, 
        s.student_name, 
        s.student_email, 
        s.student_age, 
        s.teacher_subject,
        s.teacher_id,
        t.teacher_name
    FROM students s
    LEFT JOIN teachers t ON s.teacher_id = t.teacher_id
    ORDER BY s.student_id ASC
");
$stmt->execute();
$filter_result = $stmt->fetchAll();

// Feedback or error modal
$showModal = false;
$showErrorModal = false;
$errorMessage = '';

if (isset($_SESSION['message'])) {
    $showModal = true;
    unset($_SESSION['message']);
}

if (isset($_SESSION['error'])) {
    $showErrorModal = true;
    $errorMessage = $_SESSION['error'];
    unset($_SESSION['error']);
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Student & Teacher Filters</title>

</head>

<body class="header-fixed header-tablet-and-mobile-fixed toolbar-enabled toolbar-fixed aside-enabled aside-fixed">
    <?php include 'include/header.php';
    include 'include/nav.php';
    include 'include/sidebar.php';

    ?>

<div class="d-flex flex-column flex-root">
    <div class="page d-flex flex-row flex-column-fluid">
      
    <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
        <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
            <div class="post d-flex flex-column-fluid" id="kt_post">
            <div class="container mt-4">
    <h3 class="mt-5 mb-4">Student & Teacher Information</h3>
    <table class="table table-bordered text-center">
        <thead class="table-secondary">
        <tr>
            <th style="width:6%;">Sr #</th>
            <th style="width: 8%;">Roll No</th>
            <th>Teacher Name</th>
            <th>Student Name</th>
            <th>Student Email</th>
            <th>Subject</th>
            <th style="width: 12%;">Student Age</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php $sr = 0; ?>
        <?php foreach ($filter_result as $row): ?>
            <tr>
                <td><b><?= ++$sr ?></b></td>
                <td><?= htmlspecialchars($row['student_id']); ?></td>
                <td><?= htmlspecialchars($row['teacher_name']); ?></td>
                <td><?= htmlspecialchars($row['student_name']); ?></td>
                <td><?= htmlspecialchars($row['student_email']); ?></td>
                <td><?= htmlspecialchars($row['teacher_subject']); ?></td>
                <td><?= htmlspecialchars($row['student_age']); ?></td>
                <td>
                    <form action="delete_all.php" method="POST" style="display:inline-block;">
                        <button type="submit" name="delete_student" value="<?= $row['student_id']; ?>" class="btn btn-danger"><img src="imgs/dlt.png" style="width:20px;"></button>
                    </form>
                    <button 
                        class="btn btn-success update-btn"  
                        data-id="<?= $row['student_id']; ?>"
                        data-name="<?= htmlspecialchars($row['student_name']); ?>"
                        data-email="<?= htmlspecialchars($row['student_email']); ?>"
                        data-age="<?= htmlspecialchars($row['student_age']); ?>"
                        data-teacher="<?= htmlspecialchars($row['teacher_id']); ?>"
                        data-subject="<?= htmlspecialchars($row['teacher_subject']); ?>"
                    >
                    <img src="imgs/update.png" style="width:20px;">

                    </button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Feedback Modal -->
<div class="modal fade" id="feedbackModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center">
            <div class="modal-body">
                <img src="imgs/tik.gif" alt="Deleted" class="img-fluid" style="max-width: 500px;">
                <p class="mt-3">Student record has been successfully updated or deleted.</p>
            </div>
        </div>
    </div>
</div>

<!-- Error Modal -->
<div class="modal fade" id="errorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Validation Error</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><?= htmlspecialchars($errorMessage); ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Update Modal -->
<div class="modal fade" id="updateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="update_student.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Update Student Info</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="student_id" id="update_id">
                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" class="form-control" name="student_name" id="update_name" required>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" class="form-control" name="student_email" id="update_email" required>
                    </div>
                    <div class="mb-3">
                        <label>Age</label>
                        <input type="number" class="form-control" name="student_age" id="update_age" required>
                    </div>
                    <div class="mb-3">
                        <label>Teacher</label>
                        <select class="form-select" name="teacher_name" id="update_teacher" required>
                            <option value="">Select Teacher</option>
                            <?php foreach ($teachers as $teacher): ?>
                                <option value="<?= $teacher['teacher_id']; ?>"><?= htmlspecialchars($teacher['teacher_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Subject</label>
                        <input type="text" class="form-control" name="teacher_subject" id="update_subject" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    <?php if ($showModal): ?>
    new bootstrap.Modal(document.getElementById('feedbackModal')).show();
    <?php endif; ?>

    <?php if ($showErrorModal): ?>
    new bootstrap.Modal(document.getElementById('errorModal')).show();
    <?php endif; ?>

    document.querySelectorAll('.update-btn').forEach(button => {
        button.addEventListener('click', function () {
            document.getElementById('update_id').value = this.dataset.id;
            document.getElementById('update_name').value = this.dataset.name;
            document.getElementById('update_email').value = this.dataset.email;
            document.getElementById('update_age').value = this.dataset.age;
            document.getElementById('update_subject').value = this.dataset.subject;

            const teacherSelect = document.getElementById('update_teacher');
            [...teacherSelect.options].forEach(option => {
                option.selected = option.value === this.dataset.teacher;
            });

            new bootstrap.Modal(document.getElementById('updateModal')).show();
        });
    });
});
</script>
            

            </div>
        </div>
        <?php
        include 'include/footer.php';



        ?>
    </div>
    
    </div>

</div>





    
</body>

</html>