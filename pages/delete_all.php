<?php
session_start();
include '../config/env_school.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_student'])) {
    $studentId = $_POST['delete_student'];

    $stmt = $conn->prepare("DELETE FROM students WHERE student_id = ?");
    $stmt->execute([$studentId]);

    $_SESSION['message'] = "Deleted successfully!";
    header("Location: All_students.php");
    exit();
}
