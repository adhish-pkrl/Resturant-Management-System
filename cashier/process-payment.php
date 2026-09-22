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


/* ONLY POST REQUESTS */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit();
}


$order_id = filter_input(
    INPUT_POST,
    "order_id",
    FILTER_VALIDATE_INT
);

$payment_method = trim($_POST["payment_method"] ?? "");
$transaction_id = trim($_POST["transaction_id"] ?? "");


if (!$order_id || $payment_method === "") {
    die("Invalid payment information.");
}


/* ALLOWED PAYMENT METHODS */

$allowed_methods = [
    "cash",
    "card",
    "digital"
];

if (!in_array($payment_method, $allowed_methods, true)) {
    die("Invalid payment method.");
}


/* GET THE ORDER */

$order_sql = "
    SELECT id, total_amount
    FROM orders
    WHERE id = ?
      AND order_status = 'served'
      AND payment_status = 'unpaid'
    LIMIT 1
";

$order_stmt = mysqli_prepare($conn, $order_sql);

if (!$order_stmt) {
    die("Database error: " . mysqli_error($conn));
}

mysqli_stmt_bind_param(
    $order_stmt,
    "i",
    $order_id
);

mysqli_stmt_execute($order_stmt);

$order_result = mysqli_stmt_get_result($order_stmt);

$order = mysqli_fetch_assoc($order_result);

mysqli_stmt_close($order_stmt);


if (!$order) {
    die("Order not found or payment has already been completed.");
}


$amount = (float)$order["total_amount"];


/* GENERATE TRANSACTION ID WHEN NOT PROVIDED */

if ($transaction_id === "") {

    $transaction_id =
        strtoupper($payment_method) .
        "-" .
        $order_id .
        "-" .
        time();

}


/* START TRANSACTION */

mysqli_begin_transaction($conn);

try {

    /* INSERT PAYMENT */

    $payment_sql = "
        INSERT INTO payments
        (
            order_id,
            amount,
            payment_method,
            payment_status,
            transaction_id
        )
        VALUES (?, ?, ?, 'paid', ?)
    ";

    $payment_stmt = mysqli_prepare(
        $conn,
        $payment_sql
    );

    if (!$payment_stmt) {
        throw new Exception(
            mysqli_error($conn)
        );
    }

    mysqli_stmt_bind_param(
        $payment_stmt,
        "idss",
        $order_id,
        $amount,
        $payment_method,
        $transaction_id
    );

    if (!mysqli_stmt_execute($payment_stmt)) {
        throw new Exception(
            mysqli_stmt_error($payment_stmt)
        );
    }

    mysqli_stmt_close($payment_stmt);


    /* UPDATE ORDER */

    $update_sql = "
        UPDATE orders
        SET payment_status = 'paid'
        WHERE id = ?
        AND order_status = 'served'
        AND payment_status = 'unpaid'
    ";

    $update_stmt = mysqli_prepare(
        $conn,
        $update_sql
    );

    if (!$update_stmt) {
        throw new Exception(
            mysqli_error($conn)
        );
    }

    mysqli_stmt_bind_param(
        $update_stmt,
        "i",
        $order_id
    );

    if (!mysqli_stmt_execute($update_stmt)) {
        throw new Exception(
            mysqli_stmt_error($update_stmt)
        );
    }

    mysqli_stmt_close($update_stmt);


    mysqli_commit($conn);


    header(
        "Location: payment-success.php?order_id=" .
        $order_id
    );

    exit();

} catch (Exception $e) {

    mysqli_rollback($conn);

    die(
        "Payment failed: " .
        htmlspecialchars($e->getMessage())
    );

}