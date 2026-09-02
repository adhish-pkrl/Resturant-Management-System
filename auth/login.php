<?php

session_start();

include '../config/database.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($email) || empty($password)) {

        $message = "Please enter email and password.";

    } else {

        $sql = "SELECT * FROM users WHERE email = ? AND status = 'active' LIMIT 1";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows == 1) {

            $user = $result->fetch_assoc();

            if (password_verify($password, $user["password"])) {

                $_SESSION["user_id"] = $user["id"];
                $_SESSION["user_name"] = $user["name"];
                $_SESSION["user_role"] = $user["role"];

                if ($user["role"] == "admin") {
                    header("Location: ../admin/");
                    exit();
                }

                if ($user["role"] == "staff") {
                    header("Location: ../staff/");
                    exit();
                }

                if ($user["role"] == "customer") {
                    header("Location: ../customer/");
                    exit();
                }

            } else {

                $message = "Invalid email or password.";

            }

        } else {

            $message = "Invalid email or password.";

        }

        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta
        name="description"
        content="Login to the Restaurant Management System"
    >

    <title>Login - RMS Restaurant</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Bootstrap Icons -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

    <!-- RMS CSS -->
    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

</head>

<body class="auth-page">

    <main class="auth-wrapper">

        <div class="auth-card">

            <!-- LEFT SIDE -->

            <div class="auth-brand">

                <a
                    href="../index.php"
                    class="auth-logo"
                >
                    <span>RMS</span> Restaurant
                </a>

                <div class="auth-brand-content">

                    <span class="auth-badge">
                        <i class="bi bi-stars"></i>
                        Welcome Back
                    </span>

                    <h1>
                        Good Food.
                        <br>
                        <span>Great Moments.</span>
                    </h1>

                    <p>
                        Sign in to continue your restaurant experience,
                        manage your orders, reservations and more.
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


            <!-- RIGHT SIDE -->

            <div class="auth-form-section">

                <div class="auth-form-container">

                    <div class="mb-4">

                        <span class="text-warning fw-semibold">
                            ACCOUNT LOGIN
                        </span>

                        <h2 class="fw-bold mt-2 mb-2">
                            Welcome back!
                        </h2>

                        <p class="text-secondary mb-0">
                            Enter your details to access your account.
                        </p>

                    </div>


                    <!-- Error Message -->

                    <?php if (!empty($message)): ?>

                        <div class="alert alert-danger d-flex align-items-center gap-2">
                            <i class="bi bi-exclamation-circle-fill"></i>

                            <span>
                                <?php echo htmlspecialchars($message); ?>
                            </span>
                        </div>

                    <?php endif; ?>


                    <!-- Login Form -->

                    <form method="POST" action="">

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


                        <div class="mb-2">

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
                                    placeholder="Enter your password"
                                    required
                                    autocomplete="current-password"
                                >

                            </div>

                        </div>


                        <div class="text-end mb-4">

                            <a
                                href="forgot-password.php"
                                class="auth-small-link"
                            >
                                Forgot Password?
                            </a>

                        </div>


                        <button
                            type="submit"
                            class="btn btn-warning w-100 py-3 fw-semibold"
                        >
                            <i class="bi bi-box-arrow-in-right me-2"></i>
                            Login
                        </button>

                    </form>


                    <div class="auth-divider">
                        <span>OR</span>
                    </div>


                    <p class="text-center text-secondary mb-0">

                        Don't have an account?

                        <a
                            href="register.php"
                            class="fw-semibold text-warning"
                        >
                            Create an account
                        </a>

                    </p>

                </div>

            </div>

        </div>

    </main>


    <!-- Bootstrap JS -->

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    </script>

</body>

</html>