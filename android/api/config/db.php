<?php
$host     = "localhost";
$user     = "root";
$password = "";
$database = "DB2";

// Global $conn for endpoints that include this file directly
$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    header("Content-Type: application/json");
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed: " . $conn->connect_error
    ]);
    exit;
}

/**
 * Returns a fresh MySQLi connection.
 * Called by all service-layer files.
 */
function getDbConnection(): mysqli
{
    $conn = new mysqli("localhost", "root", "", "DB2");

    if ($conn->connect_error) {
        header("Content-Type: application/json");
        echo json_encode([
            "success" => false,
            "message" => "Database connection failed: " . $conn->connect_error
        ]);
        exit;
    }

    return $conn;
}