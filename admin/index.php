<?php

session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["user_role"] !== "admin") {
    header("Location: ../auth/login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Dashboard - RMS</title>

    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

    <h1>Restaurant Management System</h1>

    <h2>Admin Dashboard</h2>

    <p>
        Welcome,
        <?php echo htmlspecialchars($_SESSION["user_name"]); ?>!
    </p>

    <hr>

    <h3>Admin Menu</h3>

<h2>Admin Menu</h2>

<p><a href="users.php">👥 User Management</a></p>

<p><a href="menu.php">🍔 Menu Management</a></p>

<p><a href="tables.php">🪑 Table Management</a></p>

<p><a href="orders.php">📦 Order Management</a></p>

<p><a href="payments.php">💳 Payments</a></p>

<p><a href="reports.php">📊 Reports</a></p>

<p>
    <a href="../auth/logout.php">Logout</a>
</p>
    <br>

    <a href="../auth/logout.php">Logout</a>

</body>

</html>