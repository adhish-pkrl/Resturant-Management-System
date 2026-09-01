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

// Get menu items with their category names
$result = $conn->query(
    "SELECT
        menu_items.id,
        menu_items.name,
        menu_items.description,
        menu_items.price,
        menu_items.image,
        menu_items.availability,
        menu_items.preparation_time,
        categories.name AS category_name
     FROM menu_items
     INNER JOIN categories
        ON menu_items.category_id = categories.id
     ORDER BY menu_items.id DESC"
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>RMS - Menu Items</title>

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
                <a href="menu_items.php">Menu Items</a>
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

            <h1>Menu Items</h1>

            <span>
                Welcome,
                <?php echo htmlspecialchars($_SESSION['user_name']); ?>
            </span>

        </div>


        <div class="card">

            <h2>All Menu Items</h2>

            <br>

            <a href="add_menu_item.php">
                <button type="button">+ Add Menu Item</button>
            </a>

            <br><br>

            <table>

                <thead>

                    <tr>

                        <th>ID</th>

                        <th>Name</th>

                        <th>Category</th>

                        <th>Description</th>

                        <th>Price</th>

                        <th>Availability</th>

                        <th>Preparation Time</th>

                        <th>Actions</th>

                    </tr>

                </thead>

                <tbody>

                    <?php if ($result->num_rows > 0): ?>

                        <?php while ($item = $result->fetch_assoc()): ?>

                            <tr>

                                <td>
                                    <?php echo $item['id']; ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($item['name']); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($item['category_name']); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($item['description']); ?>
                                </td>

                                <td>
                                    $<?php echo number_format($item['price'], 2); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($item['availability']); ?>
                                </td>

                                <td>
                                    <?php echo $item['preparation_time']; ?> min
                                </td>

                                <td>

                                    <a href="edit_menu_item.php?id=<?php echo $item['id']; ?>">
                                        Edit
                                    </a>

                                    |

                                    <a
                                        href="delete_menu_item.php?id=<?php echo $item['id']; ?>"
                                        onclick="return confirm('Are you sure you want to delete this menu item?');"
                                    >
                                        Delete
                                    </a>

                                </td>

                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>

                            <td colspan="8">
                                No menu items found.
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