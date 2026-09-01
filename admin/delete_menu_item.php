<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SESSION['user_role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

require_once "../config/database.php";

// Check menu item ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: menu_items.php");
    exit;
}

$item_id = (int) $_GET['id'];

// Check whether menu item exists
$stmt = $conn->prepare(
    "SELECT id FROM menu_items WHERE id = ? LIMIT 1"
);

$stmt->bind_param("i", $item_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 1) {

    $stmt->close();

    // Delete menu item
    $delete = $conn->prepare(
        "DELETE FROM menu_items WHERE id = ?"
    );

    $delete->bind_param("i", $item_id);
    $delete->execute();

    $delete->close();

} else {

    $stmt->close();
}

$conn->close();

header("Location: menu_items.php");
exit;

?>