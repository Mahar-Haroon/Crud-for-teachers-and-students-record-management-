<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["update_data"])) {
    $teacher_id = $_POST["teacher_id"];
    $teacher_name = $_POST["teacher_name"];
    $teacher_subject = $_POST["teacher_subject"];
    $student_name = $_POST["student_name"];
    $student_age = $_POST["student_age"];

    $sql = "INSERT INTO school_sign_up (teacher_id, teacher_name, teacher_subject, student_name, student_age) 
            VALUES (:teacher_id, :teacher_name, :teacher_subject, :student_name, :student_age)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':teacher_id', $teacher_id);
    $stmt->bindParam(':teacher_name', $teacher_name);
    $stmt->bindParam(':teacher_subject', $teacher_subject);
    $stmt->bindParam(':student_name', $student_name);
    $stmt->bindParam(':student_age', $student_age);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success mt-3'>Data inserted successfully into sign_up table.</div>";
    } else {
        echo "<div class='alert alert-danger mt-3'>Error inserting data.</div>";
    }
}


?>