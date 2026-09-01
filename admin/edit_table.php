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

// Check table ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: tables.php");
    exit;
}

$table_id = (int) $_GET['id'];

// Get table
$stmt = $conn->prepare(
    "SELECT id, table_number, capacity, status
     FROM restaurant_tables
     WHERE id = ?
     LIMIT 1"
);

$stmt->bind_param("i", $table_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    $stmt->close();
    header("Location: tables.php");
    exit;
}

$table = $result->fetch_assoc();
$stmt->close();


// Update table
if (isset($_POST['update_table'])) {

    $table_number = trim($_POST['table_number']);
    $capacity = (int) $_POST['capacity'];
    $status = $_POST['status'];

    if (empty($table_number)) {

        $error = "Please enter a table number.";

    } elseif ($capacity <= 0) {

        $error = "Capacity must be greater than 0.";

    } elseif (!in_array($status, ['available', 'occupied', 'reserved'])) {

        $error = "Invalid table status.";

    } else {

        // Check duplicate table number
        $check = $conn->prepare(
            "SELECT id
             FROM restaurant_tables
             WHERE table_number = ?
             AND id != ?
             LIMIT 1"
        );

        $check->bind_param(
            "si",
            $table_number,
            $table_id
        );

        $check->execute();

        $check_result = $check->get_result();

        if ($check_result->num_rows > 0) {

            $error = "Another table already uses this table number.";

        } else {

            $update = $conn->prepare(
                "UPDATE restaurant_tables
                 SET table_number = ?,
                     capacity = ?,
                     status = ?
                 WHERE id = ?"
            );

            $update->bind_param(
                "sisi",
                $table_number,
                $capacity,
                $status,
                $table_id
            );

            if ($update->execute()) {

                $message = "Table updated successfully!";

                // Update displayed values
                $table['table_number'] = $table_number;
                $table['capacity'] = $capacity;
                $table['status'] = $status;

            } else {

                $error = "Failed to update table.";
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

    <title>RMS - Edit Table</title>

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
                <a href="tables.php">Tables</a>
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

            <h1>Edit Table</h1>

            <span>
                Welcome,
                <?php echo htmlspecialchars($_SESSION['user_name']); ?>
            </span>

        </div>


        <div class="card">

            <h2>Update Restaurant Table</h2>

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

                    <label for="table_number">
                        Table Number
                    </label>

                    <input
                        type="text"
                        id="table_number"
                        name="table_number"
                        value="<?php echo htmlspecialchars($table['table_number']); ?>"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="capacity">
                        Capacity
                    </label>

                    <input
                        type="number"
                        id="capacity"
                        name="capacity"
                        min="1"
                        value="<?php echo htmlspecialchars($table['capacity']); ?>"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="status">
                        Status
                    </label>

                    <select
                        id="status"
                        name="status"
                        required
                    >

                        <option
                            value="available"
                            <?php
                            if ($table['status'] === 'available') {
                                echo 'selected';
                            }
                            ?>
                        >
                            Available
                        </option>

                        <option
                            value="occupied"
                            <?php
                            if ($table['status'] === 'occupied') {
                                echo 'selected';
                            }
                            ?>
                        >
                            Occupied
                        </option>

                        <option
                            value="reserved"
                            <?php
                            if ($table['status'] === 'reserved') {
                                echo 'selected';
                            }
                            ?>
                        >
                            Reserved
                        </option>

                    </select>

                </div>


                <button
                    type="submit"
                    name="update_table"
                >
                    Save Changes
                </button>

                <a href="tables.php">
                    Cancel
                </a>

            </form>

        </div>

    </main>

</div>

</body>

</html>