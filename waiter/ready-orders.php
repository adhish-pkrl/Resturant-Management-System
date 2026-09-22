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
$user_name = $_SESSION["user_name"];


/* =====================================================
   MARK ORDER AS SERVED
   ===================================================== */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $order_id = (int) ($_POST["order_id"] ?? 0);

    if ($order_id > 0) {

        /* Get the table belonging to this order */
        $table_sql = "
            SELECT table_id
            FROM orders
            WHERE id = ?
            AND waiter_id = ?
            AND order_status = 'ready'
            LIMIT 1
        ";

        $table_stmt = $conn->prepare($table_sql);

        $table_stmt->bind_param(
            "ii",
            $order_id,
            $waiter_id
        );

        $table_stmt->execute();

        $table_result = $table_stmt->get_result();

        if ($table_result->num_rows === 1) {

            $order_data = $table_result->fetch_assoc();

            $table_id = (int) $order_data["table_id"];

            $table_stmt->close();


            /* Mark order as served */
            $serve_sql = "
                UPDATE orders
                SET order_status = 'served'
                WHERE id = ?
                AND waiter_id = ?
                AND order_status = 'ready'
            ";

            $serve_stmt = $conn->prepare($serve_sql);

            $serve_stmt->bind_param(
                "ii",
                $order_id,
                $waiter_id
            );

            $serve_stmt->execute();

            $serve_stmt->close();


            /* Make the restaurant table available again */
            $available_sql = "
                UPDATE restaurant_tables
                SET status = 'available'
                WHERE id = ?
            ";

            $available_stmt = $conn->prepare($available_sql);

            $available_stmt->bind_param(
                "i",
                $table_id
            );

            $available_stmt->execute();

            $available_stmt->close();
        } else {

            $table_stmt->close();
        }
    }

    header("Location: ready-orders.php");
    exit();
}
/* =====================================================
   GET READY ORDERS
   ===================================================== */

$orders = [];

$order_sql = "
    SELECT
        o.id,
        o.table_id,
        o.total_amount,
        o.order_status,
        o.created_at,
        rt.table_number

    FROM orders o

    INNER JOIN restaurant_tables rt
        ON o.table_id = rt.id

    WHERE o.waiter_id = ?
    AND o.order_status = 'ready'

    ORDER BY o.created_at ASC
";

$order_stmt = $conn->prepare($order_sql);

$order_stmt->bind_param(
    "i",
    $waiter_id
);

$order_stmt->execute();

$order_result = $order_stmt->get_result();

while ($order = $order_result->fetch_assoc()) {

    $orders[] = $order;

}

$order_stmt->close();


/* =====================================================
   GET ITEMS FOR EACH ORDER
   ===================================================== */

foreach ($orders as &$order) {

    $order_id = (int) $order["id"];

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

    $item_stmt = $conn->prepare($item_sql);

    $item_stmt->bind_param(
        "i",
        $order_id
    );

    $item_stmt->execute();

    $item_result = $item_stmt->get_result();

    $order["items"] = [];

    while ($item = $item_result->fetch_assoc()) {

        $order["items"][] = $item;

    }

    $item_stmt->close();

}

