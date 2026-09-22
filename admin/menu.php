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

// Add Menu Item
if (isset($_POST['add_menu'])) {

    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $preparation_time = $_POST['preparation_time'];
    $availability = $_POST['availability'];

    if (empty($name) || empty($price)) {

        $error = "Name and price are required.";

    } elseif (!is_numeric($price) || $price < 0) {

        $error = "Please enter a valid price.";

    } else {

        $stmt = $conn->prepare(
            "INSERT INTO menu_items
            (category_id, name, description, price, availability, preparation_time)
            VALUES (?, ?, ?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "issdsi",
            $category_id,
            $name,
            $description,
            $price,
            $availability,
            $preparation_time
        );

        if ($stmt->execute()) {

            $message = "Menu item added successfully.";

        } else {

            $error = "Failed to add menu item.";
        }

        $stmt->close();
    }
}


// Delete Menu Item
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {

    $menu_id = (int) $_GET['delete'];

    $stmt = $conn->prepare(
        "DELETE FROM menu_items WHERE id = ?"
    );

    $stmt->bind_param("i", $menu_id);

    if ($stmt->execute()) {

        $message = "Menu item deleted successfully.";

    } else {

        $error = "Failed to delete menu item.";
    }

    $stmt->close();
}


// Change Availability
if (isset($_GET['availability']) && isset($_GET['id']) && is_numeric($_GET['id'])) {

    $menu_id = (int) $_GET['id'];
    $availability = $_GET['availability'];

    if ($availability === 'available' || $availability === 'unavailable') {

        $stmt = $conn->prepare(
            "UPDATE menu_items
             SET availability = ?
             WHERE id = ?"
        );

        $stmt->bind_param(
            "si",
            $availability,
            $menu_id
        );

        if ($stmt->execute()) {

            $message = "Menu availability updated.";

        } else {

            $error = "Failed to update availability.";
        }

        $stmt->close();
    }
}


// Get categories
$categories = $conn->query(
    "SELECT id, name
     FROM categories
     ORDER BY name ASC"
);


// Get menu items
$menu_items = $conn->query(
    "SELECT
        m.id,
        m.name,
        m.description,
        m.price,
        m.availability,
        m.preparation_time,
        c.name AS category_name
     FROM menu_items m
     LEFT JOIN categories c
        ON m.category_id = c.id
     ORDER BY m.id DESC"
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>RMS - Menu Management</title>

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

            <h1>Menu Management</h1>

            <span>
                Welcome,
                <?php echo htmlspecialchars($_SESSION['user_name']); ?>
            </span>

        </div>


        <!-- Messages -->

        <?php if (!empty($message)): ?>

            <div class="card">

                <p>
                    <?php echo htmlspecialchars($message); ?>
                </p>

            </div>

        <?php endif; ?>


        <?php if (!empty($error)): ?>

            <div class="card">

                <p>
                    <?php echo htmlspecialchars($error); ?>
                </p>

            </div>

        <?php endif; ?>


        <!-- Add Menu Item -->

        <div class="card">

            <h2>Add Menu Item</h2>

            <br>

            <form method="POST" action="">

                <div class="form-group">

                    <label for="name">Item Name</label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        placeholder="Enter menu item name"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="description">Description</label>

                    <textarea
                        id="description"
                        name="description"
                        placeholder="Enter description"
                    ></textarea>

                </div>


                <div class="form-group">

                    <label for="price">Price</label>

                    <input
                        type="number"
                        id="price"
                        name="price"
                        step="0.01"
                        min="0"
                        placeholder="Enter price"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="category_id">Category</label>

                    <select
                        id="category_id"
                        name="category_id"
                        required
                    >

                        <option value="">Select Category</option>

                        <?php while ($category = $categories->fetch_assoc()): ?>

                            <option value="<?php echo $category['id']; ?>">

                                <?php echo htmlspecialchars($category['name']); ?>

                            </option>

                        <?php endwhile; ?>

                    </select>

                </div>


                <div class="form-group">

                    <label for="preparation_time">
                        Preparation Time (minutes)
                    </label>

                    <input
                        type="number"
                        id="preparation_time"
                        name="preparation_time"
                        min="0"
                        value="15"
                    >

                </div>


                <div class="form-group">

                    <label for="availability">Availability</label>

                    <select
                        id="availability"
                        name="availability"
                    >

                        <option value="available">
                            Available
                        </option>

                        <option value="unavailable">
                            Unavailable
                        </option>

                    </select>

                </div>


                <button type="submit" name="add_menu">
                    Add Menu Item
                </button>

            </form>

        </div>


        <!-- Menu Items -->

        <div class="card">

            <h2>Menu Items</h2>

            <br>

            <table>

                <thead>

                    <tr>

                        <th>ID</th>

                        <th>Name</th>

                        <th>Category</th>

                        <th>Price</th>

                        <th>Preparation</th>

                        <th>Availability</th>

                        <th>Actions</th>

                    </tr>

                </thead>

                <tbody>

                    <?php if ($menu_items->num_rows > 0): ?>

                        <?php while ($item = $menu_items->fetch_assoc()): ?>

                            <tr>

                                <td>
                                    <?php echo $item['id']; ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($item['name']); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($item['category_name'] ?? 'No Category'); ?>
                                </td>

                                <td>
                                    <?php echo number_format($item['price'], 2); ?>
                                </td>

                                <td>
                                    <?php echo $item['preparation_time']; ?> min
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($item['availability']); ?>
                                </td>

                                <td>

                                    <?php if ($item['availability'] === 'available'): ?>

                                        <a
                                            href="menu.php?id=<?php echo $item['id']; ?>&availability=unavailable"
                                        >
                                            Make Unavailable
                                        </a>

                                    <?php else: ?>

                                        <a
                                            href="menu.php?id=<?php echo $item['id']; ?>&availability=available"
                                        >
                                            Make Available
                                        </a>

                                    <?php endif; ?>


                                    <br><br>


                                    <a
                                        href="menu.php?delete=<?php echo $item['id']; ?>"
                                        onclick="return confirm('Are you sure you want to delete this menu item?');"
                                    >
                                        Delete
                                    </a>

                                </td>

                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>

                            <td colspan="7">
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