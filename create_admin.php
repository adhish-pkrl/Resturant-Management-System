<?php

require_once "config/database.php";

$name = "Administrator";
$email = "admin@rms.com";
$password = "admin123";
$role = "admin";
$status = "active";

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare(
    "INSERT INTO users (name, email, password, role, status)
     VALUES (?, ?, ?, ?, ?)"
);

$stmt->bind_param(
    "sssss",
    $name,
    $email,
    $hashed_password,
    $role,
    $status
);

if ($stmt->execute()) {

    echo "Admin account created successfully!<br>";
    echo "Email: admin@rms.com<br>";
    echo "Password: admin123";

} else {

    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();

?>