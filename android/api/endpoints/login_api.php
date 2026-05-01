<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);

require_once "../config/db.php";
header("Content-Type: application/json");

$email = trim($_POST["email"] ?? "");
$password = trim($_POST["password"] ?? "");

if ($email === "" || $password === "") {
    echo json_encode([
        "success" => false,
        "message" => "Email and password are required."
    ]);
    exit;
}

$stmt = $conn->prepare("SELECT email, type FROM account WHERE email = ? AND password = ?");
if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => "Prepare failed: " . $conn->error
    ]);
    exit;
}

$stmt->bind_param("ss", $email, $password);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid email or password."
    ]);
    exit;
}

$row = $result->fetch_assoc();
$type = $row["type"];
$stmt->close();

$name = "";
$userId = "";

if ($type === "student") {
    $s = $conn->prepare("SELECT student_id, name FROM student WHERE email = ?");
    if (!$s) {
        echo json_encode([
            "success" => false,
            "message" => "Student lookup failed: " . $conn->error
        ]);
        exit;
    }

    $s->bind_param("s", $email);
    $s->execute();
    $s->bind_result($userId, $name);
    $s->fetch();
    $s->close();

} elseif ($type === "instructor") {
    $i = $conn->prepare("SELECT instructor_id, name FROM instructor WHERE email = ?");
    if (!$i) {
        echo json_encode([
            "success" => false,
            "message" => "Instructor lookup failed: " . $conn->error
        ]);
        exit;
    }

    $i->bind_param("s", $email);
    $i->execute();
    $i->bind_result($userId, $name);
    $i->fetch();
    $i->close();

} elseif ($type === "admin") {
    $name = "Admin";
    $userId = "admin";
}

echo json_encode([
    "success" => true,
    "message" => "Login successful.",
    "type" => $type,
    "user" => [
        "user_id" => $userId,
        "name" => $name,
        "email" => $email
    ]
]);
exit;