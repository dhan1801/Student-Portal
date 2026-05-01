<?php

function require_post_fields(array $fields): array
{
    $data = [];

    foreach ($fields as $field) {
        if (!isset($_POST[$field]) || trim($_POST[$field]) === "") {
            throw new Exception("Missing required field: " . $field);
        }
        $data[$field] = trim($_POST[$field]);
    }

    return $data;
}

function require_get_fields(array $fields): array
{
    $data = [];

    foreach ($fields as $field) {
        if (!isset($_GET[$field]) || trim($_GET[$field]) === "") {
            throw new Exception("Missing required field: " . $field);
        }
        $data[$field] = trim($_GET[$field]);
    }

    return $data;
}