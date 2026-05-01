<?php
require_once __DIR__ . '/../helpers/json_response.php';
require_once __DIR__ . '/../helpers/request.php';
require_once __DIR__ . '/../services/DiscussionService.php';

try {
    $data = require_post_fields(['student_id', 'course_id', 'section_id', 'semester', 'year', 'content']);

    // reply_to_id is optional — default to '0' meaning top-level post
    $replyToId = trim($_POST['reply_to_id'] ?? '0');
    if ($replyToId === '') {
        $replyToId = '0';
    }

    $result = post_discussion_message(
        $data['student_id'],
        $data['course_id'],
        $data['section_id'],
        $data['semester'],
        (int) $data['year'],
        $data['content'],
        $replyToId
    );

    send_json($result, $result['success'] ? 200 : 400);
} catch (Throwable $e) {
    send_json(['success' => false, 'message' => $e->getMessage()], 400);
}