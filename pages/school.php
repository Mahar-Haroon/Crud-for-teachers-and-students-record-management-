<?php
include '../config/env_school.php';

$users = $conn->query("SELECT teachers.teacher_id, teachers.teacher_name, students.student_name, teachers.teacher_subject, students.student_age
FROM teachers
INNER JOIN students ON teachers.teacher_id = students.teacher_id;")->fetchAll();


echo $user["teacher_name"];


echo "<table border='1' style='border-collapse:collapse;margin:auto;text-align:center;width:70%;'>";

echo "<tr>";

echo  "<th>Teacher ID</th>";
echo  "<th>Teacher Name</th>";
echo  "<th>Student Name</th>";
echo  "<th>Teacher Subject</th>";
echo  "<th>Student Age</th>";

echo "</tr>";


foreach($users as $user) {
echo "<tr>";

echo  "<td>" .$user["teacher_id"]  .  "</td>";
echo  "<td>" .$user["teacher_name"]  .  "</td>";
echo  "<td>" .$user["student_name"] .  "</td>";
echo  "<td>" .$user["teacher_subject"]   ."</td>";
echo  "<td>" .$user["student_age"] .  "</td>";




 echo "</tr>";
}

echo "</table>";
?>