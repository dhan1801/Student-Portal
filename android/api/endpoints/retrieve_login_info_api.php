<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);

require_once "../config/db.php";
header("Content-Type: application/json");

$email = trim($_GET["email"] ?? "");

if ($email === "") {
    echo json_encode([
        "success" => false,
        "message" => "Email is required."
    ]);
    exit;
}

$stmt = $conn->prepare("SELECT type FROM account WHERE email = ?");
if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => "Prepare failed: " . $conn->error
    ]);
    exit;
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode([
        "success" => false,
        "message" => "No account found for this email."
    ]);
    $stmt->close();
    exit;
}

$row = $result->fetch_assoc();
$type = $row["type"];
$stmt->close();

$userId = "";

if ($type === "student") {
    $s = $conn->prepare("SELECT student_id FROM student WHERE email = ?");
    if (!$s) {
        echo json_encode([
            "success" => false,
            "message" => "Student lookup failed: " . $conn->error
        ]);
        exit;
    }
    $s->bind_param("s", $email);
    $s->execute();
    $s->bind_result($userId);
    $s->fetch();
    $s->close();

} elseif ($type === "instructor") {
    $i = $conn->prepare("SELECT instructor_id FROM instructor WHERE email = ?");
    if (!$i) {
        echo json_encode([
            "success" => false,
            "message" => "Instructor lookup failed: " . $conn->error
        ]);
        exit;
    }
    $i->bind_param("s", $email);
    $i->execute();
    $i->bind_result($userId);
    $i->fetch();
    $i->close();

} elseif ($type === "admin") {
    $userId = "admin";
}

echo json_encode([
    "success" => true,
    "type" => $type,
    "user_id" => $userId,
    "email" => $email
]);
exit;