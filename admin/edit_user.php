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

$error = "";
$message = "";

// Check whether a user ID was provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: users.php");
    exit;
}

$user_id = (int) $_GET['id'];

// Get existing user
$stmt = $conn->prepare(
    "SELECT id, name, email, role, status
     FROM users
     WHERE id = ?
     LIMIT 1"
);

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    $stmt->close();
    header("Location: users.php");
    exit;
}

$user = $result->fetch_assoc();
$stmt->close();


// Update user
if (isset($_POST['update_user'])) {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $status = $_POST['status'];
    $password = $_POST['password'];

    if (empty($name) || empty($email)) {

        $error = "Name and email are required.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    } else {

        // Check whether another user already has this email
        $check = $conn->prepare(
            "SELECT id
             FROM users
             WHERE email = ? AND id != ?
             LIMIT 1"
        );

        $check->bind_param("si", $email, $user_id);
        $check->execute();

        $check_result = $check->get_result();

        if ($check_result->num_rows > 0) {

            $error = "Another user already has this email address.";

        } else {

            if (!empty($password)) {

                if (strlen($password) < 6) {

                    $error = "Password must be at least 6 characters.";

                } else {

                    $hashed_password = password_hash(
                        $password,
                        PASSWORD_DEFAULT
                    );

                    $update = $conn->prepare(
                        "UPDATE users
                         SET name = ?, email = ?, password = ?, role = ?, status = ?
                         WHERE id = ?"
                    );

                    $update->bind_param(
                        "sssssi",
                        $name,
                        $email,
                        $hashed_password,
                        $role,
                        $status,
                        $user_id
                    );

                    if ($update->execute()) {

                        $message = "User updated successfully!";

                    } else {

                        $error = "Failed to update user.";
                    }

                    $update->close();
                }

            } else {

                $update = $conn->prepare(
                    "UPDATE users
                     SET name = ?, email = ?, role = ?, status = ?
                     WHERE id = ?"
                );

                $update->bind_param(
                    "ssssi",
                    $name,
                    $email,
                    $role,
                    $status,
                    $user_id
                );

                if ($update->execute()) {

                    $message = "User updated successfully!";

                } else {

                    $error = "Failed to update user.";
                }

                $update->close();
            }

            // Update displayed values
            if (empty($error)) {

                $user['name'] = $name;
                $user['email'] = $email;
                $user['role'] = $role;
                $user['status'] = $status;
            }
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

    <title>RMS - Edit User</title>

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
                <a href="#">Categories</a>
            </li>

            <li>
                <a href="#">Menu Items</a>
            </li>

            <li>
                <a href="#">Tables</a>
            </li>

            <li>
                <a href="#">Orders</a>
            </li>

            <li>
                <a href="#">Reservations</a>
            </li>

            <li>
                <a href="#">Payments</a>
            </li>

            <li>
                <a href="../auth/logout.php">Logout</a>
            </li>

        </ul>

    </aside>


    <!-- Main Content -->

    <main class="main-content">

        <div class="topbar">

            <h1>Edit User</h1>

            <span>
                Welcome,
                <?php echo htmlspecialchars($_SESSION['user_name']); ?>
            </span>

        </div>


        <div class="card">

            <h2>Update User Information</h2>

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
                        value="<?php echo htmlspecialchars($user['name']); ?>"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="email">Email</label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?php echo htmlspecialchars($user['email']); ?>"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="password">
                        New Password
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Leave blank to keep current password"
                    >

                </div>


                <div class="form-group">

                    <label for="role">Role</label>

                    <select id="role" name="role">

                        <option value="customer"
                            <?php if ($user['role'] === 'customer') echo 'selected'; ?>>
                            Customer
                        </option>

                        <option value="staff"
                            <?php if ($user['role'] === 'staff') echo 'selected'; ?>>
                            Staff
                        </option>

                        <option value="admin"
                            <?php if ($user['role'] === 'admin') echo 'selected'; ?>>
                            Admin
                        </option>

                    </select>

                </div>


                <div class="form-group">

                    <label for="status">Status</label>

                    <select id="status" name="status">

                        <option value="active"
                            <?php if ($user['status'] === 'active') echo 'selected'; ?>>
                            Active
                        </option>

                        <option value="inactive"
                            <?php if ($user['status'] === 'inactive') echo 'selected'; ?>>
                            Inactive
                        </option>

                    </select>

                </div>


                <button type="submit" name="update_user">
                    Save Changes
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