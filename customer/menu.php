<?php

session_start();

include '../config/database.php';


/* =====================================================
   CHECK CUSTOMER LOGIN
   ===================================================== */

if (
    !isset($_SESSION["user_id"]) ||
    $_SESSION["user_role"] !== "customer"
) {
    header("Location: ../auth/login.php");
    exit();
}

$user_name = $_SESSION["user_name"];


/* =====================================================
   GET CATEGORIES
   ===================================================== */

$categories_sql = "
    SELECT id, name
    FROM categories
    ORDER BY name ASC
";

$categories_result = $conn->query($categories_sql);


/* =====================================================
   GET MENU ITEMS
   ===================================================== */

$menu_sql = "
    SELECT
        menu_items.id,
        menu_items.category_id,
        menu_items.name,
        menu_items.description,
        menu_items.price,
        menu_items.image,
        menu_items.availability,
        menu_items.preparation_time,
        categories.name AS category_name
    FROM menu_items
    INNER JOIN categories
        ON menu_items.category_id = categories.id
    WHERE menu_items.availability = 'available'
    ORDER BY menu_items.id DESC
";

$menu_result = $conn->query($menu_sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Menu - RMS Restaurant</title>


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

    <aside
        class="customer-sidebar"
        id="customerSidebar"
    >

        <div class="customer-brand">

            <a href="index.php">

                <span>RMS</span> Restaurant

            </a>

        </div>


        <!-- Customer profile -->

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
                    Customer
                </small>

            </div>

        </div>


        <!-- Navigation -->

        <nav class="customer-nav">

            <a
                href="index.php"
                class="customer-nav-link"
            >

                <i class="bi bi-grid-1x2-fill"></i>

                Dashboard

            </a>


            <a
                href="menu.php"
                class="customer-nav-link active"
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

                <span class="nav-badge">
                    0
                </span>

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


        <!-- Logout -->

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
         MAIN CONTENT
         ===================================================== -->

    <main class="customer-main">


        <!-- TOPBAR -->

        <header class="customer-topbar">

            <div class="d-flex align-items-center gap-3">

                <button
                    type="button"
                    class="customer-menu-toggle"
                    id="customerMenuToggle"
                >

                    <i class="bi bi-list"></i>

                </button>


                <div>

                    <small class="text-secondary">
                        Customer Panel
                    </small>

                    <h5 class="mb-0 fw-bold">
                        Restaurant Menu
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

                </button>


                <a
                    href="cart.php"
                    class="customer-icon-btn d-flex align-items-center justify-content-center text-decoration-none"
                    title="Cart"
                >

                    <i class="bi bi-cart3"></i>

                </a>


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
                            Customer
                        </small>

                    </div>

                </div>

            </div>

        </header>


        <!-- =====================================================
             PAGE CONTENT
             ===================================================== -->

        <div class="customer-content">


            <!-- PAGE TITLE -->

            <section class="customer-welcome">

                <div>

                    <span class="welcome-label">
                        OUR MENU
                    </span>

                    <h1>
                        Delicious food, made for you 🍽️
                    </h1>

                    <p>
                        Explore our menu and find something
                        you'll love.
                    </p>

                </div>

            </section>


            <!-- =================================================
                 SEARCH + CATEGORY DROPDOWN
                 ================================================= -->

            <section class="customer-panel mb-4">

                <div class="row g-3 align-items-center">


                    <!-- Search -->

                    <div class="col-lg-7">

                        <div class="input-group input-group-lg">

                            <span class="input-group-text bg-white">

                                <i class="bi bi-search"></i>

                            </span>


                            <input
                                type="text"
                                id="menuSearch"
                                class="form-control"
                                placeholder="Search for food..."
                            >

                        </div>

                    </div>


                    <!-- Category dropdown -->

                    <div class="col-lg-5">

                        <select
                            id="categoryFilter"
                            class="form-select form-select-lg"
                        >

                            <option value="all">
                                All Categories
                            </option>


                            <?php

                            if ($categories_result) {

                                while (
                                    $category =
                                    $categories_result->fetch_assoc()
                                ) {

                            ?>

                                <option
                                    value="<?php
                                    echo (int)$category["id"];
                                    ?>"
                                >

                                    <?php
                                    echo htmlspecialchars(
                                        $category["name"]
                                    );
                                    ?>

                                </option>

                            <?php

                                }

                            }

                            ?>

                        </select>

                    </div>

                </div>

            </section>


            <!-- =================================================
                 CATEGORY BUTTONS
                 ================================================= -->

            <div class="menu-category-buttons mb-4">


                <!-- All -->

                <button
                    class="menu-category-btn active"
                    data-category="all"
                    type="button"
                >

                    <i class="bi bi-grid"></i>

                    All

                </button>


                <?php

                /*
                 * Query categories again because the first
                 * result was already used by the dropdown.
                 */

                $button_categories_sql = "
                    SELECT id, name
                    FROM categories
                    ORDER BY name ASC
                ";

                $button_categories_result =
                    $conn->query(
                        $button_categories_sql
                    );


                if ($button_categories_result) {

                    while (
                        $category =
                        $button_categories_result->fetch_assoc()
                    ) {

                ?>

                    <button
                        class="menu-category-btn"
                        data-category="<?php
                        echo (int)$category["id"];
                        ?>"
                        type="button"
                    >

                        <i class="bi bi-tag"></i>

                        <?php
                        echo htmlspecialchars(
                            $category["name"]
                        );
                        ?>

                    </button>

                <?php

                    }

                }

                ?>

            </div>


            <!-- =================================================
                 MENU ITEMS
                 ================================================= -->

            <section>

                <div
                    class="row g-4"
                    id="menuItems"
                >


                    <?php

                    if (
                        $menu_result &&
                        $menu_result->num_rows > 0
                    ) {

                        while (
                            $item =
                            $menu_result->fetch_assoc()
                        ) {

                    ?>


                        <div
                            class="col-sm-6 col-xl-4 col-xxl-3 menu-item"
                            data-category="<?php
                            echo (int)$item["category_id"];
                            ?>"
                            data-name="<?php
                            echo htmlspecialchars(
                                $item["name"]
                            );
                            ?>"
                        >


                            <div class="food-card">


                                <!-- =========================
                                     IMAGE
                                     ========================= -->

                                <div class="food-card-image">

                                    <?php

                                    if (
                                        !empty(
                                            $item["image"]
                                        )
                                    ) {

                                    ?>

                                        <img
                                            src="../uploads/menu_items/<?php
                                            echo htmlspecialchars(
                                                $item["image"]
                                            );
                                            ?>"
                                            alt="<?php
                                            echo htmlspecialchars(
                                                $item["name"]
                                            );
                                            ?>"
                                            style="
                                                width:100%;
                                                height:100%;
                                                object-fit:cover;
                                            "
                                        >

                                    <?php

                                    } else {

                                    ?>

                                        <i class="bi bi-egg-fried"></i>

                                    <?php

                                    }

                                    ?>

                                </div>


                                <!-- =========================
                                     FOOD INFORMATION
                                     ========================= -->

                                <div class="food-card-body">


                                    <div
                                        class="d-flex justify-content-between"
                                    >


                                        <span
                                            class="food-category"
                                        >

                                            <?php
                                            echo htmlspecialchars(
                                                $item[
                                                    "category_name"
                                                ]
                                            );
                                            ?>

                                        </span>


                                        <span
                                            class="food-rating"
                                        >

                                            <i
                                                class="bi bi-check-circle-fill"
                                            ></i>

                                            Available

                                        </span>

                                    </div>


                                    <!-- Name -->

                                    <h4>

                                        <?php
                                        echo htmlspecialchars(
                                            $item["name"]
                                        );
                                        ?>

                                    </h4>


                                    <!-- Description -->

                                    <p>

                                        <?php
                                        echo htmlspecialchars(
                                            $item["description"]
                                        );
                                        ?>

                                    </p>


                                    <!-- Price + time -->

                                    <div
                                        class="food-card-footer"
                                    >


                                        <div>

                                            <strong>

                                                Rs.

                                                <?php
                                                echo number_format(
                                                    $item["price"],
                                                    2
                                                );
                                                ?>

                                            </strong>


                                            <small
                                                class="d-block text-secondary"
                                            >

                                                <i
                                                    class="bi bi-clock"
                                                ></i>

                                                <?php
                                                echo (int)
                                                    $item[
                                                        "preparation_time"
                                                    ];
                                                ?>

                                                min

                                            </small>

                                        </div>


                                        <!-- Add button -->

                                        <button
                                            class="btn btn-warning add-to-cart"
                                            type="button"
                                            data-item-id="<?php
                                            echo (int)$item["id"];
                                            ?>"
                                        >

                                            <i
                                                class="bi bi-plus-lg"
                                            ></i>

                                            Add

                                        </button>

                                    </div>

                                </div>

                            </div>

                        </div>


                    <?php

                        }

                    } else {

                    ?>


                        <!-- No menu -->

                        <div class="col-12">

                            <div
                                class="customer-panel text-center py-5"
                            >

                                <i
                                    class="bi bi-egg-fried"
                                    style="font-size:50px;"
                                ></i>


                                <h4 class="mt-3">

                                    No menu items available

                                </h4>


                                <p class="text-secondary">

                                    Please check back later.

                                </p>

                            </div>

                        </div>


                    <?php

                    }

                    ?>

                </div>


                <!-- No search results -->

                <div
                    id="noResults"
                    class="customer-panel text-center py-5 mt-4"
                    style="display:none;"
                >

                    <i
                        class="bi bi-search"
                        style="font-size:40px;"
                    ></i>


                    <h4 class="mt-3">
                        No food found
                    </h4>


                    <p class="text-secondary">
                        Try another search or category.
                    </p>

                </div>

            </section>

        </div>

    </main>

</div>


<!-- =====================================================
     JAVASCRIPT
     ===================================================== -->

<script>

document.addEventListener(
    "DOMContentLoaded",
    function () {


        /* ==========================================
           MOBILE SIDEBAR
           ========================================== */

        const toggleButton =
            document.getElementById(
                "customerMenuToggle"
            );

        const sidebar =
            document.getElementById(
                "customerSidebar"
            );


        if (
            toggleButton &&
            sidebar
        ) {

            toggleButton.addEventListener(
                "click",
                function () {

                    sidebar.classList.toggle(
                        "show"
                    );

                }
            );

        }


        /* ==========================================
           SEARCH + CATEGORY FILTER
           ========================================== */

        const searchInput =
            document.getElementById(
                "menuSearch"
            );

        const categoryFilter =
            document.getElementById(
                "categoryFilter"
            );

        const categoryButtons =
            document.querySelectorAll(
                ".menu-category-btn"
            );

        const menuItems =
            document.querySelectorAll(
                ".menu-item"
            );

        const noResults =
            document.getElementById(
                "noResults"
            );


        function filterMenu(
            category = "all"
        ) {

            const search =
                searchInput.value
                    .toLowerCase()
                    .trim();


            let visibleItems = 0;


            menuItems.forEach(
                function (item) {


                    const itemCategory =
                        item.dataset.category;


                    const itemName =
                        item.dataset.name
                            .toLowerCase();


                    const categoryMatch =
                        category === "all" ||
                        itemCategory === category;


                    const searchMatch =
                        itemName.includes(
                            search
                        );


                    if (
                        categoryMatch &&
                        searchMatch
                    ) {

                        item.style.display =
                            "";

                        visibleItems++;

                    } else {

                        item.style.display =
                            "none";

                    }

                }
            );


            if (
                visibleItems === 0
            ) {

                noResults.style.display =
                    "block";

            } else {

                noResults.style.display =
                    "none";

            }

        }


        /* Search */

        searchInput.addEventListener(
            "input",
            function () {

                filterMenu(
                    categoryFilter.value
                );

            }
        );


        /* Dropdown */

        categoryFilter.addEventListener(
            "change",
            function () {


                const selectedCategory =
                    this.value;


                categoryButtons.forEach(
                    function (button) {

                        button.classList.remove(
                            "active"
                        );


                        if (
                            button.dataset.category ===
                            selectedCategory
                        ) {

                            button.classList.add(
                                "active"
                            );

                        }

                    }
                );


                filterMenu(
                    selectedCategory
                );

            }
        );


        /* Category buttons */

        categoryButtons.forEach(
            function (button) {

                button.addEventListener(
                    "click",
                    function () {


                        const category =
                            this.dataset.category;


                        categoryButtons.forEach(
                            function (btn) {

                                btn.classList.remove(
                                    "active"
                                );

                            }
                        );


                        this.classList.add(
                            "active"
                        );


                        categoryFilter.value =
                            category;


                        filterMenu(
                            category
                        );

                    }
                );

            }
        );


        /* ==========================================
           TEMPORARY ADD BUTTON
           ========================================== */

        const addButtons =
            document.querySelectorAll(
                ".add-to-cart"
            );


        addButtons.forEach(
            function (button) {

                button.addEventListener(
                    "click",
                    function () {


                        const originalHTML =
                            this.innerHTML;


                        this.innerHTML =
                            '<i class="bi bi-check-lg"></i> Added';


                        this.classList.remove(
                            "btn-warning"
                        );


                        this.classList.add(
                            "btn-success"
                        );


                        setTimeout(
                            () => {

                                this.innerHTML =
                                    originalHTML;


                                this.classList.remove(
                                    "btn-success"
                                );


                                this.classList.add(
                                    "btn-warning"
                                );

                            },
                            1200
                        );

                    }
                );

            }
        );

    }
);

</script>


</body>

</html>

