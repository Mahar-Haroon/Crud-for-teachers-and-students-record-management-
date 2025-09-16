
<?php
include '../config/env_school.php';
if (empty($_SESSION)) {
    header("Location: Sign_in.php");
    exit();
}
$nameErr = $emailErr = $ageErr = $teacherErr = $subjectErr = "";
$student_name = $student_email = $student_age = $teacher_id = $teacher_subject = "";
$modalOpen = false; // Flag to control modal visibility
// Handle insert from modal
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_data"])) {
    $isValid = true;
    // Student Name
    if (empty($_POST["student_name"])) {
        $nameErr = "Student name is required.";
        $isValid = false;
    } else {
        $student_name = trim($_POST["student_name"]);
    }
    // Student Email
    if (empty($_POST["student_email"])) {
        $emailErr = "Email is required.";
        $isValid = false;
    } else {
        $student_email = trim($_POST["student_email"]);
        if (!filter_var($student_email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format.";
            $isValid = false;
        } else {
            // Check if email already exists
            $checkEmail = $conn->prepare("SELECT COUNT(*) FROM students WHERE student_email = ?");
            $checkEmail->execute([$student_email]);
            if ($checkEmail->fetchColumn() > 0) {
                $emailErr = "This email is already registered.";
                $isValid = false;
            }
        }
    }
    // Student Age
    if (empty($_POST["student_age"])) {
        $ageErr = "Age is required.";
        $isValid = false;
    } else {
        $student_age = $_POST["student_age"];
        if (!filter_var($student_age, FILTER_VALIDATE_INT) || $student_age <= 0) {
            $ageErr = "Please enter a valid age.";
            $isValid = false;
        }
    }
    // Teacher ID
    if (empty($_POST["teacher_id"])) {
        $teacherErr = "Teacher selection is required.";
        $isValid = false;
    } else {
        $teacher_id = $_POST["teacher_id"];
    }
    // Subject
    if (empty($_POST["teacher_subject"])) {
        $subjectErr = "Subject selection is required.";
        $isValid = false;
    } else {
        $teacher_subject = $_POST["teacher_subject"];
    }
    // Insert only if valid
    if ($isValid) {
        $sql = "INSERT INTO students (teacher_id, student_name, student_email, teacher_subject, student_age) 
                VALUES (:teacher_id, :student_name, :student_email, :teacher_subject, :student_age)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':teacher_id', $teacher_id);
        $stmt->bindParam(':student_name', $student_name);
        $stmt->bindParam(':student_email', $student_email);
        $stmt->bindParam(':teacher_subject', $teacher_subject);
        $stmt->bindParam(':student_age', $student_age);
        if ($stmt->execute()) {
            echo "<div class='alert alert-success mt-3'>Student data inserted successfully.</div>";
            echo "<script>var studentInserted = true;</script>";
            $student_name = $student_email = $student_age = $teacher_id = $teacher_subject = "";
        } else {
            echo "<div class='alert alert-danger mt-3'>Error inserting student data.</div>";
        }
    } else {
        $modalOpen = true;
    }  
}
// Fetch dropdown data
$teachers = $conn->query("SELECT DISTINCT teacher_id, teacher_name FROM teachers")->fetchAll();
$subjects = $conn->query("SELECT DISTINCT teacher_subject FROM teachers")->fetchAll();
$student_names = $conn->query("SELECT DISTINCT student_name,student_email FROM students")->fetchAll();
$student_ages = $conn->query("SELECT DISTINCT student_age,student_email FROM students ORDER BY student_age ASC")->fetchAll();
// Filters
$filter_result = $filter_result_2 = $filter_result_3 = [];
$filter_type = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['filter_teacher'])) {
        $teacher_id = $_POST['teacher_id'];
        $stmt = $conn->prepare("
            SELECT students.student_name, students.teacher_subject, teachers.teacher_name 
            FROM students
            INNER JOIN teachers ON students.teacher_subject = teachers.teacher_subject
            WHERE teachers.teacher_id = ?
        ");
        $stmt->execute([$teacher_id]);
        $filter_result = $stmt->fetchAll();
        $filter_type = 'Teacher';
    }
    if (isset($_POST['filter_subject'])) {
        $teacher_subject = $_POST['teacher_subject'];
        $stmt = $conn->prepare("
            SELECT students.student_name, teachers.teacher_name 
            FROM students
            INNER JOIN teachers ON students.teacher_subject = teachers.teacher_subject
            WHERE students.teacher_subject = ?
        ");
        $stmt->execute([$teacher_subject]);
        $filter_result_2 = $stmt->fetchAll();
        $filter_type = 'Subject';
    }
    if (isset($_POST['filter_age'])) {
        $student_age = $_POST['student_age'];
        $stmt = $conn->prepare("
            SELECT DISTINCT students.student_name
            FROM students
            WHERE students.student_age = ?
        ");
        $stmt->execute([$student_age]);
        $filter_result_3 = $stmt->fetchAll();
        $filter_type = 'Student Age';
    }
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
        <h3 class="mb-4 mt-4">Filter Students</h3>
        <!-- Filter by Teacher -->
        <form method="post" class="mb-3">
            <label>Teacher Name:</label>
            <div class="input-group" style="width:95%;">
                <select name="teacher_id" class="form-control" required>
                    <option value="">Select Teacher</option>
                    <?php foreach ($teachers as $teacher): ?>
                        <option value="<?= $teacher['teacher_id'] ?>"><?= $teacher['teacher_name'] ?></option>
                    <?php endforeach; ?>
                </select>
                <button name="filter_teacher" class="btn btn-success">Confirm</button>
            </div>
        </form>
        <!-- Filter by Subject -->
        <form method="post" class="mb-3">
            <label>Subject:</label>
            <div class="input-group" style="width:95%;">
                <select name="teacher_subject" class="form-control" required>
                    <option value="">Select Subject</option>
                    <?php foreach ($subjects as $sub): ?>
                        <option value="<?= $sub['teacher_subject'] ?>"><?= $sub['teacher_subject'] ?></option>
                    <?php endforeach; ?>
                </select>
                <button name="filter_subject" class="btn btn-success">Confirm</button>
            </div>
        </form>
        <!-- Filter by Age -->
        <?php
        // Extract just the age values
        $ages_only = array_column($student_ages, 'student_age');
        // Remove duplicates
        $unique_ages = array_unique($ages_only);
        ?>
        <form method="post" class="mb-3">
            <label>Student Age:</label>
            <div class="input-group" style="width:95%;">
                <select name="student_age" class="form-control" required>
                    <option value="">Select Age</option>
                    <?php foreach ($unique_ages as $age): ?>
                        <option value="<?= $age ?>"><?= $age ?></option>
                    <?php endforeach; ?>
                </select>
                <button name="filter_age" class="btn btn-success">Confirm</button>
            </div>
        </form>
        <!-- Filter by Student Name -->
        <form method="post" action="students_formdata.php" class="mb-3">
            <label>Student Name:</label>
            <div class="input-group" style="width:95%;">
                <select name="student_name" class="form-control" required>
                    <option value="">Select Student</option>
                    <?php foreach ($student_names as $student): ?>
                        <option value="<?= $student['student_name'] ?>"><?= $student['student_name'] ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="submit" value="Submit" name="filter_student_name" class="btn btn-success">
            </div>
        </form>     
        <!-- Filter Results -->
        <?php if (!empty($filter_result)): ?>
            <h4 class="mt-4">Results for <?= $filter_type ?> Filter</h4>
            <table class="table table-bordered text-center mt-3">
                <thead class="table-secondary">
                    <tr>
                        <th>Teacher Name</th>
                        <th>Student Name</th>
                        <th>Subject</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($filter_result as $row): ?>
                        <tr>
                            <td><?= $row['teacher_name'] ?></td>
                            <td><?= $row['student_name'] ?></td>
                            <td><?= $row['teacher_subject'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        <?php if (!empty($filter_result_2)): ?>
            <h4 class="mt-4">Results for <?= $filter_type ?> Filter</h4>
            <table class="table table-bordered text-center mt-3">
                <thead class="table-secondary">
                    <tr>
                        <th>Teacher Name</th>
                        <th>Student Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($filter_result_2 as $row): ?>
                        <tr>
                            <td><?= $row['teacher_name'] ?></td>
                            <td><?= $row['student_name'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        <?php if (!empty($filter_result_3)): ?>
            <h4 class="mt-4">Results for <?= $filter_type ?> Filter</h4>
            <table class="table table-bordered text-center mt-3">
                <thead class="table-secondary">
                    <tr>
                        <th>Student Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($filter_result_3 as $row): ?>
                        <tr>
                            <td><?= $row['student_name'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        <!-- Button to Trigger Modal -->
        <div class="container mt-5">
            <h3>Add New Student</h3>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                Add Student
            </button>
            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>">
                    <div class="modal-header">
                        <h5 class="modal-title">Insert Student Record</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Student Name:</label>
                            <input type="text" name="student_name" class="form-control" value="<?= htmlspecialchars($student_name) ?>">
                            <span class="text-danger"><?= $nameErr ?></span>
                        </div>
                        <div class="mb-3">
                            <label>Student Email:</label>
                            <input type="email" name="student_email" class="form-control" value="<?= htmlspecialchars($student_email) ?>">
                            <span class="text-danger"><?= $emailErr ?></span>
                        </div>
                        <div class="mb-3">
                            <label>Student Age:</label>
                            <input type="number" name="student_age" class="form-control" value="<?= htmlspecialchars($student_age) ?>">
                            <span class="text-danger"><?= $ageErr ?></span>
                        </div>
                        <div class="mb-3">
                            <label>Teacher:</label>
                            <select name="teacher_id" class="form-control">
                                <option value="">Select Teacher</option>
                                <?php foreach ($teachers as $teacher): ?>
                                    <option value="<?= $teacher['teacher_id'] ?>" <?= ($teacher['teacher_id'] == $teacher_id) ? 'selected' : '' ?>>
                                        <?= $teacher['teacher_name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="text-danger"><?= $teacherErr ?></span>
                        </div>
                        <div class="mb-3">
                            <label>Subject:</label>
                            <select name="teacher_subject" class="form-control">
                                <option value="">Select Subject</option>
                                <?php foreach ($subjects as $sub): ?>
                                    <option value="<?= $sub['teacher_subject'] ?>" <?= ($sub['teacher_subject'] == $teacher_subject) ? 'selected' : '' ?>>
                                        <?= $sub['teacher_subject'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="text-danger"><?= $subjectErr ?></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <input type="submit" name="update_data" class="btn btn-success" value="Add Student">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        <?php if ($modalOpen): ?>
            var myModal = new bootstrap.Modal(document.getElementById('exampleModal'));
            myModal.show();
        <?php endif; ?>

        if (typeof studentInserted !== 'undefined' && studentInserted) {
            var modal = bootstrap.Modal.getInstance(document.getElementById('exampleModal'));
            if (modal) {
                modal.hide();
            }
        }
    });
</script>
        </div>
    </div>
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
