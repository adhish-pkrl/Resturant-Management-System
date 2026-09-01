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

// Prevent deleting your own account
if ($user_id === (int) $_SESSION['user_id']) {
    header("Location: users.php?error=self_delete");
    exit;
}

// Check whether user exists
$stmt = $conn->prepare(
    "SELECT id FROM users WHERE id = ? LIMIT 1"
);

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 1) {

    $stmt->close();

    // Delete user
    $delete = $conn->prepare(
        "DELETE FROM users WHERE id = ?"
    );

    $delete->bind_param("i", $user_id);
    $delete->execute();

    $delete->close();
}

$conn->close();

header("Location: users.php");
exit;

?>