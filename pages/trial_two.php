<?php
include '../config/env_school.php';

$users = []; // Default empty array

// Handle student form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["update_data"])) {
    $teacher_id = $_POST["teacher_id"];
    $student_name = $_POST["student_name"];
    $student_age = $_POST["student_age"];
    $teacher_subject = $_POST["teacher_subject"];

    // Insert into students table
    $sql = "INSERT INTO students (teacher_id , student_name,teacher_subject, student_age) 
            VALUES (:teacher_id, :student_name,:teacher_subject,:student_age)";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':student_name', $student_name);
    $stmt->bindParam(':student_age', $student_age);
    $stmt->bindParam(':teacher_id', $teacher_id);
    $stmt->bindParam(':teacher_subject', $teacher_subject);


    if ($stmt->execute()) {
        echo "<div class='alert alert-success mt-3'>Student data inserted successfully into students table.</div>";
    } else {
        echo "<div class='alert alert-danger mt-3'>Error inserting student data.</div>";
    }
}

// Handle fetch request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["sub_form"])) {
    $selected_teacher_id = $_POST["id"];

    $stmt = $conn->prepare("SELECT teachers.teacher_id, teachers.teacher_name, students.student_name, teachers.teacher_subject, students.student_age
                            FROM teachers
                            INNER JOIN students ON teachers.teacher_subject = students.teacher_subject
                            WHERE teachers.teacher_id = ?");
    $stmt->execute([$selected_teacher_id]);
    $users = $stmt->fetchAll();

    // Get teacher info for form
    $teacher_stmt = $conn->prepare("SELECT * FROM teachers WHERE teacher_id = ?");
    $teacher_stmt->execute([$selected_teacher_id]);
    $selected_teacher = $teacher_stmt->fetch();
}

// Fetch teachers for dropdown
$teachers = $conn->query("SELECT * FROM teachers")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Teacher & Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <div class="mb-4">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label for="teacherSelect">Select Teacher:</label>
            <select name="id" class="form-control" required>
                <?php foreach ($teachers as $teacher): ?>
                    <option value="<?= $teacher['teacher_id'] ?>" <?= isset($selected_teacher_id) && $selected_teacher_id == $teacher['teacher_id'] ? 'selected' : '' ?>>
                        <?= $teacher['teacher_name'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="submit" name="sub_form" value="Confirm" class="btn btn-primary mt-2">
        </form>
    </div>

    <?php if (!empty($users)): ?>
        <h4 class="mb-3">Students under  <?php echo $selected_teacher['teacher_name'] ?> (<?php echo $selected_teacher['teacher_subject'] ?>)</h4>
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
                    <td><?= htmlspecialchars($user["teacher_id"]) ?></td>
                    <td><?= htmlspecialchars($user["teacher_name"]) ?></td>
                    <td><?= htmlspecialchars($user["student_name"]) ?></td>
                    <td><?= htmlspecialchars($user["teacher_subject"]) ?></td>
                    <td><?= htmlspecialchars($user["student_age"]) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <!-- Add Student Form -->
        <h4 class="mt-4">Add New Student for <?php echo $selected_teacher['teacher_name'] ?> (<?php echo $selected_teacher['teacher_subject'] ?>)</h4>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="mt-3">
            <input type="hidden" name="teacher_id" value="<?= $selected_teacher['teacher_id'] ?>">

            <div class="mb-3">
                <label>Student Name:</label>
                <input type="text" name="student_name" value="<?= $user["student_name"] ?>" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Student Age:</label>
                <input type="number" name="student_age" value="<?= $user["student_age"] ?>" class="form-control" required>
            </div>
            <select name="id" class="form-control" required>
                <?php foreach ($teachers as $teacher): ?>
                    <option value="<?= $teacher['teacher_id'] ?>" <?= isset($selected_teacher_id) && $selected_teacher_id == $teacher['teacher_id'] ? 'selected' : '' ?>>
                        <?= $teacher['teacher_name'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <div class="mb-3">
                <label>Student Subject:</label>
                <input type="text" name="teacher_subject" value="<?= $user["teacher_subject"] ?>" class="form-control" required>
            </div>

            <input type="submit" name="update_data" class="btn btn-success" value="Add Student">
        </form>
    <?php endif; ?>
</div>

</body>
</html>
