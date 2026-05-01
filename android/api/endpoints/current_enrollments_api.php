<?php
/**
 * Endpoint: current_enrollments_api.php
 * Returns a student's current enrollments (grade IS NULL) for the given term.
 * Delegates to RegistrationService::fetch_current_enrollments().
 */
require_once __DIR__ . '/../helpers/json_response.php';
require_once __DIR__ . '/../helpers/request.php';
require_once __DIR__ . '/../services/RegistrationService.php';

try {
    $data = require_get_fields(['student_id']);

    $semester = trim($_GET['semester'] ?? 'Spring');
    $year     = isset($_GET['year']) ? (int) $_GET['year'] : 2026;

    $courses = fetch_current_enrollments($data['student_id'], $semester, $year);

    send_json(['success' => true, 'courses' => $courses]);
} catch (Throwable $e) {
    send_json(['success' => false, 'message' => $e->getMessage()], 400);
}