unset($order);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Ready Orders - RMS Restaurant
    </title>

    <!-- Bootstrap -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Bootstrap Icons -->

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

    <!-- RMS CSS -->

    <link
        rel="stylesheet"
        href="../assets/css/style.css?v=3"
    >

    <style>

        .orders-page-header {

            display: flex;

            justify-content: space-between;

            align-items: center;

            gap: 20px;

            margin-bottom: 25px;

        }

        .orders-count {

            background: #f0fdf4;

            color: #15803d;

            border: 1px solid #bbf7d0;

            border-radius: 10px;

            padding: 10px 16px;

            font-weight: 700;

        }

        .ready-order-card {

            background: #ffffff;

            border: 1px solid #e5e7eb;

            border-radius: 16px;

            padding: 22px;

            margin-bottom: 18px;

        }

        .ready-order-header {

            display: flex;

            justify-content: space-between;

            align-items: center;

            gap: 15px;

            padding-bottom: 15px;

            border-bottom: 1px solid #eeeeee;

        }

        .order-number {

            font-size: 18px;

            font-weight: 800;

        }

        .table-label {

            color: #6b7280;

            font-size: 14px;

        }

        .status-badge {

            display: inline-block;

            padding: 7px 12px;

            border-radius: 20px;

            font-size: 13px;

            font-weight: 700;

        }

        .status-ready {

            background: #f0fdf4;

            color: #15803d;

        }

        .order-food-item {

            display: flex;

            justify-content: space-between;

            gap: 15px;

            padding: 11px 0;

            border-bottom: 1px solid #f1f1f1;

        }

        .food-name {

            font-weight: 600;

        }

        .food-quantity {

            color: #6b7280;

            font-size: 14px;

        }

        .order-footer {

            display: flex;

            justify-content: space-between;

            align-items: center;

            margin-top: 18px;

        }

        .order-total {

            font-size: 20px;

            font-weight: 800;

        }

        .empty-orders {

            text-align: center;

            padding: 70px 20px;

        }

        .empty-orders i {

            font-size: 65px;

            color: #9ca3af;

        }

    </style>

</head>


<body class="customer-page">


