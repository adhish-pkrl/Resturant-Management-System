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

// Check user ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: users.php");
    exit;
}

$user_id = (int) $_GET['id'];

// Prevent Admin from deleting their own account
if ($user_id === (int) $_SESSION['user_id']) {
    header("Location: users.php?error=self_delete");
    exit;
}

// Delete user
$stmt = $conn->prepare(
    "DELETE FROM users WHERE id = ?"
);

$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {

    $stmt->close();
    $conn->close();

    header("Location: users.php?message=deleted");
    exit;
}

$stmt->close();
$conn->close();

header("Location: users.php?error=delete_failed");
exit;
?>