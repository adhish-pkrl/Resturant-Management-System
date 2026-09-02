<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta
        name="description"
        content="Restaurant Management System - Fresh food, easy ordering and table reservations."
    >

    <title>RMS Restaurant</title>

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

    <!-- Our CSS -->
    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>

<!-- =====================================================
     NAVBAR
     ===================================================== -->

<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">

    <div class="container">

        <a class="navbar-brand fw-bold fs-4" href="index.php">
            <span class="text-warning">RMS</span> Restaurant
        </a>

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#mainNavbar"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">

            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">

                <li class="nav-item">
                    <a class="nav-link active" href="index.php">
                        Home
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#menu">
                        Menu
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#about">
                        About
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#contact">
                        Contact
                    </a>
                </li>

                <li class="nav-item ms-lg-2">
                    <a
                        href="auth/login.php"
                        class="btn btn-warning px-4"
                    >
                        Login
                    </a>
                </li>

            </ul>

        </div>

    </div>

</nav>


<!-- =====================================================
     HERO SECTION
     ===================================================== -->

<section class="rms-hero">

    <div class="container">

        <div class="row align-items-center min-vh-75">

            <div class="col-lg-6">

                <span class="badge bg-warning text-dark px-3 py-2 mb-3">
                    Fresh • Delicious • Simple
                </span>

                <h1 class="display-3 fw-bold mb-4">
                    Good Food.
                    <span class="text-warning">Great Moments.</span>
                </h1>

                <p class="lead text-secondary mb-4">
                    Enjoy freshly prepared meals, easy online ordering,
                    table reservations and a better restaurant experience.
                </p>

                <div class="d-flex flex-wrap gap-3">

                    <a
                        href="#menu"
                        class="btn btn-warning btn-lg px-4"
                    >
                        <i class="bi bi-egg-fried me-2"></i>
                        Explore Menu
                    </a>

                    <a
                        href="auth/login.php"
                        class="btn btn-dark btn-lg px-4"
                    >
                        <i class="bi bi-calendar-check me-2"></i>
                        Book a Table
                    </a>

                </div>

            </div>

            <div class="col-lg-6 mt-5 mt-lg-0">

                <div class="hero-food-card">

                    <div class="hero-food-circle">

                        <i class="bi bi-egg-fried"></i>

                    </div>

                    <div class="hero-food-info">

                        <small class="text-secondary">
                            Today's Special
                        </small>

                        <h3 class="fw-bold mb-1">
                            Chef's Special
                        </h3>

                        <p class="text-secondary mb-0">
                            Freshly prepared with quality ingredients.
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>


<!-- =====================================================
     FEATURES
     ===================================================== -->

<section class="py-5 bg-white">

    <div class="container">

        <div class="row g-4">

            <div class="col-md-4">

                <div class="feature-card h-100">

                    <div class="feature-icon">
                        <i class="bi bi-stars"></i>
                    </div>

                    <h4>Fresh Ingredients</h4>

                    <p class="text-secondary mb-0">
                        Carefully selected ingredients prepared fresh
                        for every order.
                    </p>

                </div>

            </div>

            <div class="col-md-4">

                <div class="feature-card h-100">

                    <div class="feature-icon">
                        <i class="bi bi-lightning-charge"></i>
                    </div>

                    <h4>Fast Service</h4>

                    <p class="text-secondary mb-0">
                        Simple ordering and efficient service from
                        kitchen to table.
                    </p>

                </div>

            </div>

            <div class="col-md-4">

                <div class="feature-card h-100">

                    <div class="feature-icon">
                        <i class="bi bi-phone"></i>
                    </div>

                    <h4>Easy Ordering</h4>

                    <p class="text-secondary mb-0">
                        Browse the menu, order your food and track
                        your order with ease.
                    </p>

                </div>

            </div>

        </div>

    </div>

</section>


<!-- =====================================================
     MENU
     ===================================================== -->

