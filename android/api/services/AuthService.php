<?php
require_once __DIR__ . '/../config/db.php';

function login_user(string $email, string $password): array
{
    $conn = getDbConnection();

    $stmt = $conn->prepare("SELECT email, type FROM account WHERE email = ? AND password = ?");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $stmt->close();
        return [
            'success' => false,
            'message' => 'Invalid email or password.'
        ];
    }

    $row = $result->fetch_assoc();
    $type = $row["type"];
    $stmt->close();

    $name = "";
    $userId = "";

    if ($type === "student") {
        $s = $conn->prepare("SELECT student_id, name FROM student WHERE email = ?");
        $s->bind_param("s", $email);
        $s->execute();
        $s->bind_result($userId, $name);
        $s->fetch();
        $s->close();
    } elseif ($type === "instructor") {
        $i = $conn->prepare("SELECT instructor_id, name FROM instructor WHERE email = ?");
        $i->bind_param("s", $email);
        $i->execute();
        $i->bind_result($userId, $name);
        $i->fetch();
        $i->close();
    } elseif ($type === "admin") {
        $name = "Admin";
        $userId = "admin";
    }

    return [
        'success' => true,
        'message' => 'Login successful.',
        'type' => $type,
        'user' => [
            'user_id' => $userId,
            'name' => $name,
            'email' => $email
        ]
    ];
}