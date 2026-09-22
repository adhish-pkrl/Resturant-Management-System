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

$order_id = filter_input(INPUT_GET, "order_id", FILTER_VALIDATE_INT);

if (!$order_id) {
    die("Invalid order ID.");
}


/* GET ORDER DETAILS */

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
    WHERE o.id = ?
    AND o.order_status = 'served'
    AND o.payment_status = 'unpaid'
    LIMIT 1
";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die("Database error: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "i", $order_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$order = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$order) {
    die("Order not found, already paid, or not yet served.");
}


/* GET ORDER ITEMS */

$item_sql = "
    SELECT
        oi.quantity,
        oi.price,
        mi.name
    FROM order_items oi
    INNER JOIN menu_items mi
        ON oi.menu_item_id = mi.id
    WHERE oi.order_id = ?
    ORDER BY oi.id ASC
";

$item_stmt = mysqli_prepare($conn, $item_sql);

if (!$item_stmt) {
    die("Database error: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($item_stmt, "i", $order_id);
mysqli_stmt_execute($item_stmt);

$item_result = mysqli_stmt_get_result($item_stmt);

mysqli_stmt_close($item_stmt);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Bill - Order #<?= (int)$order["id"] ?></title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>

        body {
            background: #f5f6fa;
        }

        .bill-card {
            max-width: 850px;
            margin: 40px auto;
            border: none;
            border-radius: 14px;
        }

        .bill-header {
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .total-box {
            font-size: 1.25rem;
            font-weight: 700;
        }

    </style>

</head>

<body>

<div class="container">

    <div class="card bill-card shadow-sm">

        <div class="card-body p-4">

            <div class="bill-header">

                <div class="d-flex justify-content-between">

                    <div>

                        <h2>
                            Restaurant Management System
                        </h2>

                        <h5 class="text-muted">
                            Customer Bill
                        </h5>

                    </div>

                    <div class="text-end">

                        <strong>
                            Order #<?= (int)$order["id"] ?>
                        </strong>

                        <br>

                        Table:
                        <?= htmlspecialchars($order["table_number"] ?? "N/A") ?>

                        <br>

                        <small class="text-muted">
                            <?= htmlspecialchars($order["created_at"]) ?>
                        </small>

                    </div>

                </div>

            </div>


            <!-- ORDER ITEMS -->

            <div class="table-responsive">

                <table class="table table-bordered align-middle">

                    <thead class="table-dark">

                        <tr>

                            <th>Item</th>

                            <th class="text-center">
                                Quantity
                            </th>

                            <th class="text-end">
                                Price
                            </th>

                            <th class="text-end">
                                Subtotal
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php while ($item = mysqli_fetch_assoc($item_result)): ?>

                        <?php
                        $subtotal =
                            (float)$item["price"] *
                            (int)$item["quantity"];
                        ?>

                        <tr>

                            <td>
                                <?= htmlspecialchars($item["name"]) ?>
                            </td>

                            <td class="text-center">
                                <?= (int)$item["quantity"] ?>
                            </td>

                            <td class="text-end">
                                Rs.
                                <?= number_format(
                                    (float)$item["price"],
                                    2
                                ) ?>
                            </td>

                            <td class="text-end">
                                Rs.
                                <?= number_format(
                                    $subtotal,
                                    2
                                ) ?>
                            </td>

                        </tr>

                    <?php endwhile; ?>

                    </tbody>

                    <tfoot>

                        <tr>

                            <td
                                colspan="3"
                                class="text-end"
                            >
                                <strong>Total Amount</strong>
                            </td>

                            <td class="text-end total-box">

                                Rs.
                                <?= number_format(
                                    (float)$order["total_amount"],
                                    2
                                ) ?>

                            </td>

                        </tr>

                    </tfoot>

                </table>

            </div>


            <!-- PAYMENT FORM -->

            <div class="mt-4">

                <h4 class="mb-3">
                    Process Payment
                </h4>

                <form
                    action="process-payment.php"
                    method="POST"
                >

                    <input
                        type="hidden"
                        name="order_id"
                        value="<?= (int)$order["id"] ?>"
                    >

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Payment Method
                            </label>

                            <select
                                name="payment_method"
                                class="form-select"
                                required
                            >

                                <option value="">
                                    Select Payment Method
                                </option>

                                <option value="cash">
                                    Cash
                                </option>

                                <option value="card">
                                    Card
                                </option>

                                <option value="digital">
                                    Digital Payment
                                </option>

                            </select>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Transaction ID
                            </label>

                            <input
                                type="text"
                                name="transaction_id"
                                class="form-control"
                                placeholder="Optional"
                            >

                        </div>

                    </div>

                    <div class="d-flex justify-content-between mt-3">

                        <a
                            href="index.php"
                            class="btn btn-secondary"
                        >
                            Back to Dashboard
                        </a>

                        <button
                            type="submit"
                            class="btn btn-success"
                        >
                            Confirm Payment
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

</body>

</html>