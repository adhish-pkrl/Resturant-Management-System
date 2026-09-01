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

$result = $conn->query(
    "SELECT id, name, email, role, status, created_at
     FROM users
     ORDER BY id DESC"
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>RMS - Users</title>

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

            <h1>Users</h1>

            <span>
                Welcome,
                <?php echo htmlspecialchars($_SESSION['user_name']); ?>
            </span>

        </div>


        <!-- Users Table -->

        <div class="card">

            <h2>All Users</h2>




        #adding button to users page
            <br>

<a href="add_user.php">
    <button type="button">+ Add User</button>
</a>
<br>



<br>

            <br>

            <table>

                <thead>

                    <tr>

                        <th>ID</th>

                        <th>Name</th>

                        <th>Email</th>

                        <th>Role</th>

                        <th>Status</th>

                        <th>Created At</th>
                        <th>Actions</th>

                    </tr>

                </thead>

                <tbody>

                    <?php if ($result->num_rows > 0): ?>

                        <?php while ($user = $result->fetch_assoc()): ?>

                            <tr>

                                <td>
                                    <?php echo $user['id']; ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($user['name']); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($user['email']); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($user['role']); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($user['status']); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($user['created_at']); ?>    
                                </td>
                                <!-- <td>
                                    <a href ="edit_user.php?id=<?php echo $user['id']; ?>">
                                    Edit
                                    </a>
                                </td> -->
                                <!-- to add delete button -->
                                 <td>

    <a href="edit_user.php?id=<?php echo $user['id']; ?>">
        Edit
    </a>

    <?php if ($user['id'] != $_SESSION['user_id']): ?>

        
        
        <a
            href="delete_user.php?id=<?php echo $user['id']; ?>"
            onclick="return confirm('Are you sure you want to delete this user?');"
        >
            Delete
        </a>

    <?php endif; ?>

</td>






                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>

                            <td colspan="7">
                                No users found.
                            </td>

                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </main>

</div>

</body>

</html>