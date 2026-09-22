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


$user_name = $_SESSION["user_name"];


/* =====================================================
   GET RESTAURANT TABLES
   ===================================================== */

$sql = "
    SELECT
        id,
        table_number,
        capacity,
        status
    FROM restaurant_tables
    ORDER BY id ASC
";

$result = $conn->query($sql);


/* =====================================================
   COUNT TABLE STATUS
   ===================================================== */

$total_tables = 0;
$available_tables = 0;
$occupied_tables = 0;


if ($result) {

    $tables = [];

    while ($row = $result->fetch_assoc()) {

        $tables[] = $row;

        $total_tables++;

        if ($row["status"] === "available") {

            $available_tables++;

        } elseif ($row["status"] === "occupied") {

            $occupied_tables++;

        }

    }

} else {

    $tables = [];

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

    <title>Tables - Waiter - RMS</title>


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
        href="../assets/css/style.css?v=2"
    >

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
                    class="customer-nav-link active"
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
                    Restaurant Tables
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


        <!-- =====================================================
             CONTENT
             ===================================================== -->

        <div class="customer-content">


            <!-- PAGE HEADER -->

            <section class="customer-welcome">

                <div>

                    <span class="welcome-label">
                        TABLE MANAGEMENT
                    </span>

                    <h1>
                        Choose a table 🍽️
                    </h1>

                    <p>
                        Select an available table to start
                        taking a new order.
                    </p>

                </div>


                <a
                    href="#tables"
                    class="btn btn-warning"
                >

                    <i class="bi bi-table me-2"></i>

                    View Tables

                </a>

            </section>


            <!-- =================================================
                 TABLE STATISTICS
                 ================================================= -->

            <div class="row g-4 mb-4">


                <!-- TOTAL -->

                <div class="col-md-4">

                    <div class="customer-stat-card">

                        <div class="customer-stat-icon">

                            <i class="bi bi-grid-3x3-gap"></i>

                        </div>

                        <div>

                            <span>
                                Total Tables
                            </span>

                            <h3>
                                <?php
                                echo $total_tables;
                                ?>
                            </h3>

                            <small>
                                Restaurant tables
                            </small>

                        </div>

                    </div>

                </div>


                <!-- AVAILABLE -->

                <div class="col-md-4">

                    <div class="customer-stat-card">

                        <div class="customer-stat-icon">

                            <i class="bi bi-check-circle"></i>

                        </div>

                        <div>

                            <span>
                                Available
                            </span>

                            <h3>
                                <?php
                                echo $available_tables;
                                ?>
                            </h3>

                            <small>
                                Ready for orders
                            </small>

                        </div>

                    </div>

                </div>


                <!-- OCCUPIED -->

                <div class="col-md-4">

                    <div class="customer-stat-card">

                        <div class="customer-stat-icon">

                            <i class="bi bi-person-fill"></i>

                        </div>

                        <div>

                            <span>
                                Occupied
                            </span>

                            <h3>
                                <?php
                                echo $occupied_tables;
                                ?>
                            </h3>

                            <small>
                                Currently in use
                            </small>

                        </div>

                    </div>

                </div>

            </div>


            <!-- =================================================
                 TABLES
                 ================================================= -->

            <section
                class="customer-panel"
                id="tables"
            >

                <div class="customer-panel-header">

                    <div>

                        <span class="panel-label">
                            RESTAURANT FLOOR
                        </span>

                        <h4>
                            All Tables
                        </h4>

                    </div>


                    <span class="status status-success">

                        <?php
                        echo $available_tables;
                        ?>

                        Available

                    </span>

                </div>


                <?php if (count($tables) > 0): ?>

                    <div class="row g-4">


                        <?php foreach ($tables as $table): ?>


                            <div class="col-sm-6 col-lg-4 col-xl-3">


                                <div
                                    class="waiter-table-card
                                    <?php
                                    echo $table["status"] === "available"
                                        ? "table-available"
                                        : "table-occupied";
                                    ?>"
                                >


                                    <!-- TABLE ICON -->

                                    <div class="waiter-table-icon">

                                        <i class="bi bi-table"></i>

                                    </div>


                                    <!-- TABLE NUMBER -->

                                    <h4>

                                        <?php
                                        echo htmlspecialchars(
                                            $table["table_number"]
                                        );
                                        ?>

                                    </h4>


                                    <!-- CAPACITY -->

                                    <p>

                                        <i class="bi bi-people"></i>

                                        Capacity:
                                        <?php
                                        echo (int)$table["capacity"];
                                        ?>

                                    </p>


                                    <!-- STATUS -->

                                    <?php if (
                                        $table["status"] === "available"
                                    ): ?>

                                        <span
                                            class="table-status table-status-available"
                                        >

                                            <i class="bi bi-circle-fill"></i>

                                            Available

                                        </span>


                                        <a
                                            href="new-order.php?table_id=<?php echo (int)$table["id"]; ?>"
                                            class="btn btn-warning w-100 mt-3"
                                        >

                                            <i class="bi bi-plus-circle me-1"></i>

                                            Start Order

                                        </a>


                                    <?php else: ?>

                                        <span
                                            class="table-status table-status-occupied"
                                        >

                                            <i class="bi bi-circle-fill"></i>

                                            Occupied

                                        </span>


                                        <button
                                            type="button"
                                            class="btn btn-outline-secondary w-100 mt-3"
                                            disabled
                                        >

                                            Table Busy

                                        </button>

                                    <?php endif; ?>


                                </div>

                            </div>


                        <?php endforeach; ?>


                    </div>


                <?php else: ?>


                    <div class="empty-table-state">

                        <i class="bi bi-table"></i>

                        <h6>
                            No tables found
                        </h6>

                        <p>
                            Restaurant tables have not been added yet.
                        </p>

                    </div>


                <?php endif; ?>

            </section>

        </div>

    </main>

</div>


</body>

</html>