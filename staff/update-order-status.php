<?php

session_start();

include '../config/database.php';


/*
 * Only logged-in kitchen staff or admins
 * can update order status.
 */

if (
    !isset($_SESSION["user_id"]) ||
    !in_array($_SESSION["user_role"], ["admin", "kitchen"])
) {
    http_response_code(403);
    echo "unauthorized";
    exit();
}


/*
 * Make sure this is a POST request.
 */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo "invalid_request";
    exit();
}


/*
 * Get the order ID and requested status.
 */

$order_id = isset($_POST["order_id"])
    ? (int) $_POST["order_id"]
    : 0;

$status = isset($_POST["status"])
    ? trim($_POST["status"])
    : "";


/*
 * Only these statuses are allowed.
 */

$allowed_statuses = [
    "preparing",
    "ready"
];


if ($order_id <= 0 || !in_array($status, $allowed_statuses)) {

    http_response_code(400);
    echo "invalid_data";
    exit();

}


/*
 * Update the order status.
 */

$sql = "
    UPDATE orders
    SET order_status = ?
    WHERE id = ?
    LIMIT 1
";

$stmt = $conn->prepare($sql);

if (!$stmt) {

    http_response_code(500);
    echo "database_error";
    exit();

}


$stmt->bind_param(
    "si",
    $status,
    $order_id
);


if ($stmt->execute()) {

    if ($stmt->affected_rows > 0) {

        echo "success";

    } else {

        echo "not_found";

    }

} else {

    http_response_code(500);
    echo "database_error";

}


$stmt->close();
$conn->close();

?>