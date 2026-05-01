<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);

require_once "../config/db.php";
header("Content-Type: application/json");

$email = trim($_POST["email"] ?? "");
$newPassword = trim($_POST["new_password"] ?? "");
$confirmPassword = trim($_POST["confirm_password"] ?? "");

if ($email === "" || $newPassword === "" || $confirmPassword === "") {
    echo json_encode([
        "success" => false,
        "message" => "All fields are required."
    ]);
    exit;
}

if ($newPassword !== $confirmPassword) {
    echo json_encode([
        "success" => false,
        "message" => "Passwords do not match."
    ]);
    exit;
}

if (strlen($newPassword) < 4) {
    echo json_encode([
        "success" => false,
        "message" => "Password must be at least 4 characters long."
    ]);
    exit;
}

$check = $conn->prepare("SELECT email FROM account WHERE email = ?");
if (!$check) {
    echo json_encode([
        "success" => false,
        "message" => "Prepare failed: " . $conn->error
    ]);
    exit;
}

$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows === 0) {
    echo json_encode([
        "success" => false,
        "message" => "No account found for this email."
    ]);
    $check->close();
    exit;
}
$check->close();

$update = $conn->prepare("UPDATE account SET password = ? WHERE email = ?");
if (!$update) {
    echo json_encode([
        "success" => false,
        "message" => "Prepare failed: " . $conn->error
    ]);
    exit;
}

$update->bind_param("ss", $newPassword, $email);

if ($update->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Password updated successfully."
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Error updating password: " . $update->error
    ]);
}
$update->close();
exit;