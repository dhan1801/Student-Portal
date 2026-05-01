<?php
require_once __DIR__ . '/../config/db.php';

function submit_course_evaluation(
    string $studentId,
    string $courseId,
    string $sectionId,
    string $semester,
    int $year,
    int $rating,
    string $comment
): array {
    $conn = getDbConnection();

    $check = $conn->prepare("
        SELECT COUNT(*)
        FROM section
        WHERE course_id = ?
          AND section_id = ?
          AND semester = ?
          AND year = ?
    ");
    $check->bind_param("sssi", $courseId, $sectionId, $semester, $year);
    $check->execute();
    $check->bind_result($sectionExists);
    $check->fetch();
    $check->close();

    if ($sectionExists == 0) {
        return ['success' => false, 'message' => 'No matching section found for the given course, section, semester, and year.'];
    }

    $duplicateCheck = $conn->prepare("
        SELECT COUNT(*)
        FROM course_evaluation
        WHERE student_id = ?
          AND section_id = ?
          AND course_id = ?
          AND year = ?
          AND semester = ?
    ");
    $duplicateCheck->bind_param("sssis", $studentId, $sectionId, $courseId, $year, $semester);
    $duplicateCheck->execute();
    $duplicateCheck->bind_result($exists);
    $duplicateCheck->fetch();
    $duplicateCheck->close();

    if ($exists > 0) {
        return ['success' => false, 'message' => 'An evaluation has already been submitted for this student in the given course, section, semester, and year.'];
    }

    $takesCheck = $conn->prepare("
        SELECT COUNT(*)
        FROM takes
        WHERE student_id = ?
          AND course_id = ?
          AND section_id = ?
          AND year = ?
          AND semester = ?
    ");
    $takesCheck->bind_param("sssis", $studentId, $courseId, $sectionId, $year, $semester);
    $takesCheck->execute();
    $takesCheck->bind_result($isEnrolled);
    $takesCheck->fetch();
    $takesCheck->close();

    if ($isEnrolled == 0) {
        return ['success' => false, 'message' => 'Student is not enrolled in this course.'];
    }

    $stmt = $conn->prepare("
        INSERT INTO course_evaluation
        (student_id, course_id, section_id, semester, year, rating, comment)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("ssssiis", $studentId, $courseId, $sectionId, $semester, $year, $rating, $comment);

    if (!$stmt->execute()) {
        $message = "Evaluation submission failed: " . $stmt->error;
        $stmt->close();
        return ['success' => false, 'message' => $message];
    }

    $stmt->close();
    return ['success' => true, 'message' => 'Evaluation submitted successfully.'];
}