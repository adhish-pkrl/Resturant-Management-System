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
   GET SELECTED TABLE
   ===================================================== */

$table_id = isset($_GET["table_id"])
    ? (int) $_GET["table_id"]
    : 0;


if ($table_id <= 0) {

    header("Location: tables.php");
    exit();

}


/* =====================================================
   GET TABLE INFORMATION
   ===================================================== */

$table_sql = "
    SELECT id, table_number, capacity, status
    FROM restaurant_tables
    WHERE id = ?
    LIMIT 1
";

$table_stmt = $conn->prepare($table_sql);
$table_stmt->bind_param("i", $table_id);
$table_stmt->execute();

$table_result = $table_stmt->get_result();

if ($table_result->num_rows !== 1) {

    $table_stmt->close();

    header("Location: tables.php");
    exit();

}

$table = $table_result->fetch_assoc();

$table_stmt->close();


/* =====================================================
   MAKE SURE TABLE IS AVAILABLE
   ===================================================== */

if ($table["status"] !== "available") {

    header("Location: tables.php");
    exit();

}


/* =====================================================
   GET AVAILABLE MENU ITEMS
   ===================================================== */

$menu_sql = "
    SELECT
        id,
        name,
        description,
        price,
        image,
        availability,
        preparation_time
    FROM menu_items
    WHERE availability = 'available'
    ORDER BY name ASC
";

$menu_result = $conn->query($menu_sql);

$menu_items = [];

