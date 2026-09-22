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

require_once "../config/database.php";

$sql = "
    SELECT
        o.id,
        o.table_id,
        o.total_amount,
        o.order_status,
        o.payment_status,
        o.created_at,
        rt.table_number
    FROM orders o
    LEFT JOIN restaurant_tables rt
        ON o.table_id = rt.id
    WHERE o.order_status = 'served'
      AND o.payment_status = 'unpaid'
    ORDER BY o.id DESC
";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Database error: " . mysqli_error($conn));
}

$total_unpaid_orders = mysqli_num_rows($result);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cashier Dashboard</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>

        body {
            background-color: #f5f6fa;
        }

        .navbar {
            background-color: #212529;
        }

        .dashboard-card {
            border: none;
            border-radius: 14px;
        }

        .summary-card {
            border: none;
            border-radius: 14px;
        }

        .amount {
            font-weight: 700;
        }

        .page-title {
            font-weight: 700;
        }

    </style>

</head>

<body>

<!-- NAVBAR -->

<nav class="navbar navbar-dark">

    <div class="container">

        <span class="navbar-brand fw-bold">
            Restaurant Management System - Cashier
        </span>

        <div class="d-flex align-items-center">

            <span class="text-white me-3">
                <?= htmlspecialchars($_SESSION["user_name"]) ?>
            </span>

            <a
                href="../auth/logout.php"
                class="btn btn-outline-light btn-sm"
            >
                Logout
            </a>

        </div>

    </div>

</nav>


<!-- MAIN CONTENT -->

<div class="container mt-4">

    <div class="mb-4">

        <h2 class="page-title">
            Cashier Dashboard
        </h2>

        <p class="text-muted">
            Manage served orders and process payments.
        </p>

    </div>


    <!-- SUMMARY -->

    <div class="row mb-4">

        <div class="col-md-4">

            <div class="card summary-card shadow-sm">

                <div class="card-body">

                    <h6 class="text-muted">
                        Unpaid Served Orders
                    </h6>

                    <h2 class="fw-bold">
                        <?= $total_unpaid_orders ?>
                    </h2>

                </div>

            </div>

        </div>

    </div>


    <!-- ORDERS -->

    <div class="card dashboard-card shadow-sm">

        <div class="card-body">

            <h4 class="mb-3">
                Orders Waiting for Payment
            </h4>

            <?php if ($total_unpaid_orders == 0): ?>

                <div class="text-center py-5">

                    <h5>
                        No unpaid orders
                    </h5>

                    <p class="text-muted mb-0">
                        There are currently no served orders waiting for payment.
                    </p>

                </div>

            <?php else: ?>

                <div class="table-responsive">

                    <table class="table table-hover table-bordered align-middle">

                        <thead class="table-dark">

                            <tr>

                                <th>Order #</th>

                                <th>Table</th>

                                <th>Total Amount</th>

                                <th>Order Status</th>

                                <th>Payment Status</th>

                                <th>Created At</th>

                                <th>Action</th>

                            </tr>

                        </thead>

                        <tbody>

                        <?php while ($order = mysqli_fetch_assoc($result)): ?>

                            <tr>

                                <td>
                                    #<?= (int)$order["id"] ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $order["table_number"] ?? "N/A"
                                    ) ?>
                                </td>

                                <td class="amount">

                                    Rs.
                                    <?= number_format(
                                        (float)$order["total_amount"],
                                        2
                                    ) ?>

                                </td>

                                <td>

                                    <span class="badge bg-success">
                                        <?= htmlspecialchars(
                                            ucfirst($order["order_status"])
                                        ) ?>
                                    </span>

                                </td>

                                <td>

                                    <span class="badge bg-warning text-dark">
                                        <?= htmlspecialchars(
                                            ucfirst($order["payment_status"])
                                        ) ?>
                                    </span>

                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $order["created_at"]
                                    ) ?>
                                </td>

                                <td>

                                    <a
                                        href="bill.php?order_id=<?= (int)$order["id"] ?>"
                                        class="btn btn-primary btn-sm"
                                    >
                                        View Bill
                                    </a>

                                </td>

                            </tr>

                        <?php endwhile; ?>

                        </tbody>

                    </table>

                </div>

            <?php endif; ?>

        </div>

    </div>

</div>

</body>

</html>