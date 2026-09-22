<?php

session_start();

include '../config/database.php';


/* =====================================================
   CHECK WAITER LOGIN
   ===================================================== */

if (
    !isset($_SESSION["user_id"]) ||
    $_SESSION["user_role"] !== "waiter"
) {
    header("Location: ../auth/login.php");
    exit();
}


$waiter_id = (int) $_SESSION["user_id"];


/* =====================================================
   CHECK REQUEST
   ===================================================== */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: tables.php");
    exit();

}


$table_id = isset($_POST["table_id"])
    ? (int) $_POST["table_id"]
    : 0;

$total_amount = isset($_POST["total_amount"])
    ? (float) $_POST["total_amount"]
    : 0;

$items_json = $_POST["items"] ?? "";


if (
    $table_id <= 0 ||
    empty($items_json)
) {

    die("Invalid order information.");

}


$items = json_decode(
    $items_json,
    true
);


if (
    !is_array($items) ||
    count($items) === 0
) {

    die("No food items were selected.");

}


/* =====================================================
   CHECK TABLE
   ===================================================== */

$table_sql = "
    SELECT id, status
    FROM restaurant_tables
    WHERE id = ?
    LIMIT 1
";

$table_stmt = $conn->prepare($table_sql);

$table_stmt->bind_param(
    "i",
    $table_id
);

$table_stmt->execute();

$table_result =
    $table_stmt->get_result();


if ($table_result->num_rows !== 1) {

    $table_stmt->close();

    die("Restaurant table not found.");

}


$table = $table_result->fetch_assoc();

$table_stmt->close();


if ($table["status"] !== "available") {

    die("This table is no longer available.");

}


/* =====================================================
   START DATABASE TRANSACTION
   ===================================================== */

$conn->begin_transaction();


try {


    /* =================================================
       CREATE ORDER
       ================================================= */

    $order_status = "pending";

    $payment_status = "unpaid";

    $order_type = "dine-in";

    $notes = "";


    $order_sql = "
        INSERT INTO orders
        (
            waiter_id,
            table_id,
            order_type,
            total_amount,
            order_status,
            payment_status,
            notes
        )
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ";


    $order_stmt =
        $conn->prepare($order_sql);


    if (!$order_stmt) {

        throw new Exception(
            "Could not prepare order."
        );

    }


    $order_stmt->bind_param(
        "iisdsss",
        $waiter_id,
        $table_id,
        $order_type,
        $total_amount,
        $order_status,
        $payment_status,
        $notes
    );


    if (!$order_stmt->execute()) {

        throw new Exception(
            "Could not create order."
        );

    }


    $order_id =
        $conn->insert_id;


    $order_stmt->close();


    /* =================================================
       ADD ORDER ITEMS
       ================================================= */

    $item_sql = "
        INSERT INTO order_items
        (
            order_id,
            menu_item_id,
            quantity,
            price
        )
        VALUES (?, ?, ?, ?)
    ";


    $item_stmt =
        $conn->prepare($item_sql);


    if (!$item_stmt) {

        throw new Exception(
            "Could not prepare order items."
        );

    }


    foreach ($items as $item) {


        $menu_item_id =
            isset($item["id"])
                ? (int)$item["id"]
                : 0;


        $quantity =
            isset($item["quantity"])
                ? (int)$item["quantity"]
                : 0;


        if (
            $menu_item_id <= 0 ||
            $quantity <= 0
        ) {

            continue;

        }


        /*
         * Get the current price directly
         * from the database.
         */

        $price_sql = "
            SELECT price
            FROM menu_items
            WHERE id = ?
              AND availability = 'available'
            LIMIT 1
        ";


        $price_stmt =
            $conn->prepare($price_sql);


        $price_stmt->bind_param(
            "i",
            $menu_item_id
        );


        $price_stmt->execute();


        $price_result =
            $price_stmt->get_result();


        if ($price_result->num_rows !== 1) {

            $price_stmt->close();

            throw new Exception(
                "One of the selected food items is unavailable."
            );

        }


        $menu_item =
            $price_result->fetch_assoc();


        $price =
            (float)$menu_item["price"];


        $price_stmt->close();


        $item_stmt->bind_param(
            "iiid",
            $order_id,
            $menu_item_id,
            $quantity,
            $price
        );


        if (!$item_stmt->execute()) {

            throw new Exception(
                "Could not save order item."
            );

        }

    }


    $item_stmt->close();


    /* =================================================
       MARK TABLE AS OCCUPIED
       ================================================= */

    $table_update_sql = "
        UPDATE restaurant_tables
        SET status = 'occupied'
        WHERE id = ?
    ";


    $table_update_stmt =
        $conn->prepare(
            $table_update_sql
        );


    $table_update_stmt->bind_param(
        "i",
        $table_id
    );


    if (!$table_update_stmt->execute()) {

        throw new Exception(
            "Could not update table status."
        );

    }


    $table_update_stmt->close();


    /* =================================================
       COMPLETE TRANSACTION
       ================================================= */

    $conn->commit();


    /*
     * Order successfully created.
     */

    header(
        "Location: order-success.php?order_id="
        . $order_id
    );

    exit();


} catch (Exception $e) {


    /*
     * Something failed.
     * Undo all database changes.
     */

    $conn->rollback();


    die(
        "Order could not be created: "
        . htmlspecialchars($e->getMessage())
    );

}

?>