if ($menu_result) {

    while ($row = $menu_result->fetch_assoc()) {

        $menu_items[] = $row;

    }

}

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
        New Order - Table <?php echo htmlspecialchars($table["table_number"]); ?>
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

        .order-page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            margin-bottom: 25px;
        }

        .order-table-badge {
            background: #fff7ed;
            color: #c2410c;
            border: 1px solid #fed7aa;
            border-radius: 10px;
            padding: 10px 15px;
            font-weight: 700;
        }

        .menu-food-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 15px;
            overflow: hidden;
            height: 100%;
            transition: 0.2s ease;
        }

        .menu-food-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        }

        .menu-food-image {
            width: 100%;
            height: 170px;
            object-fit: cover;
            background: #f3f4f6;
        }

        .menu-food-placeholder {
            width: 100%;
            height: 170px;
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 45px;
            color: #9ca3af;
        }

        .menu-food-body {
            padding: 18px;
        }

        .menu-food-name {
            font-size: 17px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .menu-food-description {
            color: #6b7280;
            font-size: 13px;
            min-height: 40px;
        }

        .menu-food-price {
            color: #d97706;
            font-size: 18px;
            font-weight: 800;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 12px;
        }

        .quantity-input {
            width: 65px;
            text-align: center;
        }

        .order-summary {
            position: sticky;
            top: 20px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            padding: 10px 0;
            border-bottom: 1px solid #eeeeee;
        }

        .summary-item-name {
            font-weight: 600;
        }

        .summary-total {
            display: flex;
            justify-content: space-between;
            font-size: 21px;
            font-weight: 800;
            padding-top: 18px;
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
                    echo htmlspecialchars($user_name);
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
        class="customer-nav-link active"
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
        class="customer-nav-link"
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
                    Create New Order
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
                            echo htmlspecialchars($user_name);
                            ?>
                        </strong>

                        <small>
                            Waiter
                        </small>

                    </div>

                </div>

            </div>

        </header>


        <!-- CONTENT -->

        <div class="customer-content">


            <!-- PAGE HEADER -->

            <div class="order-page-header">

                <div>

                    <span class="welcome-label">
                        NEW ORDER
                    </span>

                    <h1 class="mt-2 mb-1">
                        Select Food
                    </h1>

                    <p class="text-secondary mb-0">
                        Choose the food items and quantities
                        for this table.
                    </p>

                </div>


                <div class="order-table-badge">

                    <i class="bi bi-table me-2"></i>

                    Table
                    <?php
                    echo htmlspecialchars(
                        $table["table_number"]
                    );
                    ?>

                </div>

            </div>


            <div class="row g-4">


                <!-- =================================================
                     FOOD MENU
                     ================================================= -->

                <div class="col-lg-8">

                    <div class="customer-panel">

                        <div class="customer-panel-header">

                            <div>

                                <span class="panel-label">
                                    MENU
                                </span>

                                <h4>
                                    Available Food
                                </h4>

                            </div>


                            <span class="text-secondary">

                                <?php
                                echo count($menu_items);
                                ?>

                                items

                            </span>

                        </div>


                        <?php if (count($menu_items) > 0): ?>


                            <div class="row g-4">


                                <?php foreach ($menu_items as $item): ?>


                                    <div class="col-md-6">


                                        <div
                                            class="menu-food-card"
                                            data-menu-id="<?php echo (int)$item["id"]; ?>"
                                        >


                                            <!-- IMAGE -->

                                            <?php if (
                                                !empty($item["image"])
                                            ): ?>

                                                <img
                                                    src="../uploads/menu_items/<?php echo htmlspecialchars($item["image"]); ?>"
                                                    alt="<?php echo htmlspecialchars($item["name"]); ?>"
                                                    class="menu-food-image"
                                                >

                                            <?php else: ?>

                                                <div class="menu-food-placeholder">

                                                    <i class="bi bi-egg-fried"></i>

                                                </div>

                                            <?php endif; ?>


                                            <!-- BODY -->

                                            <div class="menu-food-body">


                                                <div class="menu-food-name">

                                                    <?php
                                                    echo htmlspecialchars(
                                                        $item["name"]
                                                    );
                                                    ?>

                                                </div>


                                                <div class="menu-food-description">

                                                    <?php
                                                    echo htmlspecialchars(
                                                        $item["description"] ?? ""
                                                    );
                                                    ?>

                                                </div>


                                                <div class="d-flex justify-content-between align-items-center mt-3">


                                                    <span class="menu-food-price">

                                                        <?php
                                                        echo number_format(
                                                            (float)$item["price"],
                                                            2
                                                        );
                                                        ?>

                                                    </span>


                                                    <small class="text-secondary">

                                                        <i class="bi bi-clock"></i>

                                                        <?php
                                                        echo (int)$item["preparation_time"];
                                                        ?> min

                                                    </small>

                                                </div>


                                                <!-- QUANTITY -->

                                                <div class="quantity-control">

                                                    <label
                                                        for="quantity_<?php echo (int)$item["id"]; ?>"
                                                        class="small fw-semibold"
                                                    >
                                                        Qty
                                                    </label>


                                                    <input
                                                        type="number"
                                                        min="0"
                                                        value="0"
                                                        class="form-control quantity-input"
                                                        id="quantity_<?php echo (int)$item["id"]; ?>"
                                                        data-id="<?php echo (int)$item["id"]; ?>"
                                                        data-name="<?php echo htmlspecialchars($item["name"], ENT_QUOTES); ?>"
                                                        data-price="<?php echo htmlspecialchars($item["price"]); ?>"
                                                    >

                                                </div>


                                            </div>

                                        </div>

                                    </div>


                                <?php endforeach; ?>


                            </div>


                        <?php else: ?>


                            <div class="text-center py-5">

                                <i
                                    class="bi bi-egg-fried"
                                    style="font-size:50px;"
                                ></i>

                                <h5 class="mt-3">
                                    No food items available
                                </h5>

                                <p class="text-secondary">
                                    Please add available menu items first.
                                </p>

                            </div>


                        <?php endif; ?>

                    </div>

                </div>


                <!-- =================================================
                     ORDER SUMMARY
                     ================================================= -->

                <div class="col-lg-4">

                    <div class="customer-panel order-summary">

                        <span class="panel-label">
                            ORDER SUMMARY
                        </span>


                        <h4 class="mt-2">
                            Table
                            <?php
                            echo htmlspecialchars(
                                $table["table_number"]
                            );
                            ?>
                        </h4>


                        <hr>


                        <div id="summaryItems">

                            <div
                                id="emptySummary"
                                class="text-center text-secondary py-4"
                            >

                                <i
                                    class="bi bi-cart3"
                                    style="font-size:35px;"
                                ></i>

                                <p class="mt-2 mb-0">
                                    No items selected yet.
                                </p>

                            </div>

                        </div>


                        <div class="summary-total">

                            <span>
                                Total
                            </span>

                            <span id="orderTotal">
                                0.00
                            </span>

                        </div>


                        <button
                            type="button"
                            id="sendOrderButton"
                            class="btn btn-warning w-100 py-3 mt-4 fw-semibold"
                            disabled
                        >

                            <i class="bi bi-send me-2"></i>

                            Send to Kitchen

                        </button>


                        <a
                            href="tables.php"
                            class="btn btn-outline-secondary w-100 mt-2"
                        >

                            <i class="bi bi-arrow-left me-2"></i>

                            Back to Tables

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </main>

</div>


<!-- =====================================================
     JAVASCRIPT
     ===================================================== -->

<script>

const quantityInputs =
    document.querySelectorAll(".quantity-input");

const summaryItems =
    document.getElementById("summaryItems");

const emptySummary =
    document.getElementById("emptySummary");

const orderTotal =
    document.getElementById("orderTotal");

const sendOrderButton =
    document.getElementById("sendOrderButton");


function updateOrderSummary() {

    let total = 0;

    let selectedItems = [];


    quantityInputs.forEach(function(input) {

        const quantity =
            parseInt(input.value) || 0;

        const price =
            parseFloat(input.dataset.price) || 0;

        const name =
            input.dataset.name;

        const id =
            input.dataset.id;


        if (quantity > 0) {

            const itemTotal =
                quantity * price;

            total += itemTotal;

            selectedItems.push({

                id: id,

                name: name,

                quantity: quantity,

                price: price,

                total: itemTotal

            });

        }

    });


    summaryItems.innerHTML = "";


    if (selectedItems.length === 0) {

        summaryItems.innerHTML = `
            <div
                class="text-center text-secondary py-4"
            >
                <i
                    class="bi bi-cart3"
                    style="font-size:35px;"
                ></i>

                <p class="mt-2 mb-0">
                    No items selected yet.
                </p>
            </div>
        `;

        sendOrderButton.disabled = true;

    } else {

        selectedItems.forEach(function(item) {

            const div =
                document.createElement("div");

            div.className =
                "summary-item";

            div.innerHTML = `

                <div>

                    <div class="summary-item-name">
                        ${item.name}
                    </div>

                    <small class="text-secondary">
                        ${item.quantity} ×
                        ${item.price.toFixed(2)}
                    </small>

                </div>

                <strong>
                    ${item.total.toFixed(2)}
                </strong>

            `;

            summaryItems.appendChild(div);

        });

        sendOrderButton.disabled = false;

    }


    orderTotal.textContent =
        total.toFixed(2);

}


quantityInputs.forEach(function(input) {

    input.addEventListener(
        "input",
        updateOrderSummary
    );

});


sendOrderButton.addEventListener(
    "click",
    function() {

        let selectedItems = [];

        let total = 0;


        quantityInputs.forEach(function(input) {

            const quantity =
                parseInt(input.value) || 0;

            const price =
                parseFloat(input.dataset.price) || 0;

            const id =
                parseInt(input.dataset.id) || 0;


            if (quantity > 0) {

                selectedItems.push({

                    id: id,

                    quantity: quantity,

                    price: price

                });

                total += quantity * price;

            }

        });


        if (selectedItems.length === 0) {

            alert("Please select at least one food item.");

            return;

        }


        if (
            !confirm(
                "Send this order to the kitchen?"
            )
        ) {

            return;

        }


        const form =
            document.createElement("form");

        form.method = "POST";

        form.action = "save-order.php";


        const tableInput =
            document.createElement("input");

        tableInput.type = "hidden";

        tableInput.name = "table_id";

        tableInput.value =
            "<?php echo (int)$table_id; ?>";

        form.appendChild(tableInput);


        const itemsInput =
            document.createElement("input");

        itemsInput.type = "hidden";

        itemsInput.name = "items";

        itemsInput.value =
            JSON.stringify(selectedItems);

        form.appendChild(itemsInput);


        const totalInput =
            document.createElement("input");

        totalInput.type = "hidden";

        totalInput.name = "total_amount";

        totalInput.value =
            total.toFixed(2);

        form.appendChild(totalInput);


        document.body.appendChild(form);

        form.submit();

    }
);


</script>


</body>

</html>