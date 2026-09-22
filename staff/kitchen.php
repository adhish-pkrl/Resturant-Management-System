<?php

session_start();

include '../config/database.php';

/* Check staff login */
if (
    !isset($_SESSION["user_id"]) ||
    !in_array($_SESSION["user_role"], ["admin", "kitchen"])
) {
    header("Location: ../auth/login.php");
    exit();
}

$user_name = $_SESSION["user_name"];


/* Get pending and preparing orders */

$sql = "
    SELECT
        o.id,
        o.table_id,
        o.total_amount,
        o.order_status,
        o.created_at,
        rt.table_number

    FROM orders o

    LEFT JOIN restaurant_tables rt
        ON o.table_id = rt.id

    WHERE o.order_status IN ('pending', 'preparing')

    ORDER BY o.created_at ASC
";

$result = $conn->query($sql);

$orders = [];


if ($result) {

    while ($row = $result->fetch_assoc()) {

        $orders[] = $row;

    }

}


/* Get food items for each order */

foreach ($orders as &$order) {

    $order_id = (int)$order["id"];

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

    <title>Kitchen - RMS Restaurant</title>


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

        .kitchen-header {

            display: flex;

            justify-content: space-between;

            align-items: center;

            margin-bottom: 25px;

        }


        .kitchen-title {

            font-size: 30px;

            font-weight: 800;

        }


        .kitchen-subtitle {

            color: #6b7280;

        }


        .kitchen-order-card {

            background: #ffffff;

            border: 1px solid #e5e7eb;

            border-radius: 16px;

            padding: 22px;

            margin-bottom: 20px;

            box-shadow:
                0 5px 20px rgba(0,0,0,0.04);

        }


        .kitchen-order-header {

            display: flex;

            justify-content: space-between;

            align-items: center;

            border-bottom: 1px solid #eeeeee;

            padding-bottom: 15px;

            margin-bottom: 15px;

        }


        .order-number {

            font-size: 20px;

            font-weight: 800;

        }


        .table-name {

            color: #6b7280;

            margin-top: 4px;

        }


        .kitchen-status {

            padding: 7px 13px;

            border-radius: 20px;

            font-size: 13px;

            font-weight: 700;

        }


        .status-pending {

            background: #fff7ed;

            color: #c2410c;

        }


        .status-preparing {

            background: #eff6ff;

            color: #1d4ed8;

        }


        .kitchen-item {

            display: flex;

            justify-content: space-between;

            align-items: center;

            padding: 12px 0;

            border-bottom: 1px solid #f1f1f1;

        }


        .kitchen-item-name {

            font-weight: 700;

        }


        .kitchen-item-quantity {

            color: #6b7280;

            font-size: 14px;

        }


        .kitchen-action {

            margin-top: 20px;

        }


        .empty-kitchen {

            text-align: center;

            padding: 80px 20px;

        }


        .empty-kitchen i {

            font-size: 65px;

            color: #9ca3af;

        }


        .kitchen-count {

            background: #fff7ed;

            color: #c2410c;

            border: 1px solid #fed7aa;

            padding: 10px 16px;

            border-radius: 10px;

            font-weight: 700;

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

            <a href="dashboard.php">

                <span>RMS</span> Restaurant

            </a>

        </div>


        <!-- USER -->

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
                    Staff
                </small>

            </div>

        </div>


        <!-- NAVIGATION -->

        <nav class="customer-nav">


            <a
                href="dashboard.php"
                class="customer-nav-link"
            >

                <i class="bi bi-grid-1x2-fill"></i>

                Dashboard

            </a>


            <a
                href="kitchen.php"
                class="customer-nav-link active"
            >

                <i class="bi bi-fire"></i>

                Kitchen

            </a>


            <a
                href="orders.php"
                class="customer-nav-link"
            >

                <i class="bi bi-receipt"></i>

                Orders

            </a>


            <a
                href="tables.php"
                class="customer-nav-link"
            >

                <i class="bi bi-table"></i>

                Tables

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
                    Staff Panel
                </small>


                <h5 class="mb-0 fw-bold">
                    Kitchen
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
                            Staff
                        </small>

                    </div>


                </div>

            </div>


        </header>


        <!-- =====================================================
             CONTENT
             ===================================================== -->

        <div class="customer-content">


            <div class="kitchen-header">


                <div>

                    <div class="kitchen-title">

                        Kitchen Orders

                    </div>


                    <div class="kitchen-subtitle">

                        Orders waiting to be prepared
                        and currently being prepared.

                    </div>

                </div>


                <div class="kitchen-count">

                    <i class="bi bi-fire me-2"></i>

                    <?php echo count($orders); ?>

                    Orders

                </div>


            </div>


            <!-- =================================================
                 ORDERS
                 ================================================= -->

            <?php if (count($orders) > 0): ?>


                <div class="row">


                    <?php foreach ($orders as $order): ?>


                        <div class="col-lg-6">


                            <div class="kitchen-order-card">


                                <!-- HEADER -->

                                <div
                                    class="kitchen-order-header"
                                >


                                    <div>


                                        <div
                                            class="order-number"
                                        >

                                            Order #

                                            <?php

                                            echo (int)
                                                $order["id"];

                                            ?>

                                        </div>


                                        <div
                                            class="table-name"
                                        >

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


                                        <?php

                                        $status =
                                            strtolower(
                                                $order["order_status"]
                                            );

                                        ?>


                                        <span
                                            class="kitchen-status status-<?php echo htmlspecialchars($status); ?>"
                                        >

                                            <?php

                                            echo ucfirst(
                                                $status
                                            );

                                            ?>

                                        </span>


                                    </div>


                                </div>


                                <!-- ITEMS -->

                                <div>


                                    <?php foreach (
                                        $order["items"]
                                        as $item
                                    ): ?>


                                        <div
                                            class="kitchen-item"
                                        >


                                            <div>


                                                <div
                                                    class="kitchen-item-name"
                                                >

                                                    <?php

                                                    echo htmlspecialchars(
                                                        $item["name"]
                                                    );

                                                    ?>

                                                </div>


                                                <div
                                                    class="kitchen-item-quantity"
                                                >

                                                    Quantity:

                                                    <?php

                                                    echo (int)
                                                        $item["quantity"];

                                                    ?>

                                                </div>

                                            </div>


                                        </div>


                                    <?php endforeach; ?>


                                </div>


                                <!-- TIME -->

                                <div
                                    class="text-secondary mt-3"
                                >

                                    <i
                                        class="bi bi-clock me-1"
                                    ></i>

                                    Ordered:

                                    <?php

                                    echo date(
                                        "M d, Y h:i A",
                                        strtotime(
                                            $order["created_at"]
                                        )
                                    );

                                    ?>

                                </div>


                                <!-- ACTION -->

                                <div class="kitchen-action">


                                    <?php if (
                                        $status === "pending"
                                    ): ?>


                                        <button
                                            type="button"
                                            class="btn btn-warning w-100 py-3 fw-semibold"
                                            onclick="startPreparing(<?php echo (int)$order['id']; ?>)"
                                        >

                                            <i
                                                class="bi bi-fire me-2"
                                            ></i>

                                            Start Preparing

                                        </button>


                                    <?php elseif (
                                        $status === "preparing"
                                    ): ?>


                                        <button
                                            type="button"
                                            class="btn btn-success w-100 py-3 fw-semibold"
                                            onclick="markReady(<?php echo (int)$order['id']; ?>)"
                                        >

                                            <i
                                                class="bi bi-check-circle me-2"
                                            ></i>

                                            Mark Ready

                                        </button>


                                    <?php endif; ?>


                                </div>


                            </div>


                        </div>


                    <?php endforeach; ?>


                </div>


            <?php else: ?>


                <div
                    class="customer-panel empty-kitchen"
                >


                    <i class="bi bi-cup-hot"></i>


                    <h3 class="mt-4">

                        Kitchen is Clear

                    </h3>


                    <p class="text-secondary">

                        There are currently no orders
                        waiting for the kitchen.

                    </p>


                </div>


            <?php endif; ?>


        </div>


    </main>


</div>

<script>

function startPreparing(orderId) {

    if (!confirm("Start preparing Order #" + orderId + "?")) {
        return;
    }

    fetch("update-order-status.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "order_id=" + encodeURIComponent(orderId) +
              "&status=preparing"
    })
    .then(response => response.text())
    .then(data => {

        if (data.trim() === "success") {
            location.reload();
        } else {
            alert("Unable to update the order.");
        }

    })
    .catch(error => {

        console.error(error);
        alert("An error occurred while updating the order.");

    });

}


function markReady(orderId) {

    if (!confirm("Mark Order #" + orderId + " as ready?")) {
        return;
    }

    fetch("update-order-status.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "order_id=" + encodeURIComponent(orderId) +
              "&status=ready"
    })
    .then(response => response.text())
    .then(data => {

        if (data.trim() === "success") {
            location.reload();
        } else {
            alert("Unable to update the order.");
        }

    })
    .catch(error => {

        console.error(error);
        alert("An error occurred while updating the order.");

    });

}

</script>

</body>

</html>