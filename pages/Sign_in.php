<?php
include '../config/env_school.php';
include 'include/header.php';
session_start();

$email = '';
$password = '';
$errors = [
    'email' => '',
    'password' => '',
    'login' => ''
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validate email
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Please enter a valid email address.";
    }

    // Validate password
    if (empty($password) || strlen($password) < 6) {
        $errors['password'] = "Password must be at least 6 characters.";
    }

    if (empty($errors['email']) && empty($errors['password'])) {
        try {
            $conn = new PDO("mysql:host=localhost;dbname=school", "root", "root");
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Fetch user by email
            $sql = "SELECT * FROM students WHERE student_email = :email";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verify password
            if ($user && password_verify($password, $user['Password'])) {
                $_SESSION['user_id'] = $user['student_id'];
                $_SESSION['email'] = $user['student_email'];
                $_SESSION['name'] = $user['student_name'];

                header("Location: trial_three_main.php");
                exit();
            } else {
                $errors['login'] = "Incorrect email or password.";
            }
        } catch (PDOException $e) {
            $errors['login'] = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign In</title>
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
    <div class="d-flex flex-column flex-column-fluid bgi-position-y-bottom position-x-center bgi-no-repeat bgi-size-contain bgi-attachment-fixed" style="background-image: url(assets/media/illustrations/sketchy-1/14.png)">
        <div class="d-flex flex-center flex-column flex-column-fluid p-10 pb-lg-20">
            <a href="#" class="mb-12">
                <img alt="Logo" src="assets/media/logos/logo-1.svg" class="h-40px">
            </a>

            <div class="w-lg-500px bg-body rounded shadow-sm p-10 p-lg-15 mx-auto">
                <form class="form w-100" method="post" novalidate>
                    <div class="text-center mb-10">
                        <h1 class="text-dark mb-3">Sign In to Metronic</h1>
                        <div class="text-gray-400 fw-bold fs-4">New Here?
                            <a href="Sign_up.php" class="link-primary fw-bolder">Create an Account</a>
                        </div>
                    </div>

                    <?php if (!empty($errors['login'])): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($errors['login']) ?></div>
                    <?php endif; ?>

                    <!-- Email -->
                    <div class="fv-row mb-10">
                        <label class="form-label fs-6 fw-bolder text-dark">Email</label>
                        <input class="form-control form-control-lg form-control-solid" type="email" name="email" value="<?= htmlspecialchars($email) ?>" required autocomplete="off">
                        <?php if (!empty($errors['email'])): ?>
                            <div class="text-danger"><?= htmlspecialchars($errors['email']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Password -->
                    <div class="fv-row mb-10">
                        <div class="d-flex flex-stack mb-2">
                            <label class="form-label fw-bolder text-dark fs-6 mb-0">Password</label>
                            <a href="#" class="link-primary fs-6 fw-bolder">Forgot Password?</a>
                        </div>
                        <input class="form-control form-control-lg form-control-solid" type="password" name="password" required autocomplete="off">
                        <?php if (!empty($errors['password'])): ?>
                            <div class="text-danger"><?= htmlspecialchars($errors['password']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Submit -->
                    <div class="text-center">
                        <button type="submit" class="btn btn-lg btn-primary w-100 mb-5">
                            <span class="indicator-label">Continue</span>
                        </button>

                        <div class="text-center text-muted text-uppercase fw-bolder mb-5">or</div>

                        <a href="#" class="btn btn-flex flex-center btn-light btn-lg w-100 mb-5">
                            <img alt="Logo" src="assets/media/svg/brand-logos/google-icon.svg" class="h-20px me-3">Continue with Google
                        </a>
                        <a href="#" class="btn btn-flex flex-center btn-light btn-lg w-100 mb-5">
                            <img alt="Logo" src="assets/media/svg/brand-logos/facebook-4.svg" class="h-20px me-3">Continue with Facebook
                        </a>
                        <a href="#" class="btn btn-flex flex-center btn-light btn-lg w-100">
                            <img alt="Logo" src="assets/media/svg/brand-logos/apple-black.svg" class="h-20px me-3">Continue with Apple
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Footer -->
        <div class="d-flex flex-center flex-column-auto p-10">
            <div class="d-flex align-items-center fw-bold fs-6">
                <a href="#" class="text-muted text-hover-primary px-2">About</a>
                <a href="#" class="text-muted text-hover-primary px-2">Contact</a>
                <a href="#" class="text-muted text-hover-primary px-2">Help</a>
            </div>
        </div>
    </div>
</div>

<script src="assets/plugins/global/plugins.bundle.js"></script>
<script src="assets/js/scripts.bundle.js"></script>
</body>
</html>
