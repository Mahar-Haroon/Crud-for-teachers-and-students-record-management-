<?php
$username = "root";
$password = "root";

try {
    // Create a PDO connection
    $conn = new PDO("mysql:host=localhost;dbname=school", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected Successfully<br>";

    // Sanitize and validate input data
    if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST["submit"])) {
        $teacher_id = trim($_POST['teacher_id']);
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Prepare the SQL query using placeholders
        $sql = "INSERT INTO students (teacher_id, student_name, student_email, Password) VALUES (:teacher_id, :name, :email, :password)";
        $stmt = $conn->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':teacher_id', $teacher_id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);

        // Execute the statement
        if ($stmt->execute()) {
            echo "User registered successfully!";
        } else {
            echo "Error: Could not insert data.";
        }
    } else {
        echo "Inputs are empty!";
    }

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
