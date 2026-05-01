<?php
/**
 * Endpoint: browse_courses_api.php
 * Returns current-semester course offerings with enrollment counts.
 * Uses a single SQL subquery via RegistrationService (no N+1 queries).
 */
require_once __DIR__ . '/../helpers/json_response.php';
require_once __DIR__ . '/../helpers/request.php';
require_once __DIR__ . '/../services/RegistrationService.php';

try {
    $semester = trim($_GET['semester'] ?? 'Spring');
    $year     = isset($_GET['year']) ? (int) $_GET['year'] : 2026;

    if ($semester === '') {
        send_json(['success' => false, 'message' => 'semester is required.'], 400);
    }

    $rows = fetch_browse_courses($semester, $year);

    $courses = array_map(fn($row) => [
        'course_id'   => $row['course_id'],
        'title'       => $row['title'],
        'section_id'  => $row['section_id'],
        'semester'    => $row['semester'],
        'year'        => (int) $row['year'],
        'building'    => $row['building'],
        'room_number' => $row['room_number'],
        'day'         => $row['day'],
        'start_hour'  => (int) $row['start_hour'],
        'start_min'   => (int) $row['start_min'],
        'end_hour'    => (int) $row['end_hour'],
        'end_min'     => (int) $row['end_min'],
        'enrolled'    => (int) $row['enrolled'],
        'max_allowed' => 15,
    ], $rows);

    send_json(['success' => true, 'courses' => $courses]);
} catch (Throwable $e) {
    send_json(['success' => false, 'message' => $e->getMessage()], 400);
}