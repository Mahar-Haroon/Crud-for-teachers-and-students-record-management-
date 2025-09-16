<?php
session_start();
include '../config/env_school.php';

if (isset($_POST['delete_teacher'])) {
    $id = $_POST['delete_teacher'];
    $stmt = $conn->prepare("DELETE FROM teachers WHERE teacher_id = ?");
    $stmt->execute([$id]);

    $_SESSION['message'] = "Teacher deleted successfully.";
    header("Location: teacher_dlt_data.php"); // Match your file name
    exit();
}
