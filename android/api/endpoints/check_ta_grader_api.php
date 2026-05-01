<?php
/**
 * Endpoint: check_ta_grader_api.php
 * Returns whether a student is a TA or grader for a given section.
 *
 * GET params: student_id, course_id, section_id, semester, year
 *
 * Response:
 *   { "success": true, "is_ta_or_grader": true|false }
 */
require_once __DIR__ . '/../helpers/json_response.php';
require_once __DIR__ . '/../helpers/request.php';
require_once __DIR__ . '/../services/DiscussionService.php';

try {
    $data = require_get_fields(['student_id', 'course_id', 'section_id', 'semester', 'year']);

    // Reuse the is_ta_or_grader helper already defined in DiscussionService
    $conn = getDbConnection();
    $result = is_ta_or_grader(
        $conn,
        $data['student_id'],
        $data['course_id'],
        $data['section_id'],
        $data['semester'],
        (int) $data['year']
    );

    send_json(['success' => true, 'is_ta_or_grader' => $result]);
} catch (Throwable $e) {
    send_json(['success' => false, 'message' => $e->getMessage()], 400);
}