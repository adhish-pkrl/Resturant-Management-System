<?php

session_start();

include '../config/database.php';

$message = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";

    if (
        empty($name) ||
        empty($email) ||
        empty($password) ||
        empty($confirm_password)
    ) {

        $message = "Please fill in all fields.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";

    } elseif ($password !== $confirm_password) {

        $message = "Passwords do not match.";

    } elseif (strlen($password) < 6) {

        $message = "Password must be at least 6 characters.";

    } else {

        // Check whether email already exists
        $check_sql = "SELECT id FROM users WHERE email = ? LIMIT 1";

        $check_stmt = $conn->prepare($check_sql);

        if (!$check_stmt) {

            $message = "Something went wrong. Please try again.";

        } else {

            $check_stmt->bind_param("s", $email);
            $check_stmt->execute();

            $check_result = $check_stmt->get_result();

            if ($check_result->num_rows > 0) {

                $message = "An account with this email already exists.";

            } else {

                // Securely hash the password
                $hashed_password = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );

                // Public registration creates customer accounts
                $role = "customer";
                $status = "active";

                $sql = "INSERT INTO users
                        (name, email, password, role, status)
                        VALUES (?, ?, ?, ?, ?)";

                $stmt = $conn->prepare($sql);

                if (!$stmt) {

                    $message = "Registration failed. Please try again.";

                } else {

                    $stmt->bind_param(
                        "sssss",
                        $name,
                        $email,
                        $hashed_password,
                        $role,
                        $status
                    );

                    if ($stmt->execute()) {

                        $success = "Registration successful! You can now login.";

                    } else {

                        $message = "Registration failed. Please try again.";

                    }

                    $stmt->close();
                }
            }

            $check_stmt->close();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="description"
        content="Create your customer account at RMS Restaurant"
    >

    <title>Register - RMS Restaurant</title>


    <!-- Bootstrap CSS -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <!-- Bootstrap Icons -->

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >


    <!-- Our CSS -->

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

</head>


<body class="auth-page">

<main class="auth-wrapper">

    <div class="auth-card">


        <!-- ==========================================
             LEFT SIDE
             ========================================== -->

        <div class="auth-brand">

            <a
                href="../index.php"
                class="auth-logo"
            >
                <span>RMS</span> Restaurant
            </a>


            <div class="auth-brand-content">

                <span class="auth-badge">

                    <i class="bi bi-person-plus"></i>

                    Join Us

                </span>


                <h1>

                    Great Food.

                    <br>

                    <span>
                        Great Experience.
                    </span>

                </h1>


                <p>

                    Create your account and enjoy an easier
                    way to explore our menu, place orders and
                    reserve your table.

                </p>

            </div>


            <a
                href="../index.php"
                class="auth-home-link"
            >

                <i class="bi bi-arrow-left"></i>

                Back to Home

            </a>

        </div>


        <!-- ==========================================
             RIGHT SIDE
             ========================================== -->

        <div class="auth-form-section">

            <div class="auth-form-container">


                <!-- Heading -->

                <div class="mb-4">

                    <span class="text-warning fw-semibold">

                        CREATE ACCOUNT

                    </span>


                    <h2 class="fw-bold mt-2 mb-2">

                        Join our restaurant

                    </h2>


                    <p class="text-secondary mb-0">

                        Create your customer account to get started.

                    </p>

                </div>


                <!-- Error Message -->

                <?php if (!empty($message)): ?>

                    <div
                        class="alert alert-danger d-flex align-items-center gap-2"
                    >

                        <i class="bi bi-exclamation-circle-fill"></i>

                        <span>

                            <?php
                            echo htmlspecialchars($message);
                            ?>

                        </span>

                    </div>

                <?php endif; ?>


                <!-- Success Message -->

                <?php if (!empty($success)): ?>

                    <div
                        class="alert alert-success d-flex align-items-center gap-2"
                    >

                        <i class="bi bi-check-circle-fill"></i>

                        <span>

                            <?php
                            echo htmlspecialchars($success);
                            ?>

                        </span>

                    </div>

                <?php endif; ?>


                <!-- Registration Form -->

                <form
                    method="POST"
                    action=""
                >


                    <!-- Full Name -->

                    <div class="mb-3">

                        <label
                            for="name"
                            class="form-label fw-semibold"
                        >

                            Full Name

                        </label>


                        <div class="input-group">

                            <span class="input-group-text">

                                <i class="bi bi-person"></i>

                            </span>


                            <input
                                type="text"
                                class="form-control"
                                id="name"
                                name="name"
                                placeholder="Enter your full name"
                                required
                                autocomplete="name"
                            >

                        </div>

                    </div>


                    <!-- Email -->

                    <div class="mb-3">

                        <label
                            for="email"
                            class="form-label fw-semibold"
                        >

                            Email Address

                        </label>


                        <div class="input-group">

                            <span class="input-group-text">

                                <i class="bi bi-envelope"></i>

                            </span>


                            <input
                                type="email"
                                class="form-control"
                                id="email"
                                name="email"
                                placeholder="Enter your email"
                                required
                                autocomplete="email"
                            >

                        </div>

                    </div>


                    <!-- Password -->

                    <div class="mb-3">

                        <label
                            for="password"
                            class="form-label fw-semibold"
                        >

                            Password

                        </label>


                        <div class="input-group">

                            <span class="input-group-text">

                                <i class="bi bi-lock"></i>

                            </span>


                            <input
                                type="password"
                                class="form-control"
                                id="password"
                                name="password"
                                placeholder="Create a password"
                                required
                                autocomplete="new-password"
                            >

                        </div>


                        <div class="form-text">

                            Password must be at least 6 characters.

                        </div>

                    </div>


                    <!-- Confirm Password -->

                    <div class="mb-4">

                        <label
                            for="confirm_password"
                            class="form-label fw-semibold"
                        >

                            Confirm Password

                        </label>


                        <div class="input-group">

                            <span class="input-group-text">

                                <i class="bi bi-shield-lock"></i>

                            </span>


                            <input
                                type="password"
                                class="form-control"
                                id="confirm_password"
                                name="confirm_password"
                                placeholder="Confirm your password"
                                required
                                autocomplete="new-password"
                            >

                        </div>

                    </div>


                    <!-- Submit -->

                    <button
                        type="submit"
                        class="btn btn-warning w-100 py-3 fw-semibold"
                    >

                        <i class="bi bi-person-plus me-2"></i>

                        Create Account

                    </button>

                </form>


                <!-- Divider -->

                <div class="auth-divider">

                    <span>OR</span>

                </div>


                <!-- Login Link -->

                <p class="text-center text-secondary mb-0">

                    Already have an account?

                    <a
                        href="login.php"
                        class="fw-semibold text-warning"
                    >

                        Login here

                    </a>

                </p>

            </div>

        </div>

    </div>

</main>


<!-- Bootstrap JavaScript -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

</body>

</html>