<div class="customer-layout">


    <!-- =====================================================
         SIDEBAR
         ===================================================== -->

    <aside class="customer-sidebar">


        <div class="customer-brand">

            <a href="index.php">

                <span>RMS</span> Restaurant

            </a>

        </div>


        <!-- PROFILE -->

        <div class="customer-profile">

            <div class="customer-avatar">

                <?php

                echo strtoupper(
                    substr($user_name, 0, 1)
                );

                ?>

            </div>


            <div>

                <strong>

                    <?php

                    echo htmlspecialchars(
                        $user_name
                    );

                    ?>

                </strong>

                <small>
                    Waiter
                </small>

            </div>

        </div>


        <!-- NAVIGATION -->

        <nav class="customer-nav">


            <a
                href="index.php"
                class="customer-nav-link"
            >

                <i class="bi bi-grid-1x2-fill"></i>

                Dashboard

            </a>


            <a
                href="tables.php"
                class="customer-nav-link"
            >

                <i class="bi bi-table"></i>

                Tables

            </a>


            <a
                href="new-order.php"
                class="customer-nav-link"
            >

                <i class="bi bi-plus-circle"></i>

                New Order

            </a>


            <a
                href="active-orders.php"
                class="customer-nav-link"
            >

                <i class="bi bi-receipt"></i>

                Active Orders

            </a>


            <a
                href="ready-orders.php"
                class="customer-nav-link active"
            >

                <i class="bi bi-check-circle"></i>

                Ready Orders

            </a>


        </nav>


        <!-- LOGOUT -->

        <div class="customer-sidebar-bottom">

            <a
                href="../auth/logout.php"
                class="customer-nav-link logout-link"
            >

                <i class="bi bi-box-arrow-left"></i>

                Logout

            </a>

        </div>


    </aside>


    <!-- =====================================================
         MAIN
         ===================================================== -->

    <main class="customer-main">


        <!-- TOPBAR -->

        <header class="customer-topbar">


            <div>

                <small class="text-secondary">
                    Waiter Panel
                </small>

                <h5 class="mb-0 fw-bold">
                    Ready Orders
                </h5>

            </div>


            <div class="customer-topbar-right">

                <div class="customer-top-profile">


                    <div class="customer-top-avatar">

                        <?php

                        echo strtoupper(
                            substr($user_name, 0, 1)
                        );

                        ?>

                    </div>


                    <div class="d-none d-md-block">

                        <strong>

                            <?php

                            echo htmlspecialchars(
                                $user_name
                            );

                            ?>

                        </strong>

                        <small>
                            Waiter
                        </small>

                    </div>


                </div>

            </div>


        </header>


        <!-- =================================================
             CONTENT
             ================================================= -->

        <div class="customer-content">


            <!-- PAGE HEADER -->

            <div class="orders-page-header">


                <div>

                    <span class="welcome-label">
                        READY TO SERVE
                    </span>

                    <h1 class="mt-2 mb-1">
                        Ready Orders
                    </h1>

                    <p class="text-secondary mb-0">
                        Orders that are ready to be served to customers.
                    </p>

                </div>


                <div class="orders-count">

                    <i class="bi bi-check-circle me-2"></i>

                    <?php echo count($orders); ?>

                    Ready

                </div>


            </div>


            <!-- =================================================
                 ORDERS
                 ================================================= -->

            <?php if (count($orders) > 0): ?>


                <?php foreach ($orders as $order): ?>


                    <div class="ready-order-card">


                        <!-- ORDER HEADER -->

                        <div class="ready-order-header">


                            <div>


                                <div class="order-number">

                                    Order #

                                    <?php

                                    echo (int)
                                        $order["id"];

                                    ?>

                                </div>


                                <div class="table-label mt-1">

                                    <i
                                        class="bi bi-table me-1"
                                    ></i>

                                    Table

                                    <?php

                                    echo htmlspecialchars(
                                        $order["table_number"]
                                    );

                                    ?>

                                </div>


                            </div>


                            <div>

                                <span
                                    class="status-badge status-ready"
                                >

                                    <i class="bi bi-check-circle me-1"></i>

                                    Ready

                                </span>

                            </div>


                        </div>


                        <!-- FOOD ITEMS -->

                        <div class="mt-3">


                            <?php foreach (
                                $order["items"]
                                as $item
                            ): ?>


                                <div
                                    class="order-food-item"
                                >


                                    <div>


                                        <div
                                            class="food-name"
                                        >

                                            <?php

                                            echo htmlspecialchars(
                                                $item["name"]
                                            );

                                            ?>

                                        </div>


                                        <div
                                            class="food-quantity"
                                        >

                                            Quantity:

                                            <?php

                                            echo (int)
                                                $item["quantity"];

                                            ?>

                                            ×

                                            <?php

                                            echo number_format(
                                                (float)$item["price"],
                                                2
                                            );

                                            ?>

                                        </div>


                                    </div>


                                    <strong>

                                        <?php

                                        $item_total =
                                            (float)$item["price"]
                                            *
                                            (int)$item["quantity"];

                                        echo number_format(
                                            $item_total,
                                            2
                                        );

                                        ?>

                                    </strong>


                                </div>


                            <?php endforeach; ?>


                        </div>


                        <!-- ORDER FOOTER -->

                        <div class="order-footer">


                            <div>

                                <small
                                    class="text-secondary"
                                >

                                    Ordered:

                                    <?php

                                    echo date(
                                        "M d, Y h:i A",
                                        strtotime(
                                            $order["created_at"]
                                        )
                                    );

                                    ?>

                                </small>

                            </div>


                            <div
                                class="order-total"
                            >

                                <?php

                                echo number_format(
                                    (float)$order["total_amount"],
                                    2
                                );

                                ?>

                            </div>


                        </div>


                        <!-- SERVE BUTTON -->
                            <div class="mt-3">

                                <form method="POST" action="">

                                    <input
                                        type="hidden"
                                        name="order_id"
                                        value="<?php echo (int)$order['id']; ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="btn btn-success w-100 py-3 fw-semibold"
                                    >

                                        <i class="bi bi-check2-circle me-2"></i>

                                        Mark as Served

                                    </button>

                                </form>

                            </div>

                    </div>


                <?php endforeach; ?>


            <?php else: ?>


                <!-- EMPTY STATE -->

                <div
                    class="customer-panel empty-orders"
                >

                    <i class="bi bi-check-circle"></i>


                    <h3 class="mt-4">

                        No Ready Orders

                    </h3>


                    <p class="text-secondary">

                        There are currently no orders
                        ready to be served.

                    </p>


                    <a
                        href="active-orders.php"
                        class="btn btn-warning px-4 py-3 mt-2 fw-semibold"
                    >

                        <i
                            class="bi bi-receipt me-2"
                        ></i>

                        View Active Orders

                    </a>

                </div>


            <?php endif; ?>


        </div>


    </main>


</div>

</body>

</html>