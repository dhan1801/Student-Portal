<?php
require_once __DIR__ . '/../helpers/json_response.php';
require_once __DIR__ . '/../helpers/request.php';
require_once __DIR__ . '/../services/DiscussionService.php';

try {
    $data = require_get_fields(['course_id', 'section_id', 'semester', 'year']);

    $result = fetch_discussion_posts(
        $data['course_id'],
        $data['section_id'],
        $data['semester'],
        (int) $data['year']
    );

    send_json($result);
} catch (Throwable $e) {
    send_json(['success' => false, 'message' => $e->getMessage()], 400);
}