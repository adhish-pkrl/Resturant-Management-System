-- =========================================================
-- RESTAURANT MANAGEMENT SYSTEM
-- Database: rms_db
-- =========================================================

CREATE DATABASE IF NOT EXISTS rms_db;

USE rms_db;

-- ---------------------------------------------------------
-- Drop existing tables
-- ---------------------------------------------------------
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS reservations;
DROP TABLE IF EXISTS menu_items;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS restaurant_tables;
DROP TABLE IF EXISTS users;

SET FOREIGN_KEY_CHECKS = 1;

-- =========================================================
-- 1. USERS
-- =========================================================

CREATE TABLE users (
    id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'staff', 'customer') NOT NULL DEFAULT 'customer',
    status VARCHAR(20) NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE KEY unique_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =========================================================
-- 2. CATEGORIES
-- =========================================================

CREATE TABLE categories (
    id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description VARCHAR(255) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE KEY unique_category_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =========================================================
-- 3. MENU ITEMS
-- =========================================================

CREATE TABLE menu_items (
    id INT NOT NULL AUTO_INCREMENT,
    category_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    description VARCHAR(255) NULL,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255) NULL,
    availability VARCHAR(20) NOT NULL DEFAULT 'available',
    preparation_time INT NOT NULL DEFAULT 15,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    KEY idx_menu_category (category_id),

    CONSTRAINT fk_menu_category
        FOREIGN KEY (category_id)
        REFERENCES categories(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =========================================================
-- 4. RESTAURANT TABLES
-- =========================================================

CREATE TABLE restaurant_tables (
    id INT NOT NULL AUTO_INCREMENT,
    table_number VARCHAR(20) NOT NULL,
    capacity INT NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'available',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE KEY unique_table_number (table_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =========================================================
-- 5. ORDERS
-- =========================================================

CREATE TABLE orders (
    id INT NOT NULL AUTO_INCREMENT,
    customer_id INT NOT NULL,
    table_id INT NULL,
    order_type VARCHAR(20) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    order_status VARCHAR(30) NOT NULL DEFAULT 'pending',
    payment_status VARCHAR(20) NOT NULL DEFAULT 'pending',
    notes TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL,

    PRIMARY KEY (id),

    KEY idx_orders_customer (customer_id),
    KEY idx_orders_table (table_id),

    CONSTRAINT fk_orders_customer
        FOREIGN KEY (customer_id)
        REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_orders_table
        FOREIGN KEY (table_id)
        REFERENCES restaurant_tables(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =========================================================
-- 6. ORDER ITEMS
-- =========================================================

CREATE TABLE order_items (
    id INT NOT NULL AUTO_INCREMENT,
    order_id INT NOT NULL,
    menu_item_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    KEY idx_order_items_order (order_id),
    KEY idx_order_items_menu (menu_item_id),

    CONSTRAINT fk_order_items_order
        FOREIGN KEY (order_id)
        REFERENCES orders(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_order_items_menu
        FOREIGN KEY (menu_item_id)
        REFERENCES menu_items(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =========================================================
-- 7. PAYMENTS
-- =========================================================

CREATE TABLE payments (
    id INT NOT NULL AUTO_INCREMENT,
    order_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(30) NOT NULL,
    payment_status VARCHAR(20) NOT NULL DEFAULT 'pending',
    transaction_id VARCHAR(100) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    KEY idx_payments_order (order_id),

    CONSTRAINT fk_payments_order
        FOREIGN KEY (order_id)
        REFERENCES orders(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =========================================================
-- 8. RESERVATIONS
-- =========================================================

CREATE TABLE reservations (
    id INT NOT NULL AUTO_INCREMENT,
    customer_id INT NOT NULL,
    table_id INT NOT NULL,
    reservation_date DATE NOT NULL,
    reservation_time TIME NOT NULL,
    number_of_guests INT NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    KEY idx_reservations_customer (customer_id),
    KEY idx_reservations_table (table_id),

    CONSTRAINT fk_reservations_customer
        FOREIGN KEY (customer_id)
        REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_reservations_table
        FOREIGN KEY (table_id)
        REFERENCES restaurant_tables(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =========================================================
-- SAMPLE CATEGORIES
-- =========================================================

INSERT INTO categories (name, description) VALUES
('Pizza', 'Freshly prepared pizzas'),
('Burger', 'Fresh and delicious burgers'),
('Momo', 'Steamed and fried momos'),
('Indian Food', 'Traditional Indian dishes'),
('Chinese Food', 'Popular Chinese dishes'),
('Drinks', 'Cold and hot beverages'),
('Desserts', 'Sweet dishes and desserts');


-- =========================================================
-- SAMPLE RESTAURANT TABLES
-- =========================================================

INSERT INTO restaurant_tables (table_number, capacity, status) VALUES
('Table 1', 2, 'available'),
('Table 2', 4, 'available'),
('Table 3', 4, 'available'),
('Table 4', 6, 'available'),
('Table 5', 8, 'available');


-- =========================================================
-- DATABASE RELATIONSHIPS
-- =========================================================
--
-- categories.id
--       ↓
-- menu_items.category_id
--
-- users.id
--       ↓
-- orders.customer_id
--
-- restaurant_tables.id
--       ↓
-- orders.table_id
--
-- orders.id
--       ↓
-- order_items.order_id
--
-- menu_items.id
--       ↓
-- order_items.menu_item_id
--
-- orders.id
--       ↓
-- payments.order_id
--
-- users.id
--       ↓
-- reservations.customer_id
--
-- restaurant_tables.id
--       ↓
-- reservations.table_id
--
-- =========================================================