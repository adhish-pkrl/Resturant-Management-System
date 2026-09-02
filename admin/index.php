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

    <p>👥 User Management</p>
    <p>🍔 Menu Management</p>
    <p>🗂️ Category Management</p>
    <p>🪑 Table Management</p>
    <p>📦 Order Management</p>
    <p>💳 Payments</p>
    <p>📅 Reservations</p>
    <p>📊 Reports</p>

    <br>

    <a href="../auth/logout.php">Logout</a>

</body>

</html>