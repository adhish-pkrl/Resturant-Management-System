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

// Check menu item ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: menu_items.php");
    exit;
}

$item_id = (int) $_GET['id'];

// Get menu item
$stmt = $conn->prepare(
    "SELECT id, category_id, name, description, price,
            image, availability, preparation_time
     FROM menu_items
     WHERE id = ?
     LIMIT 1"
);

$stmt->bind_param("i", $item_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    $stmt->close();
    header("Location: menu_items.php");
    exit;
}

$item = $result->fetch_assoc();
$stmt->close();

// Get categories
$categories_result = $conn->query(
    "SELECT id, name
     FROM categories
     ORDER BY name ASC"
);

// Update menu item
if (isset($_POST['update_menu_item'])) {

    $category_id = (int) $_POST['category_id'];
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = $_POST['price'];
    $availability = $_POST['availability'];
    $preparation_time = (int) $_POST['preparation_time'];

    if ($category_id <= 0 || empty($name) || empty($description)) {

        $error = "Please fill in all required fields.";

    } elseif (!is_numeric($price) || $price < 0) {

        $error = "Please enter a valid price.";

    } elseif ($preparation_time < 0) {

        $error = "Preparation time cannot be negative.";

    } else {

        // Check category exists
        $category_check = $conn->prepare(
            "SELECT id
             FROM categories
             WHERE id = ?
             LIMIT 1"
        );

        $category_check->bind_param("i", $category_id);
        $category_check->execute();

        $category_result = $category_check->get_result();

        if ($category_result->num_rows !== 1) {

            $error = "Selected category does not exist.";

        } else {

            // Check duplicate name
            $check = $conn->prepare(
                "SELECT id
                 FROM menu_items
                 WHERE name = ? AND id != ?
                 LIMIT 1"
            );

            $check->bind_param("si", $name, $item_id);
            $check->execute();

            $check_result = $check->get_result();

            if ($check_result->num_rows > 0) {

                $error = "Another menu item already has this name.";

            } else {

                $update = $conn->prepare(
                    "UPDATE menu_items
                     SET category_id = ?,
                         name = ?,
                         description = ?,
                         price = ?,
                         availability = ?,
                         preparation_time = ?
                     WHERE id = ?"
                );

                $update->bind_param(
                    "issdsii",
                    $category_id,
                    $name,
                    $description,
                    $price,
                    $availability,
                    $preparation_time,
                    $item_id
                );

                if ($update->execute()) {

                    $message = "Menu item updated successfully!";

                    // Update displayed values
                    $item['category_id'] = $category_id;
                    $item['name'] = $name;
                    $item['description'] = $description;
                    $item['price'] = $price;
                    $item['availability'] = $availability;
                    $item['preparation_time'] = $preparation_time;

                } else {

                    $error = "Failed to update menu item.";
                }

                $update->close();
            }

            $check->close();
        }

        $category_check->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>RMS - Edit Menu Item</title>

    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

<div class="dashboard">

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


    <main class="main-content">

        <div class="topbar">

            <h1>Edit Menu Item</h1>

            <span>
                Welcome,
                <?php echo htmlspecialchars($_SESSION['user_name']); ?>
            </span>

        </div>


        <div class="card">

            <h2>Update Menu Item</h2>

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

                    <label for="name">
                        Name
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="<?php echo htmlspecialchars($item['name']); ?>"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="category_id">
                        Category
                    </label>

                    <select
                        id="category_id"
                        name="category_id"
                        required
                    >

                        <option value="">
                            -- Select Category --
                        </option>

                        <?php while ($category = $categories_result->fetch_assoc()): ?>

                            <option
                                value="<?php echo $category['id']; ?>"
                                <?php
                                if ($item['category_id'] == $category['id']) {
                                    echo 'selected';
                                }
                                ?>
                            >
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>

                        <?php endwhile; ?>

                    </select>

                </div>


                <div class="form-group">

                    <label for="description">
                        Description
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        required
                    ><?php echo htmlspecialchars($item['description']); ?></textarea>

                </div>


                <div class="form-group">

                    <label for="price">
                        Price
                    </label>

                    <input
                        type="number"
                        id="price"
                        name="price"
                        step="0.01"
                        min="0"
                        value="<?php echo htmlspecialchars($item['price']); ?>"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="availability">
                        Availability
                    </label>

                    <select
                        id="availability"
                        name="availability"
                    >

                        <option
                            value="available"
                            <?php
                            if ($item['availability'] === 'available') {
                                echo 'selected';
                            }
                            ?>
                        >
                            Available
                        </option>

                        <option
                            value="unavailable"
                            <?php
                            if ($item['availability'] === 'unavailable') {
                                echo 'selected';
                            }
                            ?>
                        >
                            Unavailable
                        </option>

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
                        value="<?php echo htmlspecialchars($item['preparation_time']); ?>"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>
                        Image
                    </label>

                    <p>
                        Current image:
                        <?php echo htmlspecialchars($item['image'] ?: 'None'); ?>
                    </p>

                    <small>
                        Image upload will be added later.
                    </small>

                </div>


                <button type="submit" name="update_menu_item">
                    Save Changes
                </button>

                <a href="menu_items.php">
                    Cancel
                </a>

            </form>

        </div>

    </main>
    
</div>

</body>

</html>