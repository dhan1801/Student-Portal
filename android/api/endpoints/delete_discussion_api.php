<?php
require_once __DIR__ . '/../helpers/json_response.php';
require_once __DIR__ . '/../helpers/request.php';
require_once __DIR__ . '/../services/DiscussionService.php';

try {
    $data = require_post_fields([
        'requester_id',
        'discussion_id',
        'course_id',
        'section_id',
        'semester',
        'year'
    ]);

    $result = delete_discussion_post(
        $data['requester_id'],
        $data['discussion_id'],
        $data['course_id'],
        $data['section_id'],
        $data['semester'],
        (int) $data['year']
    );

    send_json($result, $result['success'] ? 200 : 400);
} catch (Throwable $e) {
    send_json(['success' => false, 'message' => $e->getMessage()], 400);
}