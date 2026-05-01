<?php
require_once __DIR__ . '/../helpers/json_response.php';
require_once __DIR__ . '/../helpers/request.php';
require_once __DIR__ . '/../services/EvaluationService.php';

try {
    $data = require_post_fields([
        'student_id',
        'course_id',
        'section_id',
        'semester',
        'year',
        'rating',
        'comment'
    ]);

    $result = submit_course_evaluation(
        $data['student_id'],
        $data['course_id'],
        $data['section_id'],
        $data['semester'],
        (int)$data['year'],
        (int)$data['rating'],
        $data['comment']
    );

    send_json($result, $result['success'] ? 200 : 400);
} catch (Throwable $e) {
    send_json([
        'success' => false,
        'message' => $e->getMessage()
    ], 400);
}