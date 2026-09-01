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

// Check category ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: categories.php");
    exit;
}

$category_id = (int) $_GET['id'];

// Get existing category
$stmt = $conn->prepare(
    "SELECT id, name
     FROM categories
     WHERE id = ?
     LIMIT 1"
);

$stmt->bind_param("i", $category_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    $stmt->close();
    header("Location: categories.php");
    exit;
}

$category = $result->fetch_assoc();
$stmt->close();


// Update category
if (isset($_POST['update_category'])) {

    $name = trim($_POST['name']);

    if (empty($name)) {

        $error = "Category name is required.";

    } else {

        // Check for duplicate category name
        $check = $conn->prepare(
            "SELECT id
             FROM categories
             WHERE name = ? AND id != ?
             LIMIT 1"
        );

        $check->bind_param("si", $name, $category_id);
        $check->execute();

        $check_result = $check->get_result();

        if ($check_result->num_rows > 0) {

            $error = "This category already exists.";

        } else {

            $update = $conn->prepare(
                "UPDATE categories
                 SET name = ?
                 WHERE id = ?"
            );

            $update->bind_param(
                "si",
                $name,
                $category_id
            );

            if ($update->execute()) {

                $message = "Category updated successfully!";

                $category['name'] = $name;

            } else {

                $error = "Failed to update category.";
            }

            $update->close();
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

    <title>RMS - Edit Category</title>

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

            <h1>Edit Category</h1>

            <span>
                Welcome,
                <?php echo htmlspecialchars($_SESSION['user_name']); ?>
            </span>

        </div>


        <div class="card">

            <h2>Update Category</h2>

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
                        Category Name
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="<?php echo htmlspecialchars($category['name']); ?>"
                        required
                    >

                </div>


                <button type="submit" name="update_category">
                    Save Changes
                </button>

                <a href="categories.php">
                    Cancel
                </a>

            </form>

        </div>

    </main>

</div>

</body>

</html>