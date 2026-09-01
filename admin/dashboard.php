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

// Get total users
$user_count_result = $conn->query("SELECT COUNT(*) AS total FROM users");
$user_count = $user_count_result->fetch_assoc()['total'];

// Get total menu items
$menu_count_result = $conn->query("SELECT COUNT(*) AS total FROM menu_items");
$menu_count = $menu_count_result->fetch_assoc()['total'];

// Get total orders
$order_count_result = $conn->query("SELECT COUNT(*) AS total FROM orders");
$order_count = $order_count_result->fetch_assoc()['total'];

// Get total reservations
$reservation_count_result = $conn->query("SELECT COUNT(*) AS total FROM reservations");
$reservation_count = $reservation_count_result->fetch_assoc()['total'];

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>RMS - Admin Dashboard</title>

    <link rel="stylesheet" href="/restaurant-management-system/assets/css/style.css">

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
                <a href="#">Users</a>
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

            <h1>Dashboard</h1>

            <span>
                Welcome,
                <?php echo htmlspecialchars($_SESSION['user_name']); ?>
            </span>

        </div>


        <!-- Dashboard Cards -->

        <div class="cards">

            <div class="card">

                <h3>Users</h3>

                <p><?php echo $user_count; ?></p>

            </div>


            <div class="card">

                <h3>Menu Items</h3>

                <p><?php echo $order_count;?></p>

            </div>


            <div class="card">

                <h3>Orders</h3>

                <p>0</p>

            </div>


            <div class="card">

                <h3>Reservations</h3>

                <p><?php echo $reservation_count;?></p>

            </div>

        </div>

    </main>

</div>

</body>

</html>