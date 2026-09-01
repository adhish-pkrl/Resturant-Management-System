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

// Get all categories
$result = $conn->query(
    "SELECT id, name
     FROM categories
     ORDER BY id DESC"
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>RMS - Categories</title>

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
                <a href="categories.php">Categories</a>
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

            <h1>Categories</h1>

            <span>
                Welcome,
                <?php echo htmlspecialchars($_SESSION['user_name']); ?>
            </span>

        </div>


        <div class="card">

            <h2>All Categories</h2>

            <br>

            <a href="add_category.php">
                <button type="button">+ Add Category</button>
            </a>

            <br><br>

            <table>

                <thead>

                    <tr>

                        <th>ID</th>

                        <th>Category Name</th>

                        <th>Actions</th>

                    </tr>

                </thead>

                <tbody>

                    <?php if ($result->num_rows > 0): ?>

                        <?php while ($category = $result->fetch_assoc()): ?>

                            <tr>

                                <td>
                                    <?php echo $category['id']; ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </td>

                                <td>

                                    <a href="edit_category.php?id=<?php echo $category['id']; ?>">
                                        Edit
                                    </a>

                                    |

                                    <a
                                        href="delete_category.php?id=<?php echo $category['id']; ?>"
                                        onclick="return confirm('Are you sure you want to delete this category?');"
                                    >
                                        Delete
                                    </a>

                                </td>

                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>

                            <td colspan="3">
                                No categories found.
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