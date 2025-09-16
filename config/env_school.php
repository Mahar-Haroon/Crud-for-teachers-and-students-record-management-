<?php

$username = "root";
$password = "root";

// Create connection
$conn = new PDO('mysql:host=localhost;dbname=school', $username, $password);


session_start();

// Check connection
if($conn){
            $conn_link = "Connected Successfully" . "<br>";
} else {
            echo "Not Connected";
}

?>