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
<?php
// Handle form submission
$teachers = $conn->query("SELECT * from teachers")->fetchAll();
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['submit'])) {
    $target_dir = "../Controls/uploads/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    // Validate image
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] === 0) {
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check === false) {
            $imageErr1 = "File is not an image.";
            $uploadOk = 0;
        } else {
            // Allow certain file formats only
            if (!in_array($imageFileType, ["jpg", "jpeg", "png"])) {
                $imageErr2 = "Sorry, only JPG, JPEG, and PNG files are allowed.";
                $uploadOk = 0;
            }
        }
    } else {
        $imageErr3 = "Image file is required.";
        $uploadOk = 0;
    }
    // Validate input fields
    if (empty($_POST["first_name"])) {
        $fnameErr = "First name is required.";
    } else {
        $fname = test_input($_POST["first_name"]);
        if (!preg_match("/^[a-zA-Z-' ]*$/", $fname)) {
            $fnameErr = "Only letters and white space allowed.";
        }
    }
    if (empty($_POST["last_name"])) {
        $lnameErr = "Last name is required.";
    } else {
        $lname = test_input($_POST["last_name"]);
    }
    if (empty($_POST["email"])) {
        $emailErr = "Email is required.";
    } else {
        $email = test_input($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format.";
        }
    }
    if (isset($_POST["subject"])) {
        $subject = implode(',', $_POST["subject"]);
    } else {
        $subjectErr = "At least one subject is required.";
    }
    if (empty($_POST["password"])) {
        $passwordErr = "Password is required.";
    } else {
        $password = test_input($_POST["password"]);
    }
    $all_data = !empty($fname) && !empty($lname) && !empty($email) && !empty($password) && !empty($subject);
    // If no validation errors and upload is successful
    if ($all_data && $uploadOk == 1 && $_FILES["image"]["error"] === 0) {
        $imageData = file_get_contents($_FILES['image']['tmp_name']);
        $imageName = $_FILES["image"]["name"];
        try {
            //  the SQL query
            $sql = "INSERT INTO sign_up (Profile, first_name, last_name, email, password, subject) 
                    VALUES (:image_name, :first_name, :last_name, :email, :password, :subject)";
            $stmt = $conn->prepare($sql);
            // Bind parameters
            $stmt->bindParam(':first_name', $fname);
            $stmt->bindParam(':last_name', $lname);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':image_name', $_FILES["image"]["name"]);
            $stmt->bindParam(':subject', $subject);
            $stmt->execute();
            // If file upload is successful
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $success_message=  "The file ". htmlspecialchars( basename( $_FILES["fileToUpload"]["name"])). " has been uploaded.";
              } else {
                $error_of_all = "Sorry, there was an error uploading your file.";
              }
        } catch (PDOException $e) {
            $error_of_all = "Error: " . $e->getMessage();
        }
    } else {
        if (!$all_data) {
            $error_of_all = "Please fill in all required fields correctly.";
        }
    }
}
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
$er_al = !empty($error_of_all) . !empty($imageErr1) . !empty($imageErr2) . !empty($imageErr3);
$su_al = $success_message . $success_message
?>
<?php if (!empty($su_al)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $signupSuccess . "<br>" . $success_message; ?>
            <button class="btn-danger" style="float: right;background-color: brown;color:white;border:1px solid transparent;margin-top:-5px;margin-right:-15px;" type="button" onclick="this.parentElement.style.display='none';" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
    <?php if (!empty($er_al)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $error_of_all . $imageErr1 . $imageErr2 . $imageErr3; ?>
            <button class="btn-danger" style="float: right;background-color: brown;color:white;border:1px solid transparent;margin-top:-5px;margin-right:-15px;" type="button" onclick="this.parentElement.style.display='none';" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <section style="background-color: #eee;">
        <div class="container">
            <div class="row d-flex justify-content-center align-items-center">
                <div class="col-lg-12 col-xl-11">
                    <div class="card text-black" style="border-radius: 25px;">
                        <div class="card-body p-md-5">
                            <div class="row justify-content-center">
                                <div class="col-md-10 col-lg-6 col-xl-5 order-2 order-lg-1">
                                    <p class="text-center h1 fw-bold mb-5 mx-1 mx-md-4 mt-4">Sign up</p>
                                    <!-- Form starts here -->
                                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" class="mx-1 mx-md-4" enctype="multipart/form-data">
                                        <input type="file" name="image" accept=".jpg,.png,.jpeg" id="image" class="mb-4">                                                                        
                                        <div class="d-flex flex-row align-items-center mb-4">
                                            <div class="form-outline flex-fill mb-0">
                                                <input type="text" name="first_name" class="form-control" value="<?php echo $fname;?>"/>                                             
                                                <label class="form-label">First Name</label><br>
                                                <span class="error text-danger">* <?php echo $fnameErr;?></span>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-row align-items-center mb-4">
                                            <div class="form-outline flex-fill mb-0">
                                                <input type="text" name="last_name" class="form-control" value="<?php echo $lname;?>"/>
                                                <label class="form-label">Last Name</label><br>
                                                <span class="error text-danger">* <?php echo $lnameErr;?></span>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-row align-items-center mb-4">
                                            <div class="form-outline flex-fill mb-0">
                                                <input type="email" name="email" class="form-control" value="<?php echo $email;?>"/>
                                                <label class="form-label">Email</label><br>
                                                <span class="error text-danger">* <?php echo $emailErr;?></span>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-row align-items-center mb-4">
                                            <div class="form-outline flex-fill mb-0">
                                                <input type="password" name="password" class="form-control"/>                                                
                                                <label class="form-label">Password</label><br>
                                                <span class="error text-danger">* <?php echo $passwordErr;?></span>                                               
                                            </div>
                                        </div>
                                        <select class="form-control" name="id">
                                            <?php
                                            foreach($teachers as $teacher){?>
<option value="<?=$teacher['id']?>"><?=$teacher['teacher_name']?></option>
<?php   }                                                                                       
                                            ?>                                            
                                        </select>
                                        <div class="form-check d-flex justify-content-center mb-5" style="margin-left: 240px;">
                                            <label><h5>Subjects:</h5></label>
                                            <input class="form-check-input me-2 mx-3" type="checkbox" name="subject[]" value="math" <?php if (strpos($subject, 'math') !== false) echo 'checked'; ?> />
                                            <label>Math</label>
                                            <input class="form-check-input me-2 mx-3" type="checkbox" name="subject[]" value="Chem" <?php if (strpos($subject, 'Chem') !== false) echo 'checked'; ?> />
                                            <label>Chemistry</label>
                                            <input class="form-check-input me-2 mx-3" type="checkbox" name="subject[]" value="Eng" <?php if (strpos($subject, 'Eng') !== false) echo 'checked'; ?> />
                                            <label>English</label>
                                            <input class="form-check-input me-2 mx-3" style="z-index:111111;" type="checkbox" name="subject[]" value="Bio" <?php if (strpos($subject, 'Bio') !== false) echo 'checked'; ?> />
                                            <label>Biology</label>
                                            <input class="form-check-input me-2 mx-3" style="z-index:111111;" type="checkbox" name="subject[]" value="Urdu" <?php if (strpos($subject, 'Urdu') !== false) echo 'checked'; ?> />
                                            <label>Urdu</label>
                                            <input class="form-check-input me-2 mx-3" style="z-index:111111;" type="checkbox" name="subject[]" value="Physics" <?php if (strpos($subject, 'Physics') !== false) echo 'checked'; ?> />
                                            <label>Physics</label>
                                        </div>
                                        <div class="d-flex justify-content-center mx-4 mb-3 mb-lg-4">
                                            <input type="submit" class="btn btn-primary btn-lg" name="submit" value="REGISTER">
                                        </div>
                                    </form>
                                    <!-- End of form -->
                                </div>
                                <div class="col-md-10 col-lg-6 col-xl-7 d-flex align-items-center order-1 order-lg-2">
                                    <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-registration/draw1.webp" class="img-fluid" alt="Sample image">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Display success or error message --> 
</body>
</html>
