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

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: categories.php");
    exit;
}

$category_id = (int) $_GET['id'];

// Check whether the category exists
$stmt = $conn->prepare(
    "SELECT id FROM categories WHERE id = ? LIMIT 1"
);

$stmt->bind_param("i", $category_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 1) {

    $stmt->close();

    // Delete category
    $delete = $conn->prepare(
        "DELETE FROM categories WHERE id = ?"
    );

    $delete->bind_param("i", $category_id);
    $delete->execute();

    $delete->close();

} else {

    $stmt->close();
}

$conn->close();

header("Location: categories.php");
exit;

?>