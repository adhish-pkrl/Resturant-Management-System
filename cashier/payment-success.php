<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SESSION["user_role"] !== "cashier" && $_SESSION["user_role"] !== "admin") {
    header("Location: ../auth/login.php");
    exit();
}

$order_id = filter_input(
    INPUT_GET,
    "order_id",
    FILTER_VALIDATE_INT
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Payment Successful</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

</head>

<body class="bg-light">

<div class="container">

    <div
        class="card shadow-sm mx-auto mt-5"
        style="max-width: 600px;"
    >

        <div class="card-body text-center p-5">

            <h1 class="text-success">
                Payment Successful
            </h1>

            <p class="lead mt-3">
                Order #<?= (int)$order_id ?>
                has been successfully paid.
            </p>

            <div class="mt-4">

                <a
                    href="index.php"
                    class="btn btn-primary"
                >
                    Back to Cashier Dashboard
                </a>

            </div>

        </div>

    </div>

</div>

</body>

</html>