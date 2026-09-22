<?php

session_start();

if (
    !isset($_SESSION["user_id"]) ||
    $_SESSION["user_role"] !== "waiter"
) {
    header("Location: ../auth/login.php");
    exit();
}

$order_id = isset($_GET["order_id"])
    ? (int) $_GET["order_id"]
    : 0;

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Order Sent - RMS Restaurant</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

    <link
        rel="stylesheet"
        href="../assets/css/style.css?v=3"
    >

</head>

<body class="customer-page">

<div class="customer-layout">

    <main class="customer-main">

        <div
            class="customer-content d-flex justify-content-center align-items-center"
            style="min-height:80vh;"
        >

            <div
                class="customer-panel text-center"
                style="max-width:600px;"
            >

                <div
                    class="mb-4"
                    style="font-size:70px;"
                >
                    🍳
                </div>

                <h1 class="fw-bold">
                    Order Sent to Kitchen!
                </h1>

                <p class="text-secondary mt-3">

                    Order #

                    <strong>
                        <?php echo $order_id; ?>
                    </strong>

                    has been successfully sent
                    to the kitchen.

                </p>

                <div class="mt-4">

                    <a
                        href="tables.php"
                        class="btn btn-warning px-4 py-3 fw-semibold"
                    >

                        <i class="bi bi-table me-2"></i>

                        Back to Tables

                    </a>

                    <a
                        href="index.php"
                        class="btn btn-outline-secondary px-4 py-3 ms-2"
                    >

                        Dashboard

                    </a>

                </div>

            </div>

        </div>

    </main>

</div>

</body>

</html>