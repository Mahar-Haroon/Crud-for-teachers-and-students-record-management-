<?php
include '../config/env_school.php';
if (empty($_SESSION)) {
    header("Location: Sign_in.php");
    exit();
}
// Fetch all teachers
$teachers = $conn->query("SELECT DISTINCT teacher_name, teacher_id, teacher_age, teacher_subject FROM teachers")->fetchAll();
// Check if modal should be shown
$showModal = false;
if (isset($_SESSION['message'])) {
    $showModal = true;
    unset($_SESSION['message']);
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
            <div class="container mt-5">
        <h2 class="mt-4 mb-3 text-center">Teachers List</h2>
        <table class="table table-bordered text-center">
            <thead class="table-secondary">
                <tr>
                    <th style="width: 5%;">Sr #</th>
                    <th>Teacher Name</th>
                    <th>Teacher Subject</th>
                    <th style="width: 20%;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $sr = 0; ?>
                <?php foreach ($teachers as $teacher): ?>
                    <tr>
                        <td><?= ++$sr ?></td>
                        <td><?= htmlspecialchars($teacher['teacher_name']) ?></td>
                        <td><?= htmlspecialchars($teacher['teacher_subject']) ?></td>
                        <td class="d-flex justify-content-center gap-2">
                            <form action="teacher_delete.php" method="POST">
                                <button type="submit" name="delete_teacher" value="<?= $teacher['teacher_id'] ?>" class="btn btn-danger btn-sm"><img src="imgs/dlt.png" style="width:20px;"></button>
                            </form>
                            <button style="width: ; height: 33px;  text-align: center;font-size: small;"   
                                class="btn btn-success btn-sm updateBtn"
                                data-id="<?= $teacher['teacher_id'] ?>"
                                data-name="<?= htmlspecialchars($teacher['teacher_name']) ?>"
                                data-subject="<?= htmlspecialchars($teacher['teacher_subject']) ?>"
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
    <div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
                <div class="modal-body">
                    <img src="imgs/delete.gif" alt="Deleted" class="img-fluid" style="max-width: 500px;">
                    <p class="mt-3">Teacher record has been successfully updated or deleted.</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Update Modal -->
    <div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="teacher_update.php" method="POST" class="modal-content p-3">
                <input type="hidden" name="teacher_id" id="update_id">
                <div class="modal-header">
                    <h5 class="modal-title">Update Teacher</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="update_name" class="form-label">Teacher Name</label>
                        <input type="text" class="form-control" name="teacher_name" id="update_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="update_subject" class="form-label">Teacher Subject</label>
                        <input type="text" class="form-control" name="teacher_subject" id="update_subject" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Modal Script -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            <?php if ($showModal): ?>
                var modal = new bootstrap.Modal(document.getElementById('feedbackModal'));
                modal.show();
            <?php endif; ?>
            // Handle update button click
            document.querySelectorAll('.updateBtn').forEach(button => {
                button.addEventListener('click', () => {
                    document.getElementById('update_id').value = button.getAttribute('data-id');
                    document.getElementById('update_name').value = button.getAttribute('data-name');
                    document.getElementById('update_subject').value = button.getAttribute('data-subject');
                    const updateModal = new bootstrap.Modal(document.getElementById('updateModal'));
                    updateModal.show();
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
