<?php

session_start();

require_once "../config/database.php";

$error = "";

if (isset($_POST['login'])) {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {

        $error = "Please enter your email and password.";

    } else {

        $stmt = $conn->prepare(
            "SELECT id, name, email, password, role, status
             FROM users
             WHERE email = ?
             LIMIT 1"
        );

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $user = $result->fetch_assoc();

            if ($user['status'] !== 'active') {

                $error = "Your account is not active.";

            } elseif (password_verify($password, $user['password'])) {

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];

                if ($user['role'] === 'admin') {

                    header("Location: ../admin/dashboard.php");
                    exit;

                } elseif ($user['role'] === 'staff') {

                    header("Location: ../staff/dashboard.php");
                    exit;

                } elseif ($user['role'] === 'customer') {

                    header("Location: ../customer/dashboard.php");
                    exit;

                } else {

                    $error = "Invalid user role.";
                }

            } else {

                $error = "Invalid email or password.";
            }

        } else {

            $error = "Invalid email or password.";
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

    <title>RMS - Login</title>

    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

    <div class="login-container">

        <h1>Restaurant Management System</h1>

        <h2>Login</h2>

        <?php if (!empty($error)): ?>

            <p>
                <?php echo htmlspecialchars($error); ?>
            </p>

        <?php endif; ?>

        <form method="POST" action="">

            <div class="form-group">

                <label for="email">Email</label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Enter your email"
                    required
                >

            </div>

            <div class="form-group">

                <label for="password">Password</label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter your password"
                    required
                >

            </div>

                <?php
                    echo "<h1>RMS Login Page</h1>";
                    echo "<p>Login system will be built here.</p>";
                ?>
            <button type="submit" name="login">
                Login
            </button>

        </form>

    </div>

</body>

</html>