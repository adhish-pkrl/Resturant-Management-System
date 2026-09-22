<?php

session_start();

if (
    !isset($_SESSION["user_id"]) ||
    $_SESSION["user_role"] !== "waiter"
) {
    header("Location: ../auth/login.php");
    exit();
}

$user_name = $_SESSION["user_name"];

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Waiter Dashboard - RMS</title>

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
        href="../assets/css/style.css"
    >

</head>


<body class="customer-page">


<div class="customer-layout">


    <!-- SIDEBAR -->

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
        class="customer-nav-link active"
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


    <!-- MAIN -->

    <main class="customer-main">


        <!-- TOPBAR -->

        <header class="customer-topbar">

            <div>

                <small class="text-secondary">
                    Waiter Panel
                </small>

                <h5 class="mb-0 fw-bold">
                    Restaurant Operations
                </h5>

            </div>


            <div class="customer-topbar-right">

                <button
                    class="customer-icon-btn"
                    type="button"
                >

                    <i class="bi bi-bell"></i>

                </button>


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


            <!-- WELCOME -->

            <section class="customer-welcome">

                <div>

                    <span class="welcome-label">
                        WAITER DASHBOARD
                    </span>

                    <h1>
                        Welcome back,
                        <?php
                        echo htmlspecialchars($user_name);
                        ?>! 👋
                    </h1>

                    <p>
                        Manage tables, take orders and
                        keep track of food preparation.
                    </p>

                </div>

            </section>


            <!-- STAT CARDS -->

            <div class="row g-4 mb-4">


                <!-- TABLES -->

                <div class="col-md-6 col-xl-3">

                    <div class="customer-stat-card">

                        <div class="customer-stat-icon">

                            <i class="bi bi-table"></i>

                        </div>

                        <div>

                            <span>
                                Tables
                            </span>

                            <h3>
                                0
                            </h3>

                            <small>
                                Available / occupied
                            </small>

                        </div>

                    </div>

                </div>


                <!-- ACTIVE ORDERS -->

                <div class="col-md-6 col-xl-3">

                    <div class="customer-stat-card">

                        <div class="customer-stat-icon">

                            <i class="bi bi-receipt"></i>

                        </div>

                        <div>

                            <span>
                                Active Orders
                            </span>

                            <h3>
                                0
                            </h3>

                            <small>
                                Orders you created
                            </small>

                        </div>

                    </div>

                </div>


                <!-- PREPARING -->

                <div class="col-md-6 col-xl-3">

                    <div class="customer-stat-card">

                        <div class="customer-stat-icon">

                            <i class="bi bi-fire"></i>

                        </div>

                        <div>

                            <span>
                                Preparing
                            </span>

                            <h3>
                                0
                            </h3>

                            <small>
                                Kitchen orders
                            </small>

                        </div>

                    </div>

                </div>


                <!-- READY -->

                <div class="col-md-6 col-xl-3">

                    <div class="customer-stat-card">

                        <div class="customer-stat-icon">

                            <i class="bi bi-check-circle"></i>

                        </div>

                        <div>

                            <span>
                                Ready
                            </span>

                            <h3>
                                0
                            </h3>

                            <small>
                                Ready to serve
                            </small>

                        </div>

                    </div>

                </div>

            </div>


            <!-- QUICK ACTIONS -->

            <div class="customer-panel">

                <div class="d-flex justify-content-between align-items-center mb-4">

                    <div>

                        <span class="welcome-label">
                            QUICK ACTIONS
                        </span>

                        <h3 class="mt-2 mb-0">
                            What would you like to do?
                        </h3>

                    </div>

                </div>


                <div class="row g-4">


                    <!-- NEW ORDER -->

                    <div class="col-md-6 col-xl-4">

                        <a
                            href="#"
                            class="text-decoration-none"
                        >

                            <div class="p-4 border rounded-3 h-100">

                                <i
                                    class="bi bi-plus-circle text-warning"
                                    style="font-size: 35px;"
                                ></i>

                                <h5 class="mt-3">
                                    Take New Order
                                </h5>

                                <p class="text-secondary mb-0">
                                    Select a table, choose food
                                    and set quantities.
                                </p>

                            </div>

                        </a>

                    </div>


                    <!-- ACTIVE ORDERS -->

                    <div class="col-md-6 col-xl-4">

                        <a
                            href="#"
                            class="text-decoration-none"
                        >

                            <div class="p-4 border rounded-3 h-100">

                                <i
                                    class="bi bi-receipt text-warning"
                                    style="font-size: 35px;"
                                ></i>

                                <h5 class="mt-3">
                                    Active Orders
                                </h5>

                                <p class="text-secondary mb-0">
                                    Check the orders currently
                                    being prepared.
                                </p>

                            </div>

                        </a>

                    </div>


                    <!-- READY ORDERS -->

                    <div class="col-md-6 col-xl-4">

                        <a
                            href="#"
                            class="text-decoration-none"
                        >

                            <div class="p-4 border rounded-3 h-100">

                                <i
                                    class="bi bi-bell text-warning"
                                    style="font-size: 35px;"
                                ></i>

                                <h5 class="mt-3">
                                    Ready Orders
                                </h5>

                                <p class="text-secondary mb-0">
                                    See food that is ready
                                    to be served.
                                </p>

                            </div>

                        </a>

                    </div>

                </div>

            </div>


            <!-- WORKFLOW -->

            <div class="customer-panel mt-4">

                <span class="welcome-label">
                    ORDER WORKFLOW
                </span>

                <h3 class="mt-2">
                    From table to kitchen
                </h3>


                <div class="row text-center mt-4">


                    <div class="col-md">

                        <i
                            class="bi bi-table"
                            style="font-size:35px;"
                        ></i>

                        <h6 class="mt-2">
                            1. Select Table
                        </h6>

                    </div>


                    <div class="col-md">

                        <i
                            class="bi bi-egg-fried"
                            style="font-size:35px;"
                        ></i>

                        <h6 class="mt-2">
                            2. Select Food
                        </h6>

                    </div>


                    <div class="col-md">

                        <i
                            class="bi bi-send"
                            style="font-size:35px;"
                        ></i>

                        <h6 class="mt-2">
                            3. Send to Kitchen
                        </h6>

                    </div>


                    <div class="col-md">

                        <i
                            class="bi bi-fire"
                            style="font-size:35px;"
                        ></i>

                        <h6 class="mt-2">
                            4. Kitchen Prepares
                        </h6>

                    </div>


                    <div class="col-md">

                        <i
                            class="bi bi-check-circle"
                            style="font-size:35px;"
                        ></i>

                        <h6 class="mt-2">
                            5. Serve
                        </h6>

                    </div>

                </div>

            </div>

        </div>

    </main>

</div>


</body>

</html>