<section id="menu" class="py-5">

    <div class="container">

        <div class="text-center mb-5">

            <span class="text-warning fw-semibold">
                OUR MENU
            </span>

            <h2 class="display-6 fw-bold mt-2">
                Popular Dishes
            </h2>

            <p class="text-secondary">
                Some customer favourites from our kitchen.
            </p>

        </div>

        <div class="row g-4">

            <!-- Food Card 1 -->

            <div class="col-md-6 col-lg-3">

                <div class="food-card h-100">

                    <div class="food-placeholder">
                        <i class="bi bi-egg-fried"></i>
                    </div>

                    <div class="p-3">

                        <div class="d-flex justify-content-between align-items-start">

                            <h5 class="fw-bold mb-1">
                                Margherita Pizza
                            </h5>

                            <span class="badge bg-success">
                                Veg
                            </span>

                        </div>

                        <p class="small text-secondary">
                            Fresh tomato, mozzarella and herbs.
                        </p>

                        <div class="d-flex justify-content-between align-items-center">

                            <strong>
                                Rs. 450
                            </strong>

                            <button class="btn btn-sm btn-warning">
                                Add
                            </button>

                        </div>

                    </div>

                </div>

            </div>


            <!-- Food Card 2 -->

            <div class="col-md-6 col-lg-3">

                <div class="food-card h-100">

                    <div class="food-placeholder">
                        <i class="bi bi-cup-hot"></i>
                    </div>

                    <div class="p-3">

                        <div class="d-flex justify-content-between align-items-start">

                            <h5 class="fw-bold mb-1">
                                Chicken Burger
                            </h5>

                            <span class="badge bg-danger">
                                Non-Veg
                            </span>

                        </div>

                        <p class="small text-secondary">
                            Crispy chicken with fresh vegetables.
                        </p>

                        <div class="d-flex justify-content-between align-items-center">

                            <strong>
                                Rs. 350
                            </strong>

                            <button class="btn btn-sm btn-warning">
                                Add
                            </button>

                        </div>

                    </div>

                </div>

            </div>


            <!-- Food Card 3 -->

            <div class="col-md-6 col-lg-3">

                <div class="food-card h-100">

                    <div class="food-placeholder">
                        <i class="bi bi-basket2"></i>
                    </div>

                    <div class="p-3">

                        <div class="d-flex justify-content-between align-items-start">

                            <h5 class="fw-bold mb-1">
                                Chicken Momo
                            </h5>

                            <span class="badge bg-danger">
                                Non-Veg
                            </span>

                        </div>

                        <p class="small text-secondary">
                            Steamed momos served with spicy sauce.
                        </p>

                        <div class="d-flex justify-content-between align-items-center">

                            <strong>
                                Rs. 250
                            </strong>

                            <button class="btn btn-sm btn-warning">
                                Add
                            </button>

                        </div>

                    </div>

                </div>

            </div>


            <!-- Food Card 4 -->

            <div class="col-md-6 col-lg-3">

                <div class="food-card h-100">

                    <div class="food-placeholder">
                        <i class="bi bi-cup-straw"></i>
                    </div>

                    <div class="p-3">

                        <div class="d-flex justify-content-between align-items-start">

                            <h5 class="fw-bold mb-1">
                                Cold Coffee
                            </h5>

                            <span class="badge bg-success">
                                Veg
                            </span>

                        </div>

                        <p class="small text-secondary">
                            Smooth chilled coffee with creamy foam.
                        </p>

                        <div class="d-flex justify-content-between align-items-center">

                            <strong>
                                Rs. 180
                            </strong>

                            <button class="btn btn-sm btn-warning">
                                Add
                            </button>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>


<!-- =====================================================
     ABOUT
     ===================================================== -->

<section id="about" class="py-5 bg-white">

    <div class="container">

        <div class="row align-items-center g-5">

            <div class="col-lg-6">

                <div class="about-visual">

                    <i class="bi bi-shop"></i>

                </div>

            </div>

            <div class="col-lg-6">

                <span class="text-warning fw-semibold">
                    ABOUT US
                </span>

                <h2 class="display-6 fw-bold mt-2 mb-4">
                    A Better Restaurant Experience
                </h2>

                <p class="text-secondary">
                    Our Restaurant Management System brings food,
                    customers and restaurant operations together in
                    one simple platform.
                </p>

                <p class="text-secondary">
                    Customers can browse the menu, place orders and
                    reserve tables, while restaurant staff can manage
                    orders and daily operations efficiently.
                </p>

                <div class="row mt-4">

                    <div class="col-6">
                        <h3 class="fw-bold">100+</h3>
                        <p class="text-secondary">Happy Customers</p>
                    </div>

                    <div class="col-6">
                        <h3 class="fw-bold">25+</h3>
                        <p class="text-secondary">Menu Items</p>
                    </div>

                </div>

            </div>

        </div>

    </div>

</section>


<!-- =====================================================
     CALL TO ACTION
     ===================================================== -->

<section class="py-5">

    <div class="container">

        <div class="cta-section text-center">

            <span class="badge bg-warning text-dark mb-3">
                READY TO ORDER?
            </span>

            <h2 class="display-6 fw-bold">
                Your next great meal is waiting.
            </h2>

            <p class="text-secondary mb-4">
                Explore our menu and enjoy a simple ordering experience.
            </p>

            <a
                href="auth/login.php"
                class="btn btn-dark btn-lg px-5"
            >
                Get Started
            </a>

        </div>

    </div>

</section>


<!-- =====================================================
     FOOTER
     ===================================================== -->

<footer id="contact" class="bg-dark text-white py-5">

    <div class="container">

        <div class="row g-4">

            <div class="col-lg-5">

                <h4 class="fw-bold">
                    <span class="text-warning">RMS</span> Restaurant
                </h4>

                <p class="text-white-50">
                    Fresh food, easy ordering and a better
                    restaurant experience.
                </p>

            </div>

            <div class="col-md-4 col-lg-3">

                <h6 class="fw-bold">
                    Quick Links
                </h6>

                <div class="d-flex flex-column gap-2">

                    <a href="index.php" class="text-white-50">
                        Home
                    </a>

                    <a href="#menu" class="text-white-50">
                        Menu
                    </a>

                    <a href="#about" class="text-white-50">
                        About
                    </a>

                    <a href="auth/login.php" class="text-white-50">
                        Login
                    </a>

                </div>

            </div>

            <div class="col-md-8 col-lg-4">

                <h6 class="fw-bold">
                    Contact
                </h6>

                <p class="text-white-50 mb-2">
                    <i class="bi bi-geo-alt me-2"></i>
                    Restaurant Address
                </p>

                <p class="text-white-50 mb-2">
                    <i class="bi bi-telephone me-2"></i>
                    +977 9800000000
                </p>

                <p class="text-white-50">
                    <i class="bi bi-envelope me-2"></i>
                    info@rmsrestaurant.com
                </p>

            </div>

        </div>

        <hr class="border-secondary my-4">

        <div class="text-center text-white-50">

            © 2026 RMS Restaurant. All rights reserved.

        </div>

    </div>

</footer>


<!-- Bootstrap JavaScript -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

</body>
</html>