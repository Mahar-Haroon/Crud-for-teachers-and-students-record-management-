<?php
include '../config/env_school.php';

$users = []; // default empty array

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["sub_form"])) {
    $selected_teacher_id = $_POST["id"];

    // Use a prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT teachers.teacher_id, teachers.teacher_name, students.student_name, teachers.teacher_subject, students.student_age
                            FROM teachers
                            INNER JOIN students ON teachers.teacher_id = students.teacher_id
                            WHERE teachers.teacher_id = ?");
    $stmt->execute([$selected_teacher_id]);
    $users = $stmt->fetchAll();
}

// Fetch all teachers for dropdown
$teachers = $conn->query("SELECT * FROM teachers")->fetchAll();
?>

<!-- Signup Form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <div class="mb-4">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label for="teacherSelect">Select Teacher:</label>
            <select name="id" class="form-control">
                <?php foreach ($teachers as $teacher): ?>
                    <option value="<?= $teacher['teacher_id'] ?>"><?= $teacher['teacher_name'] ?></option>
                <?php endforeach; ?>
            </select>
            <input type="submit" name="sub_form" class="btn btn-primary mt-2">
        </form>
    </div>

    <?php if (!empty($users)): ?>
        <table border='1' style='border-collapse:collapse;margin:auto;text-align:center;width:70%;'>
            <tr>
                <th>Teacher ID</th>
                <th>Teacher Name</th>
                <th>Student Name</th>
                <th>Teacher Subject</th>
                <th>Student Age</th>
            </tr>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= $user["teacher_id"] ?></td>
                    <td><?= $user["teacher_name"] ?></td>
                    <td><?= $user["student_name"] ?></td>
                    <td><?= $user["teacher_subject"] ?></td>
                    <td><?= $user["student_age"] ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>

</body>
</html>
