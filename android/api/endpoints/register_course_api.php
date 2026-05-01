<?php
/**
 * Endpoint: register_course_api.php
 * Delegates all registration logic to RegistrationService.
 * Checks: duplicate, time conflict, prerequisite, capacity (15), deadline.
 */
require_once __DIR__ . '/../helpers/json_response.php';
require_once __DIR__ . '/../helpers/request.php';
require_once __DIR__ . '/../services/RegistrationService.php';

try {
    $data = require_post_fields(['student_id', 'course_id', 'section_id', 'semester', 'year']);

    $result = register_student(
        $data['student_id'],
        $data['course_id'],
        $data['section_id'],
        $data['semester'],
        (int) $data['year']
    );

    send_json($result, $result['success'] ? 200 : 400);
} catch (Throwable $e) {
    send_json(['success' => false, 'message' => $e->getMessage()], 400);
}