<?php

session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["user_role"] != "customer") {
    header("Location: ../auth/login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Customer Dashboard</title>

    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

    <h1>Welcome, <?php echo htmlspecialchars($_SESSION["user_name"]); ?>! 🎉</h1>

    <h2>Customer Dashboard</h2>

    <p>You are successfully logged in as a customer.</p>

    <hr>

    <h3>Customer Menu</h3>

    <p>🍽️ View Menu</p>
    <p>🛒 Place Order</p>
    <p>📋 My Orders</p>
    <p>📅 Make Reservation</p>
    <p>💳 Payments</p>

    <br>

    <a href="../auth/logout.php">Logout</a>

</body>

</html>