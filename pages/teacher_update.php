<?php
session_start();
include '../config/env_school.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['teacher_id'];
    $name = $_POST['teacher_name'];
    $subject = $_POST['teacher_subject'];

    $stmt = $conn->prepare("UPDATE teachers SET teacher_name = ?, teacher_subject = ? WHERE teacher_id = ?");
    $stmt->execute([$name, $subject, $id]);

    $_SESSION['message'] = "Teacher updated successfully.";
    header("Location: teacher_dlt_data.php"); 
    exit();
}
