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


// Get all orders
$orders = $conn->query(
    "SELECT
        o.id,
        o.order_type,
        o.total_amount,
        o.order_status,
        o.payment_status,
        o.notes,
        o.created_at,
        u.name AS waiter_name,
        t.table_number
     FROM orders o

     LEFT JOIN users u
        ON o.waiter_id = u.id

     LEFT JOIN restaurant_tables t
        ON o.table_id = t.id

     ORDER BY o.id DESC"
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>RMS - Orders</title>

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

            <h1>Order Management</h1>

            <span>
                Welcome,
                <?php echo htmlspecialchars($_SESSION['user_name']); ?>
            </span>

        </div>


        <!-- Orders -->

        <div class="card">

            <h2>All Orders</h2>

            <br>


            <table>

                <thead>

                    <tr>

                        <th>ID</th>

                        <th>Waiter</th>

                        <th>Table</th>

                        <th>Order Type</th>

                        <th>Total</th>

                        <th>Order Status</th>

                        <th>Payment Status</th>

                        <th>Notes</th>

                        <th>Created At</th>

                    </tr>

                </thead>


                <tbody>

                    <?php if ($orders->num_rows > 0): ?>

                        <?php while ($order = $orders->fetch_assoc()): ?>

                            <tr>

                                <td>
                                    <?php echo $order['id']; ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        $order['waiter_name'] ?? 'Unknown'
                                    );
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        $order['table_number'] ?? 'N/A'
                                    );
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        ucfirst($order['order_type'])
                                    );
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo number_format(
                                        $order['total_amount'],
                                        2
                                    );
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        ucfirst($order['order_status'])
                                    );
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        ucfirst($order['payment_status'])
                                    );
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        $order['notes'] ?? ''
                                    );
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        $order['created_at']
                                    );
                                    ?>
                                </td>

                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>

                            <td colspan="9">
                                No orders found.
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