<?php
include 'include/header.php';

$username = "root";
$password = "root";

// Initialize field-specific errors
$errors = [
    'name' => '',
    'email' => '',
    'password' => '',
    'confirm_password' => '',
    'toc' => ''
];
$success = "";

try {
    $conn = new PDO("mysql:host=localhost;dbname=school", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["submit"])) {
        $teacher_id = trim($_POST['teacher_id'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? '')); // Normalize email
        $password = trim($_POST['password'] ?? '');
        $confirmPassword = trim($_POST['confirm-password'] ?? '');
        $toc = $_POST['toc'] ?? '';

        $hasError = false;

        // Validate name
        if (empty($name)) {
            $errors['name'] = "Name is required.";
            $hasError = true;
        }

        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Invalid email format.";
            $hasError = true;
        } else {
            $stmt = $conn->prepare("SELECT COUNT(*) FROM students WHERE student_email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            if ($stmt->fetchColumn() > 0) {
                $errors['email'] = "This email is already taken.";
                $hasError = true;
            }
        }

        // Validate password
        if (strlen($password) < 6) {
            $errors['password'] = "Password must be at least 6 characters.";
            $hasError = true;
        }

        // Confirm password
        if ($password !== $confirmPassword) {
            $errors['confirm_password'] = "Passwords do not match.";
            $hasError = true;
        }

        // Terms & conditions
        if (empty($toc)) {
            $errors['toc'] = "You must agree to the Terms and Conditions.";
            $hasError = true;
        }

        if (!$hasError) {
            $Password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO students (teacher_id, student_name, student_email, Password) 
                    VALUES (:teacher_id, :name, :email, :password)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':teacher_id', $teacher_id);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $Password);

            if ($stmt->execute()) {
                $success = "User registered successfully!";
                $_POST = []; // Clear the form
            } else {
                $errors['name'] = "Something went wrong while inserting data.";
            }
        }
    }
} catch (PDOException $e) {
    $errors['name'] = "Connection failed: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link href="assets/plugins/global/plugins.bundle.css" rel="stylesheet">
    <link href="assets/css/styles.bundle.css" rel="stylesheet">
    <style>
        .text-danger {
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
<div class="d-flex flex-column flex-root">
    <div class="d-flex flex-center flex-column flex-column-fluid p-10 pb-lg-20">
        <a href="#" class="mb-12">
            <img alt="Logo" src="assets/media/logos/logo-1.svg" class="h-40px">
        </a>

        <div class="w-lg-600px bg-body rounded shadow-sm p-10 p-lg-15 mx-auto">

            <?php if (!empty($success)): ?>
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                <script>
                    window.addEventListener('DOMContentLoaded', function () {
                        Swal.fire({
                            icon: 'success',
                            title: 'Registration Successful!',
                            text: 'You have successfully registered!',
                            confirmButtonText: 'Ok, got it!',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        }).then(() => {
                            window.location.href = 'trial_three_main.php';
                        });
                    });
                </script>
            <?php endif; ?>

            <form class="form w-100" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="kt_sign_up_form" novalidate>
                <div class="mb-10 text-center">
                    <h1 class="text-dark mb-3">Create an Account</h1>
                    <div class="text-gray-400 fw-bold fs-4">Already have an account?
                        <a href="Sign_in.php" class="link-primary fw-bolder">Sign in here</a>
                    </div>
                </div>

                <input type="hidden" name="teacher_id" value="1">

                <!-- Name -->
                <div class="mb-7">
                    <label class="form-label fw-bolder text-dark fs-6">Name</label>
                    <input class="form-control form-control-lg form-control-solid" type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                    <?php if ($errors['name']): ?>
                        <div class="text-danger"><?= htmlspecialchars($errors['name']) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Email -->
                <div class="mb-7">
                    <label class="form-label fw-bolder text-dark fs-6">Email</label>
                    <input class="form-control form-control-lg form-control-solid" type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                    <?php if ($errors['email']): ?>
                        <div class="text-danger"><?= htmlspecialchars($errors['email']) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Password -->
                <div class="mb-7">
                    <label class="form-label fw-bolder text-dark fs-6">Password</label>
                    <input class="form-control form-control-lg form-control-solid" type="password" name="password" required>
                    <?php if ($errors['password']): ?>
                        <div class="text-danger"><?= htmlspecialchars($errors['password']) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Confirm Password -->
                <div class="mb-7">
                    <label class="form-label fw-bolder text-dark fs-6">Confirm Password</label>
                    <input class="form-control form-control-lg form-control-solid" type="password" name="confirm-password" required>
                    <?php if ($errors['confirm_password']): ?>
                        <div class="text-danger"><?= htmlspecialchars($errors['confirm_password']) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Terms -->
                <div class="mb-10">
                    <label class="form-check form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" name="toc" value="1" <?= isset($_POST['toc']) ? 'checked' : '' ?>>
                        <span class="form-check-label fw-bold text-gray-700 fs-6">
                            I Agree to the
                            <a href="#" class="ms-1 link-primary">Terms and Conditions</a>.
                        </span>
                    </label>
                    <?php if ($errors['toc']): ?>
                        <div class="text-danger"><?= htmlspecialchars($errors['toc']) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Submit -->
                <div class="text-center">
                    <button type="submit" name="submit" class="btn btn-lg btn-primary">
                        <span class="indicator-label">Submit</span>
                        <span class="indicator-progress">Please wait...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="assets/plugins/global/plugins.bundle.js"></script>
<script src="assets/js/scripts.bundle.js"></script>
</body>
</html>
