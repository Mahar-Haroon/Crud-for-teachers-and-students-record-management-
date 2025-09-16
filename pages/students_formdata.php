<?php 
include '../config/env_school.php';

$subjects = $conn->query("SELECT DISTINCT teacher_subject FROM teachers")->fetchAll();
$student_names = $conn->query("SELECT DISTINCT student_name FROM students")->fetchAll();
$student_ages = $conn->query("SELECT DISTINCT student_age FROM students ORDER BY student_age ASC")->fetchAll();

$filter_result = [];
$filter_type = '';


    if (isset($_POST['filter_student_name'])) {
        $student_name = $_POST['student_name'];
        $stmt = $conn->prepare("
            SELECT students.student_name, students.teacher_subject, teachers.teacher_name ,students.student_email,students.student_age,teachers.teacher_id
            FROM students
            INNER JOIN teachers ON students.teacher_id = teachers.teacher_id
            WHERE students.student_name = ?
        ");
        $stmt->execute([$student_name]);
        $filter_result = $stmt->fetchAll();
        $filter_type = 'Teacher';
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





        <div class="container">
<table class="table table-bordered text-center mt-3">
            <thead class="table-secondary">
                <tr>
                    <th>Teacher ID</th>
                    <th>Teacher Name</th>
                    <th>Student Name</th>
                    <th>Student Email</th>
                    <th>Subject</th>
                    <th>Student Age</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($filter_result as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['teacher_id']) ?></td>
                        <td><?= htmlspecialchars($row['teacher_name']) ?></td>
                        <td><?= htmlspecialchars($row['student_name']) ?></td>
                        <td><?= htmlspecialchars($row['student_email']) ?></td>
                        <td><?= htmlspecialchars($row['teacher_subject']) ?></td>
                        <td><?= htmlspecialchars($row['student_age']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        
     

    

       


       





 

    
        

    </div>

    
            </div>
        </div>
    </div>
    
    </div>
    

</div>
<?php
        include 'include/footer.php';



        ?>




    
</body>

</html>