<select class="form-control" name="id" id="teacherSelect" onchange="fetchTeacherData(this.value)">
    <option value="">Select a Teacher</option>
    <?php foreach($teachers as $teacher): ?>
        <option value="<?= $teacher['id'] ?>"><?= $teacher['teacher_name'] ?></option>
    <?php endforeach; ?>
</select>

<div id="teacherDetails" style="margin-top: 20px;">
    <p><strong>Subject:</strong> <span id="teacherSubject"></span></p>
    <p><strong>Age:</strong> <span id="teacherAge"></span></p>
</div>

<script>
function fetchTeacherData(teacherId) {
    if (!teacherId) {
        document.getElementById("teacherSubject").innerText = "";
        document.getElementById("teacherAge").innerText = "";
        return;
    }

    fetch('get_teacher_info.php?id=' + teacherId)
        .then(response => response.json())
        .then(data => {
            document.getElementById("teacherSubject").innerText = data.teacher_subject || "N/A";
            document.getElementById("teacherAge").innerText = data.teacher_age || "N/A";
        })
        .catch(error => console.error('Error fetching teacher data:', error));
}
</script>

<?php
include '../config/env_school.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT teacher_subject, teacher_age FROM teachers WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $teacher = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($teacher) {
        echo json_encode($teacher);
    } else {
        echo json_encode([]);
    }
}
?>
