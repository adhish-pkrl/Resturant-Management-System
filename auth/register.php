<?php

session_start();

include '../config/database.php';

$message = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {

        $message = "Please fill in all fields.";

    } elseif ($password !== $confirm_password) {

        $message = "Passwords do not match.";

    } elseif (strlen($password) < 6) {

        $message = "Password must be at least 6 characters.";

    } else {

        // Check if email already exists
        $check_sql = "SELECT id FROM users WHERE email = ? LIMIT 1";

        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();

        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {

            $message = "An account with this email already exists.";

        } else {

            // Securely hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // New users are customers by default
            $role = "customer";
            $status = "active";

            $sql = "INSERT INTO users 
                    (name, email, password, role, status) 
                    VALUES (?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);

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

        $check_stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Register - Restaurant Management System</title>

    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

    <div class="login-container">

        <h1>Restaurant Management System</h1>

        <h2>Create Account</h2>

        <?php if (!empty($message)): ?>

            <p class="error-message">
                <?php echo htmlspecialchars($message); ?>
            </p>

        <?php endif; ?>

        <?php if (!empty($success)): ?>

            <p style="color: green;">
                <?php echo htmlspecialchars($success); ?>
            </p>

        <?php endif; ?>

        <form method="POST" action="">

            <label for="name">Full Name</label>

            <input
                type="text"
                id="name"
                name="name"
                placeholder="Enter your full name"
                required
            >

            <label for="email">Email</label>

            <input
                type="email"
                id="email"
                name="email"
                placeholder="Enter your email"
                required
            >

            <label for="password">Password</label>

            <input
                type="password"
                id="password"
                name="password"
                placeholder="Enter password"
                required
            >

            <label for="confirm_password">Confirm Password</label>

            <input
                type="password"
                id="confirm_password"
                name="confirm_password"
                placeholder="Confirm password"
                required
            >

            <button type="submit">Register</button>

        </form>

        <p>
            Already have an account?
            <a href="login.php">Login</a>
        </p>

    </div>

</body>

</html>