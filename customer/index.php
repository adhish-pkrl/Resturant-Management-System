<?php

session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["user_role"] !== "customer") {
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

    <meta
        name="description"
        content="Customer Dashboard - RMS Restaurant"
    >

    <title>Customer Dashboard - RMS Restaurant</title>

    <!-- Bootstrap CSS -->

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
    href="/restaurant-management-system/assets/css/style.css?v=1"
    >

</head>

<body class="customer-page">

<div class="customer-layout">

    <!-- =================================================
         SIDEBAR
         ================================================= -->

    <aside class="customer-sidebar" id="customerSidebar">

        <div class="customer-brand">

            <a href="index.php">

                <span>RMS</span> Restaurant

            </a>

        </div>


        <div class="customer-profile">

            <div class="customer-avatar">

                <?php echo strtoupper(substr($user_name, 0, 1)); ?>

            </div>

            <div>

                <strong>
                    <?php echo htmlspecialchars($user_name); ?>
                </strong>

                <small>
                    Customer
                </small>

            </div>

        </div>


        <nav class="customer-nav">

            <a
                href="index.php"
                class="customer-nav-link active"
            >
                <i class="bi bi-grid-1x2-fill"></i>
                Dashboard
            </a>

            <a
                href="menu.php"
                class="customer-nav-link"
            >
                <i class="bi bi-egg-fried"></i>
                Browse Menu
            </a>

            <a
                href="cart.php"
                class="customer-nav-link"
            >
                <i class="bi bi-cart3"></i>
                My Cart
                <span class="nav-badge">0</span>
            </a>

            <a
                href="orders.php"
                class="customer-nav-link"
            >
                <i class="bi bi-receipt"></i>
                My Orders
            </a>

            <a
                href="reservations.php"
                class="customer-nav-link"
            >
                <i class="bi bi-calendar-check"></i>
                Reservations
            </a>

            <a
                href="#"
                class="customer-nav-link"
            >
                <i class="bi bi-star"></i>
                Reviews
            </a>

            <a
                href="#"
                class="customer-nav-link"
            >
                <i class="bi bi-person"></i>
                My Profile
            </a>

        </nav>


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


    <!-- =================================================
         MAIN AREA
         ================================================= -->

    <main class="customer-main">


        <!-- TOPBAR -->

        <header class="customer-topbar">

            <div class="d-flex align-items-center gap-3">

                <button
                    type="button"
                    class="customer-menu-toggle"
                    id="customerMenuToggle"
                    aria-label="Open menu"
                >
                    <i class="bi bi-list"></i>
                </button>

                <div>

                    <small class="text-secondary">
                        Customer Panel
                    </small>

                    <h5 class="mb-0 fw-bold">
                        Dashboard
                    </h5>

                </div>

            </div>


            <div class="customer-topbar-right">

                <button
                    type="button"
                    class="customer-icon-btn"
                    title="Notifications"
                >

                    <i class="bi bi-bell"></i>

                    <span class="notification-dot"></span>

                </button>


                <div class="customer-top-profile">

                    <div class="customer-top-avatar">

                        <?php echo strtoupper(substr($user_name, 0, 1)); ?>

                    </div>

                    <div class="d-none d-md-block">

                        <strong>
                            <?php echo htmlspecialchars($user_name); ?>
                        </strong>

                        <small>
                            Customer
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
                        WELCOME BACK
                    </span>

                    <h1>
                        Hello,
                        <?php echo htmlspecialchars($user_name); ?>! 👋
                    </h1>

                    <p>
                        What would you like to enjoy today?
                    </p>

                </div>


                <a
                    href="menu.php"
                    class="btn btn-warning px-4"
                >
                    <i class="bi bi-egg-fried me-2"></i>
                    Explore Menu
                </a>

            </section>


            <!-- STATISTICS -->

            <section class="row g-4 mb-4">

                <div class="col-12 col-md-4">

                    <div class="customer-stat-card">

                        <div class="stat-icon">
                            <i class="bi bi-bag-check"></i>
                        </div>

                        <div>

                            <span>
                                Total Orders
                            </span>

                            <h3>
                                0
                            </h3>

                            <small>
                                Orders placed
                            </small>

                        </div>

                    </div>

                </div>


                <div class="col-12 col-md-4">

                    <div class="customer-stat-card">

                        <div class="stat-icon">
                            <i class="bi bi-clock-history"></i>
                        </div>

                        <div>

                            <span>
                                Active Orders
                            </span>

                            <h3>
                                0
                            </h3>

                            <small>
                                Currently processing
                            </small>

                        </div>

                    </div>

                </div>


                <div class="col-12 col-md-4">

                    <div class="customer-stat-card">

                        <div class="stat-icon">
                            <i class="bi bi-wallet2"></i>
                        </div>

                        <div>

                            <span>
                                Total Spent
                            </span>

                            <h3>
                                Rs. 0
                            </h3>

                            <small>
                                Lifetime spending
                            </small>

                        </div>

                    </div>

                </div>

            </section>


            <!-- MAIN GRID -->

            <section class="row g-4">


                <!-- ACTIVE ORDER -->

                <div class="col-lg-7">

                    <div class="customer-panel h-100">

                        <div class="customer-panel-header">

                            <div>

                                <span class="panel-label">
                                    ORDER STATUS
                                </span>

                                <h4>
                                    Your Active Order
                                </h4>

                            </div>

                            <span class="status status-warning">
                                No Active Order
                            </span>

                        </div>


                        <div class="empty-order-state">

                            <div class="empty-order-icon">

                                <i class="bi bi-receipt-cutoff"></i>

                            </div>

                            <h5>
                                No active orders
                            </h5>

                            <p>
                                Your current orders will appear here.
                            </p>

                            <a
                                href="menu.php"
                                class="btn btn-warning"
                            >
                                Order Something Delicious
                            </a>

                        </div>

                    </div>

                </div>


                <!-- QUICK ACTIONS -->

                <div class="col-lg-5">

                    <div class="customer-panel h-100">

                        <div class="customer-panel-header">

                            <div>

                                <span class="panel-label">
                                    QUICK ACTIONS
                                </span>

                                <h4>
                                    What would you like to do?
                                </h4>

                            </div>

                        </div>


                        <div class="quick-actions">


                            <a
                                href="menu.php"
                                class="quick-action"
                            >

                                <div class="quick-action-icon">

                                    <i class="bi bi-egg-fried"></i>

                                </div>

                                <div>

                                    <strong>
                                        Browse Menu
                                    </strong>

                                    <small>
                                        Explore our dishes
                                    </small>

                                </div>

                                <i class="bi bi-chevron-right"></i>

                            </a>


                            <a
                                href="cart.php"
                                class="quick-action"
                            >

                                <div class="quick-action-icon">

                                    <i class="bi bi-cart3"></i>

                                </div>

                                <div>

                                    <strong>
                                        View Cart
                                    </strong>

                                    <small>
                                        Check your selected items
                                    </small>

                                </div>

                                <i class="bi bi-chevron-right"></i>

                            </a>


                            <a
                                href="reservations.php"
                                class="quick-action"
                            >

                                <div class="quick-action-icon">

                                    <i class="bi bi-calendar-check"></i>

                                </div>

                                <div>

                                    <strong>
                                        Book a Table
                                    </strong>

                                    <small>
                                        Reserve your table
                                    </small>

                                </div>

                                <i class="bi bi-chevron-right"></i>

                            </a>

                        </div>

                    </div>

                </div>


                <!-- RECENT ORDERS -->

                <div class="col-12">

                    <div class="customer-panel">

                        <div class="customer-panel-header">

                            <div>

                                <span class="panel-label">
                                    ORDER HISTORY
                                </span>

                                <h4>
                                    Recent Orders
                                </h4>

                            </div>

                            <a
                                href="orders.php"
                                class="view-all-link"
                            >
                                View All
                                <i class="bi bi-arrow-right"></i>
                            </a>

                        </div>


                        <div class="empty-table-state">

                            <i class="bi bi-receipt"></i>

                            <h6>
                                No orders yet
                            </h6>

                            <p>
                                Your order history will appear here
                                after you place your first order.
                            </p>

                        </div>

                    </div>

                </div>


                <!-- RECOMMENDED -->

                <div class="col-12">

                    <div class="customer-panel">

                        <div class="customer-panel-header">

                            <div>

                                <span class="panel-label">
                                    DISCOVER
                                </span>

                                <h4>
                                    Popular Today
                                </h4>

                            </div>

                            <a
                                href="menu.php"
                                class="view-all-link"
                            >
                                View Menu
                                <i class="bi bi-arrow-right"></i>
                            </a>

                        </div>


                        <div class="row g-4">

                            <!-- ITEM 1 -->

                            <div class="col-md-6 col-lg-3">

                                <div class="mini-food-card">

                                    <div class="mini-food-image">
                                        <i class="bi bi-egg-fried"></i>
                                    </div>

                                    <div class="p-3">

                                        <span class="small text-secondary">
                                            Pizza
                                        </span>

                                        <h6 class="fw-bold mt-1 mb-2">
                                            Margherita Pizza
                                        </h6>

                                        <div class="d-flex justify-content-between align-items-center">

                                            <strong>
                                                Rs. 450
                                            </strong>

                                            <button
                                                type="button"
                                                class="btn btn-sm btn-warning"
                                            >
                                                Add
                                            </button>

                                        </div>

                                    </div>

                                </div>

                            </div>


                            <!-- ITEM 2 -->

                            <div class="col-md-6 col-lg-3">

                                <div class="mini-food-card">

                                    <div class="mini-food-image">
                                        <i class="bi bi-cup-hot"></i>
                                    </div>

                                    <div class="p-3">

                                        <span class="small text-secondary">
                                            Burger
                                        </span>

                                        <h6 class="fw-bold mt-1 mb-2">
                                            Chicken Burger
                                        </h6>

                                        <div class="d-flex justify-content-between align-items-center">

                                            <strong>
                                                Rs. 350
                                            </strong>

                                            <button
                                                type="button"
                                                class="btn btn-sm btn-warning"
                                            >
                                                Add
                                            </button>

                                        </div>

                                    </div>

                                </div>

                            </div>


                            <!-- ITEM 3 -->

                            <div class="col-md-6 col-lg-3">

                                <div class="mini-food-card">

                                    <div class="mini-food-image">
                                        <i class="bi bi-basket2"></i>
                                    </div>

                                    <div class="p-3">

                                        <span class="small text-secondary">
                                            Momo
                                        </span>

                                        <h6 class="fw-bold mt-1 mb-2">
                                            Chicken Momo
                                        </h6>

                                        <div class="d-flex justify-content-between align-items-center">

                                            <strong>
                                                Rs. 250
                                            </strong>

                                            <button
                                                type="button"
                                                class="btn btn-sm btn-warning"
                                            >
                                                Add
                                            </button>

                                        </div>

                                    </div>

                                </div>

                            </div>


                            <!-- ITEM 4 -->

                            <div class="col-md-6 col-lg-3">

                                <div class="mini-food-card">

                                    <div class="mini-food-image">
                                        <i class="bi bi-cup-straw"></i>
                                    </div>

                                    <div class="p-3">

                                        <span class="small text-secondary">
                                            Drinks
                                        </span>

                                        <h6 class="fw-bold mt-1 mb-2">
                                            Cold Coffee
                                        </h6>

                                        <div class="d-flex justify-content-between align-items-center">

                                            <strong>
                                                Rs. 180
                                            </strong>

                                            <button
                                                type="button"
                                                class="btn btn-sm btn-warning"
                                            >
                                                Add
                                            </button>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </section>

        </div>

    </main>

</div>


<!-- =================================================
     JAVASCRIPT
     ================================================= -->

<script>

document.addEventListener("DOMContentLoaded", function () {

    const toggleButton =
        document.getElementById("customerMenuToggle");

    const sidebar =
        document.getElementById("customerSidebar");

    if (toggleButton && sidebar) {

        toggleButton.addEventListener("click", function () {

            sidebar.classList.toggle("show");

        });

    }

});

</script>


</body>

</html>