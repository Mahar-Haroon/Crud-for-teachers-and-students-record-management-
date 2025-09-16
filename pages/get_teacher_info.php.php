<?php
include '../config/env_school.php';
if (isset($_POST['teacher_id'])) {
    $teacher_id = $_POST['teacher_id'];
    $stmt = $conn->prepare("SELECT * FROM teachers WHERE id = :id");
    $stmt->bindParam(':id', $teacher_id);
    $stmt->execute();
    $teacher = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($teacher);
}
?>
<!-- .................................................................... -->
 <!-- Add this in your <head> -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- .................................................................... -->
<!-- Teacher Selection Dropdown -->
<div class="mb-4">
    <label for="teacherSelect">Select Teacher:</label>
    <select name="id" id="teacherSelect" class="form-control">
        <option value="">-- Select a Teacher --</option>
        <?php foreach ($teachers as $teacher): ?>
            <option value="<?= $teacher['id'] ?>"><?= $teacher['teacher_name'] ?></option>
        <?php endforeach; ?>
    </select>
</div>
<!-- Autofilled Fields -->
<div class="mb-4">
    <input type="text" name="teacher_name" id="teacher_name" class="form-control" placeholder="Teacher Name" value="<?= $fname ?>">
    <span class="text-danger"><?= $fnameErr ?></span>
</div>
<div class="mb-4">
    <input type="text" name="teacher_subject" id="teacher_subject" class="form-control" placeholder="Teacher Subject" value="<?= $password ?>">
    <span class="text-danger"><?= $passwordErr ?></span>
</div>
<!-- .................................................................... -->
<script>
$(document).ready(function () {
    $('#teacherSelect').on('change', function () {
        var teacherId = $(this).val();
        if (teacherId !== "") {
            $.ajax({
                url: 'fetch_teacher_data.php',
                type: 'POST',
                data: { teacher_id: teacherId },
                dataType: 'json',
                success: function (response) {
                    $('#teacher_name').val(response.teacher_name);
                    $('#teacher_subject').val(response.teacher_subject);
                }
            });
        } else {
            // Clear fields if no teacher is selected
            $('#teacher_name').val('');
            $('#teacher_subject').val('');
        }
    });
});
</script>
<!-- .................backend code submission code................................................... -->
<!-- Teacher select dropdown -->
<select class="form-control" name="id">
    <option value="">Select Teacher</option>
    <?php foreach ($teachers as $teacher): ?>
        <option value="<?= $teacher['id'] ?>"><?= $teacher['teacher_name'] ?></option>
    <?php endforeach; ?>
</select>
<!-- Auto-populated fields -->
<div class="mb-4">
    <label>Subject</label>
    <input type="text" id="teacher_subject" class="form-control" readonly>
</div>
<div class="mb-4">
    <label>Age</label>
    <input type="text" id="teacher_age" class="form-control" readonly>
</div>
