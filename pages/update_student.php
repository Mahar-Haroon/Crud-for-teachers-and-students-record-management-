<?php
session_start();
include '../config/env_school.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['student_id'];
    $name = $_POST['student_name'];
    $email = $_POST['student_email'];
    $age = $_POST['student_age'];
    $teacher_id = $_POST['teacher_name'];
    $subject = $_POST['teacher_subject'];

    // Fetch the current student data
    $stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ?");
    $stmt->execute([$id]);
    $student = $stmt->fetch();

  

    if ($teacher_id == $student['teacher_id']) {
        $_SESSION['error'] = "⚠️ This teacher is already assigned to this student. Please select a different teacher.";
        header("Location: All_students.php");
        exit();
    }

    // Update the student
    $stmt = $conn->prepare("UPDATE students SET 
        student_name = ?, 
        student_email = ?, 
        student_age = ?, 
        teacher_id = ?, 
        teacher_subject = ?
        WHERE student_id = ?");
    $stmt->execute([$name, $email, $age, $teacher_id, $subject, $id]);

    $_SESSION['message'] = "Student updated successfully.";
    header("Location: All_students.php");
    exit();
}
?>
