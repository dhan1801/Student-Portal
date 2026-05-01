<?php
/**
 * Endpoint: get_discussion_courses_api.php
 *
 * Returns every course a student should be able to open on the discussion board:
 *   1. Courses the student is currently enrolled in (grade IS NULL) — can post/reply.
 *   2. Courses the student is TA or grader for in the same term — can also delete posts.
 *
 * A course that appears in both lists is returned once with is_ta_or_grader = true.
 *
 * GET params: student_id, semester (optional, default Spring), year (optional, default 2026)
 *
 * Response:
 *   {
 *     "success": true,
 *     "courses": [
 *       {
 *         "course_id":      "CS-101",
 *         "section_id":     "1",
 *         "title":          "Intro to CS",
 *         "semester":       "Spring",
 *         "year":           2026,
 *         "is_ta_or_grader": false
 *       }, ...
 *     ]
 *   }
 */
require_once __DIR__ . '/../helpers/json_response.php';
require_once __DIR__ . '/../helpers/request.php';
require_once __DIR__ . '/../config/db.php';

try {
    $data     = require_get_fields(['student_id']);
    $studentId = $data['student_id'];
    $semester  = trim($_GET['semester'] ?? 'Spring');
    $year      = isset($_GET['year']) ? (int) $_GET['year'] : 2026;

    $conn = getDbConnection();

    // ── 1. Courses the student is enrolled in (currently in-progress) ──────────
    $stmt = $conn->prepare("
        SELECT t.course_id, t.section_id, c.title, t.semester, t.year
        FROM takes t
        JOIN course c ON c.course_id = t.course_id
        WHERE t.student_id = ?
          AND t.semester   = ?
          AND t.year       = ?
          AND t.grade IS NULL
    ");
    $stmt->bind_param("ssi", $studentId, $semester, $year);
    $stmt->execute();
    $enrolled = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Build a keyed map so we can merge without duplicates
    // key = "course_id|section_id"
    $courseMap = [];
    foreach ($enrolled as $row) {
        $key = $row['course_id'] . '|' . $row['section_id'];
        $courseMap[$key] = [
            'course_id'       => $row['course_id'],
            'section_id'      => $row['section_id'],
            'title'           => $row['title'],
            'semester'        => $row['semester'],
            'year'            => (int) $row['year'],
            'is_ta_or_grader' => false,
        ];
    }

    // ── 2. Courses the student is TA for ────────────────────────────────────────
    $stmt = $conn->prepare("
        SELECT ta.course_id, ta.section_id, c.title, ta.semester, ta.year
        FROM teacher_assistant ta
        JOIN course c ON c.course_id = ta.course_id
        WHERE ta.student_id = ?
          AND ta.semester   = ?
          AND ta.year       = ?
    ");
    $stmt->bind_param("ssi", $studentId, $semester, $year);
    $stmt->execute();
    $ta_rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    foreach ($ta_rows as $row) {
        $key = $row['course_id'] . '|' . $row['section_id'];
        // Upsert: mark as TA/grader whether or not it already exists from enrollment
        $courseMap[$key] = [
            'course_id'       => $row['course_id'],
            'section_id'      => $row['section_id'],
            'title'           => $row['title'],
            'semester'        => $row['semester'],
            'year'            => (int) $row['year'],
            'is_ta_or_grader' => true,
        ];
    }

    // ── 3. Courses the student is grader for ────────────────────────────────────
    $stmt = $conn->prepare("
        SELECT g.course_id, g.section_id, c.title, g.semester, g.year
        FROM grader g
        JOIN course c ON c.course_id = g.course_id
        WHERE g.student_id = ?
          AND g.semester   = ?
          AND g.year       = ?
    ");
    $stmt->bind_param("ssi", $studentId, $semester, $year);
    $stmt->execute();
    $gr_rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    foreach ($gr_rows as $row) {
        $key = $row['course_id'] . '|' . $row['section_id'];
        $courseMap[$key] = [
            'course_id'       => $row['course_id'],
            'section_id'      => $row['section_id'],
            'title'           => $row['title'],
            'semester'        => $row['semester'],
            'year'            => (int) $row['year'],
            'is_ta_or_grader' => true,
        ];
    }

    send_json(['success' => true, 'courses' => array_values($courseMap)]);

} catch (Throwable $e) {
    send_json(['success' => false, 'message' => $e->getMessage()], 400);
}