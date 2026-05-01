<?php
require_once __DIR__ . '/../config/db.php';

/**
 * Checks whether a student is enrolled in the given section.
 */
function is_student_enrolled(mysqli $conn, string $studentId, string $courseId,
                             string $sectionId, string $semester, int $year): bool
{
    $stmt = $conn->prepare("
        SELECT 1 FROM takes
        WHERE student_id = ? AND course_id = ?
          AND section_id = ? AND semester = ? AND year = ?
        LIMIT 1
    ");
    $stmt->bind_param("ssssi", $studentId, $courseId, $sectionId, $semester, $year);
    $stmt->execute();
    $exists = (bool) $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $exists;
}

/**
 * Checks whether a student is a TA or grader for the given section.
 */
function is_ta_or_grader(mysqli $conn, string $studentId, string $courseId,
                         string $sectionId, string $semester, int $year): bool
{
    foreach (['teacher_assistant', 'grader'] as $table) {
        $stmt = $conn->prepare("
            SELECT 1 FROM $table
            WHERE student_id = ? AND course_id = ?
              AND section_id = ? AND semester = ? AND year = ?
            LIMIT 1
        ");
        $stmt->bind_param("ssssi", $studentId, $courseId, $sectionId, $semester, $year);
        $stmt->execute();
        $found = (bool) $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($found) return true;
    }
    return false;
}

/**
 * Generates the next discussion_id scoped to a specific section.
 * Each section has its own counter starting at 1, so posts within
 * a section are numbered 1, 2, 3... independently of other sections.
 */
function next_discussion_id(mysqli $conn, string $courseId, string $sectionId,
                            string $semester, int $year): string
{
    $stmt = $conn->prepare("
        SELECT MAX(CAST(discussion_id AS UNSIGNED)) AS max_id
        FROM discussion
        WHERE course_id  = ? AND section_id = ?
          AND semester   = ? AND year       = ?
          AND discussion_id REGEXP '^[0-9]+$'
    ");
    $stmt->bind_param("sssi", $courseId, $sectionId, $semester, $year);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return (string)(((int)($row['max_id'] ?? 0)) + 1);
}

/**
 * Fetches all posts for a section ordered by discussion_id ascending.
 */
function fetch_discussion_posts(string $courseId, string $sectionId,
                                string $semester, int $year): array
{
    $conn = getDbConnection();
    $stmt = $conn->prepare("
        SELECT discussion_id, reply_id, student_id, content
        FROM discussion
        WHERE course_id = ? AND section_id = ?
          AND semester = ? AND year = ?
        ORDER BY CAST(discussion_id AS UNSIGNED) ASC
    ");
    $stmt->bind_param("sssi", $courseId, $sectionId, $semester, $year);
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return ['success' => true, 'posts' => $rows];
}

/**
 * Posts a new message or reply.
 * Each post gets its own row with a section-scoped discussion_id starting at 1.
 * Only enrolled students may post.
 */
function post_discussion_message(string $studentId, string $courseId,
                                 string $sectionId, string $semester, int $year,
                                 string $content, string $replyToId): array
{
    $conn = getDbConnection();

    if (!is_student_enrolled($conn, $studentId, $courseId, $sectionId, $semester, $year)) {
        return ['success' => false, 'message' => 'You are not enrolled in this section.'];
    }

    // Validate parent post exists if this is a reply
    if ($replyToId !== '0') {
        $check = $conn->prepare("
            SELECT 1 FROM discussion
            WHERE discussion_id = ? AND course_id = ?
              AND section_id = ? AND semester = ? AND year = ?
            LIMIT 1
        ");
        $check->bind_param("ssssi", $replyToId, $courseId, $sectionId, $semester, $year);
        $check->execute();
        $exists = (bool) $check->get_result()->fetch_assoc();
        $check->close();
        if (!$exists) {
            return ['success' => false, 'message' => 'The post you are replying to does not exist.'];
        }
    }

    // Generate section-scoped ID so each section starts from 1
    $newId = next_discussion_id($conn, $courseId, $sectionId, $semester, $year);

    $stmt = $conn->prepare("
        INSERT INTO discussion
            (discussion_id, reply_id, student_id, course_id, section_id, semester, year, content)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("ssssssis",
        $newId, $replyToId, $studentId, $courseId, $sectionId, $semester, $year, $content
    );

    if (!$stmt->execute()) {
        $err = $stmt->error;
        $stmt->close();
        return ['success' => false, 'message' => 'Post failed: ' . $err];
    }
    $stmt->close();
    return ['success' => true, 'message' => 'Post submitted successfully.', 'discussion_id' => $newId];
}

/**
 * Deletes a post by discussion_id. Only a TA or grader may delete.
 */
function delete_discussion_post(string $requesterId, string $discussionId,
                                string $courseId, string $sectionId,
                                string $semester, int $year): array
{
    $conn = getDbConnection();

    if (!is_ta_or_grader($conn, $requesterId, $courseId, $sectionId, $semester, $year)) {
        return ['success' => false, 'message' => 'Only a TA or grader may delete posts.'];
    }

    $stmt = $conn->prepare("
        DELETE FROM discussion
        WHERE discussion_id = ? AND course_id = ?
          AND section_id = ? AND semester = ? AND year = ?
    ");
    $stmt->bind_param("ssssi", $discussionId, $courseId, $sectionId, $semester, $year);
    $ok       = $stmt->execute();
    $affected = $stmt->affected_rows;
    $stmt->close();

    if (!$ok || $affected === 0) {
        return ['success' => false, 'message' => 'Post not found.'];
    }
    return ['success' => true, 'message' => 'Post deleted successfully.'];
}