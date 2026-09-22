<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SESSION['user_role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

require_once "../config/database.php";

$message = "";
$error = "";

if (isset($_POST['add_user'])) {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];
    $status = $_POST['status'];

    if (empty($name) || empty($email) || empty($password)) {

        $error = "Please fill in all required fields.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    } elseif (strlen($password) < 6) {

        $error = "Password must be at least 6 characters.";

    } else {

        // Check whether email already exists
        $check = $conn->prepare(
            "SELECT id FROM users WHERE email = ? LIMIT 1"
        );

        $check->bind_param("s", $email);
        $check->execute();

        $check_result = $check->get_result();

        if ($check_result->num_rows > 0) {

            $error = "A user with this email already exists.";

        } else {

            // Hash the password
            $hashed_password = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            // Insert new user
            $stmt = $conn->prepare(
                "INSERT INTO users
                (name, email, password, role, status)
                VALUES (?, ?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "sssss",
                $name,
                $email,
                $hashed_password,
                $role,
                $status
            );

            if ($stmt->execute()) {

                $message = "User added successfully!";

                // Clear form values
                $name = "";
                $email = "";

            } else {

                $error = "Failed to add user.";
            }

            $stmt->close();
        }

        $check->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>RMS - Add User</title>

    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

<div class="dashboard">

    <!-- Sidebar -->

    <aside class="sidebar">

        <h2>RMS</h2>
<ul>

    <li>
        <a href="dashboard.php">Dashboard</a>
    </li>

    <li>
        <a href="users.php">Users</a>
    </li>

    <li>
        <a href="menu.php">Menu Items</a>
    </li>

    <li>
        <a href="tables.php">Tables</a>
    </li>

    <li>
        <a href="orders.php">Orders</a>
    </li>

    <li>
        <a href="payments.php">Payments</a>
    </li>

    <li>
        <a href="reports.php">Reports</a>
    </li>

    <li>
        <a href="../auth/logout.php">Logout</a>
    </li>

</ul>
    </aside>


    <!-- Main Content -->

    <main class="main-content">

        <div class="topbar">

            <h1>Add User</h1>

            <span>
                Welcome,
                <?php echo htmlspecialchars($_SESSION['user_name']); ?>
            </span>

        </div>


        <div class="card">

            <h2>Create New User</h2>

            <br>

            <?php if (!empty($message)): ?>

                <p>
                    <?php echo htmlspecialchars($message); ?>
                </p>

            <?php endif; ?>


            <?php if (!empty($error)): ?>

                <p>
                    <?php echo htmlspecialchars($error); ?>
                </p>

            <?php endif; ?>


            <form method="POST" action="">

                <div class="form-group">

                    <label for="name">Name</label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        placeholder="Enter full name"
                        value="<?php echo htmlspecialchars($name ?? ''); ?>"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="email">Email</label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Enter email"
                        value="<?php echo htmlspecialchars($email ?? ''); ?>"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="password">Password</label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Enter password"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="role">Role</label>

                    <select id="role" name="role" required>

                        <option value="admin">Admin</option>

                        <option value="waiter">Waiter</option>

                        <option value="kitchen">Kitchen</option>

                        <option value="cashier">Cashier</option>

                    </select>

                </div>


                <div class="form-group">

                    <label for="status">Status</label>

                    <select id="status" name="status">

                        <option value="active">Active</option>

                        <option value="inactive">Inactive</option>

                    </select>

                </div>


                <button type="submit" name="add_user">
                    Add User
                </button>

                <a href="users.php">
                    Cancel
                </a>

            </form>

        </div>

    </main>

</div>

</body>

</html>