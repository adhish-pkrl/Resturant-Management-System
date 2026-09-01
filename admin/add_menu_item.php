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

// Get categories
$categories_result = $conn->query(
    "SELECT id, name
     FROM categories
     ORDER BY name ASC"
);

if (!$categories_result) {
    die("Category query failed: " . $conn->error);
}


// Add menu item
if (isset($_POST['add_menu_item'])) {

    $category_id = (int) $_POST['category_id'];
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = $_POST['price'];
    $availability = $_POST['availability'];
    $preparation_time = (int) $_POST['preparation_time'];


    // Basic validation
    if ($category_id <= 0 || empty($name) || empty($description)) {

        $error = "Please fill in all required fields.";

    } elseif (!is_numeric($price) || $price < 0) {

        $error = "Please enter a valid price.";

    } elseif ($preparation_time < 0) {

        $error = "Preparation time cannot be negative.";

    } elseif (
        !isset($_FILES['image']) ||
        $_FILES['image']['error'] !== UPLOAD_ERR_OK
    ) {

        $error = "Please select an image.";

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

            // Check duplicate menu item name
            $check = $conn->prepare(
                "SELECT id
                 FROM menu_items
                 WHERE name = ?
                 LIMIT 1"
            );

            $check->bind_param("s", $name);
            $check->execute();

            $check_result = $check->get_result();


            if ($check_result->num_rows > 0) {

                $error = "A menu item with this name already exists.";

            } else {

                // Allowed image types
                $allowed_types = [
                    'image/jpeg' => 'jpg',
                    'image/png'  => 'png',
                    'image/webp' => 'webp'
                ];

                $file_type = mime_content_type(
                    $_FILES['image']['tmp_name']
                );


                if (!isset($allowed_types[$file_type])) {

                    $error = "Only JPG, PNG, and WEBP images are allowed.";

                } elseif (
                    $_FILES['image']['size'] > 5 * 1024 * 1024
                ) {

                    $error = "Image must be smaller than 5MB.";

                } else {

                    // Create unique image filename
                    $extension = $allowed_types[$file_type];

                    $image = uniqid('menu_', true) . '.' . $extension;

                    $upload_path = "../uploads/menu_items/" . $image;


                    // Upload image
                    if (!move_uploaded_file(
                        $_FILES['image']['tmp_name'],
                        $upload_path
                    )) {

                        $error = "Failed to upload image.";

                    } else {

                        // Insert menu item
                        $stmt = $conn->prepare(
                            "INSERT INTO menu_items
                            (
                                category_id,
                                name,
                                description,
                                price,
                                image,
                                availability,
                                preparation_time
                            )
                            VALUES (?, ?, ?, ?, ?, ?, ?)"
                        );


                        $stmt->bind_param(
                            "issdssi",
                            $category_id,
                            $name,
                            $description,
                            $price,
                            $image,
                            $availability,
                            $preparation_time
                        );


                        if ($stmt->execute()) {

                            $message = "Menu item added successfully!";

                            // Clear form
                            $name = "";
                            $description = "";
                            $price = "";
                            $preparation_time = "";

                        } else {

                            // Delete uploaded image if database insert fails
                            if (file_exists($upload_path)) {
                                unlink($upload_path);
                            }

                            $error = "Failed to add menu item.";
                        }

                        $stmt->close();
                    }
                }
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

    <title>RMS - Add Menu Item</title>

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

            <h1>Add Menu Item</h1>

            <span>
                Welcome,
                <?php echo htmlspecialchars($_SESSION['user_name']); ?>
            </span>

        </div>


        <div class="card">

            <h2>Create New Menu Item</h2>

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


            <form
                method="POST"
                action=""
                enctype="multipart/form-data"
            >

                <!-- Name -->

                <div class="form-group">

                    <label for="name">
                        Name
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="<?php echo htmlspecialchars($name ?? ''); ?>"
                        required
                    >

                </div>


                <!-- Category -->

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


                        <?php while (
                            $category = $categories_result->fetch_assoc()
                        ): ?>

                            <option
                                value="<?php echo $category['id']; ?>"
                            >

                                <?php
                                echo htmlspecialchars(
                                    $category['name']
                                );
                                ?>

                            </option>

                        <?php endwhile; ?>

                    </select>

                </div>


                <!-- Description -->

                <div class="form-group">

                    <label for="description">
                        Description
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        required
                    ><?php echo htmlspecialchars($description ?? ''); ?></textarea>

                </div>


                <!-- Price -->

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
                        value="<?php echo htmlspecialchars($price ?? ''); ?>"
                        required
                    >

                </div>


                <!-- Availability -->

                <div class="form-group">

                    <label for="availability">
                        Availability
                    </label>

                    <select
                        id="availability"
                        name="availability"
                        required
                    >

                        <option value="available">
                            Available
                        </option>

                        <option value="unavailable">
                            Unavailable
                        </option>

                    </select>

                </div>


                <!-- Preparation Time -->

                <div class="form-group">

                    <label for="preparation_time">
                        Preparation Time (minutes)
                    </label>

                    <input
                        type="number"
                        id="preparation_time"
                        name="preparation_time"
                        min="0"
                        value="<?php echo htmlspecialchars($preparation_time ?? ''); ?>"
                        required
                    >

                </div>


                <!-- Image -->

                <div class="form-group">

                    <label for="image">
                        Image
                    </label>

                    <input
                        type="file"
                        id="image"
                        name="image"
                        accept="image/jpeg,image/png,image/webp"
                        required
                    >

                    <small>
                        Allowed formats: JPG, PNG, WEBP.
                        Maximum size: 5MB.
                    </small>

                </div>


                <!-- Buttons -->

                <button
                    type="submit"
                    name="add_menu_item"
                >
                    Add Menu Item
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