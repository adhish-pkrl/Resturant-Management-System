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

// Add table
if (isset($_POST['add_table'])) {

    $table_number = trim($_POST['table_number']);
    $capacity = (int) $_POST['capacity'];
    $status = $_POST['status'];

    if (empty($table_number)) {

        $error = "Please enter a table number.";

    } elseif ($capacity <= 0) {

        $error = "Capacity must be greater than 0.";

    } else {

        // Check duplicate table number
        $check = $conn->prepare(
            "SELECT id
             FROM restaurant_tables
             WHERE table_number = ?
             LIMIT 1"
        );

        $check->bind_param("s", $table_number);
        $check->execute();

        $result = $check->get_result();

        if ($result->num_rows > 0) {

            $error = "This table number already exists.";

        } else {

            $stmt = $conn->prepare(
                "INSERT INTO restaurant_tables
                (table_number, capacity, status)
                VALUES (?, ?, ?)"
            );

            $stmt->bind_param(
                "sis",
                $table_number,
                $capacity,
                $status
            );

            if ($stmt->execute()) {

                $message = "Table added successfully!";

            } else {

                $error = "Failed to add table.";
            }

            $stmt->close();
        }

        $check->close();
    }
}


// Delete table
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {

    $table_id = (int) $_GET['delete'];

    $stmt = $conn->prepare(
        "DELETE FROM restaurant_tables
         WHERE id = ?"
    );

    $stmt->bind_param("i", $table_id);

    if ($stmt->execute()) {

        $message = "Table deleted successfully.";

    } else {

        $error = "Failed to delete table.";
    }

    $stmt->close();
}


// Get all tables
$tables_result = $conn->query(
    "SELECT id, table_number, capacity, status, created_at
     FROM restaurant_tables
     ORDER BY id ASC"
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>RMS - Tables</title>

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

            <h1>Restaurant Tables</h1>

            <span>
                Welcome,
                <?php echo htmlspecialchars($_SESSION['user_name']); ?>
            </span>

        </div>


        <div class="card">

            <h2>Add New Table</h2>

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
                        placeholder="Example: T1"
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
                        placeholder="Example: 4"
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

                        <option value="available">
                            Available
                        </option>

                        <option value="occupied">
                            Occupied
                        </option>

                        <option value="reserved">
                            Reserved
                        </option>

                    </select>

                </div>


                <button
                    type="submit"
                    name="add_table"
                >
                    Add Table
                </button>

            </form>

        </div>


        <br>


        <div class="card">

            <h2>All Restaurant Tables</h2>

            <br>


            <table>

                <thead>

                    <tr>

                        <th>ID</th>

                        <th>Table Number</th>

                        <th>Capacity</th>

                        <th>Status</th>

                        <th>Created At</th>

                        <th>Action</th>

                    </tr>

                </thead>


                <tbody>

                    <?php if ($tables_result->num_rows > 0): ?>

                        <?php while (
                            $table = $tables_result->fetch_assoc()
                        ): ?>

                            <tr>

                                <td>
                                    <?php echo $table['id']; ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        $table['table_number']
                                    );
                                    ?>
                                </td>

                                <td>
                                    <?php echo $table['capacity']; ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        ucfirst($table['status'])
                                    );
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        $table['created_at']
                                    );
                                    ?>
                                </td>

                                <td>

                                    <a href="edit_table.php?id=<?php echo $table['id']; ?>">
                                        Edit
                                    </a>

                                   |

                                    <a
                                        href="tables.php?delete=<?php echo $table['id']; ?>"
                                        onclick="return confirm('Are you sure you want to delete this table?');"
                                    >
                                        Delete
                                    </a>

                                </td>

                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>

                            <td colspan="6">
                                No restaurant tables found.
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