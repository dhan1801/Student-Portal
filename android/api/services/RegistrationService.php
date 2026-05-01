<?php
require_once __DIR__ . '/../config/db.php';

/**
 * Returns add/drop deadlines for the given semester, or null if not configured.
 */
function get_registration_deadlines(mysqli $conn, string $semester, int $year): ?array
{
    $stmt = $conn->prepare("
        SELECT add_deadline, drop_deadline
        FROM registration_deadline
        WHERE semester = ? AND year = ?
        LIMIT 1
    ");
    $stmt->bind_param("si", $semester, $year);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc() ?: null;
    $stmt->close();
    return $row;
}

/**
 * Returns all current-semester offerings with enrollment counts.
 * Uses a single SQL subquery — no N+1 queries.
 */
function fetch_browse_courses(string $semester, int $year): array
{
    $conn = getDbConnection();
    $stmt = $conn->prepare("
        SELECT
            s.course_id, c.title, s.section_id, s.semester, s.year,
            s.building, s.room_number, s.capacity,
            ts.day, ts.start_hour, ts.start_min, ts.end_hour, ts.end_min,
            (
                SELECT COUNT(*)
                FROM takes t
                WHERE t.course_id  = s.course_id
                  AND t.section_id = s.section_id
                  AND t.semester   = s.semester
                  AND t.year       = s.year
            ) AS enrolled
        FROM section s
        JOIN time_slot ts ON s.time_slot_id = ts.time_slot_id
        JOIN course c     ON s.course_id    = c.course_id
        WHERE s.semester = ? AND s.year = ?
        ORDER BY s.course_id, s.section_id
    ");
    $stmt->bind_param("si", $semester, $year);
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
}

/**
 * Returns true if the student is already registered in this exact section.
 */
function is_duplicate_registration(mysqli $conn, string $studentId, string $courseId,
                                   string $sectionId, string $semester, int $year): bool
{
    $stmt = $conn->prepare("
        SELECT 1 FROM takes
        WHERE student_id = ? AND course_id = ?
          AND section_id = ? AND semester  = ? AND year = ?
        LIMIT 1
    ");
    $stmt->bind_param("ssssi", $studentId, $courseId, $sectionId, $semester, $year);
    $stmt->execute();
    $exists = (bool) $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $exists;
}

/**
 * Returns true if the student is already taking this course in the same term
 * (any section).
 */
function same_course_same_term(mysqli $conn, string $studentId, string $courseId,
                               string $semester, int $year): bool
{
    $stmt = $conn->prepare("
        SELECT 1 FROM takes
        WHERE student_id = ? AND course_id = ?
          AND semester   = ? AND year      = ?
        LIMIT 1
    ");
    $stmt->bind_param("sssi", $studentId, $courseId, $semester, $year);
    $stmt->execute();
    $exists = (bool) $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $exists;
}

/**
 * Returns the passing grade record if the student already completed this course,
 * or null if they have not.
 */
function already_completed_course(mysqli $conn, string $studentId,
                                  string $courseId): ?array
{
    $stmt = $conn->prepare("
        SELECT semester, year, grade FROM takes
        WHERE student_id = ? AND course_id = ?
          AND grade IN ('A','A-','B+','B','B-','C+','C','D')
        ORDER BY year DESC
        LIMIT 1
    ");
    $stmt->bind_param("ss", $studentId, $courseId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc() ?: null;
    $stmt->close();
    return $row;
}

/**
 * Checks whether the student has passed all prerequisites for a course.
 * Returns an array of missing prereq course_ids, or an empty array if all met.
 */
function missing_prerequisites(mysqli $conn, string $studentId,
                               string $courseId): array
{
    // Fetch all prereqs for this course
    $stmt = $conn->prepare("
        SELECT prereq_id FROM prereq
        WHERE course_id = ?
    ");
    $stmt->bind_param("s", $courseId);
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    if (empty($rows)) {
        return []; // no prereqs required
    }

    $missing = [];
    foreach ($rows as $row) {
        $prereqId = $row['prereq_id'];

        // Student must have passed the prereq course with a passing grade
        $check = $conn->prepare("
            SELECT 1 FROM takes
            WHERE student_id = ? AND course_id = ?
              AND grade IN ('A','A-','B+','B','B-','C+','C','D')
            LIMIT 1
        ");
        $check->bind_param("ss", $studentId, $prereqId);
        $check->execute();
        $passed = (bool) $check->get_result()->fetch_assoc();
        $check->close();

        if (!$passed) {
            $missing[] = $prereqId;
        }
    }

    return $missing;
}

/**
 * Fetches section details including time slot.
 */
function fetch_section_with_timeslot(mysqli $conn, string $courseId,
                                     string $sectionId, string $semester,
                                     int $year): ?array
{
    $stmt = $conn->prepare("
        SELECT s.course_id, s.section_id, s.semester, s.year, s.capacity,
               ts.day, ts.start_hour, ts.start_min, ts.end_hour, ts.end_min
        FROM section s
        JOIN time_slot ts ON s.time_slot_id = ts.time_slot_id
        WHERE s.course_id  = ? AND s.section_id = ?
          AND s.semester   = ? AND s.year       = ?
        LIMIT 1
    ");
    $stmt->bind_param("sssi", $courseId, $sectionId, $semester, $year);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc() ?: null;
    $stmt->close();
    return $row;
}

/**
 * Returns the conflicting section if the student has a time-slot clash,
 * or null if no conflict exists.
 *
 * A conflict occurs when two sections meet on the same day AND either:
 *   (a) their times overlap, OR
 *   (b) the gap between them is less than 15 minutes.
 */
function conflicting_schedule(mysqli $conn, string $studentId,
                              array $targetSection): ?array
{
    $stmt = $conn->prepare("
        SELECT t.course_id, t.section_id,
               ts.day, ts.start_hour, ts.start_min, ts.end_hour, ts.end_min
        FROM takes t
        JOIN section s
          ON t.course_id = s.course_id AND t.section_id = s.section_id
         AND t.semester  = s.semester  AND t.year       = s.year
        JOIN time_slot ts ON s.time_slot_id = ts.time_slot_id
        WHERE t.student_id = ? AND t.semester = ? AND t.year = ?
          AND t.grade IS NULL
    ");
    $stmt->bind_param("ssi", $studentId, $targetSection['semester'], $targetSection['year']);
    $stmt->execute();
    $result = $stmt->get_result();

    $newStart = ((int)$targetSection['start_hour'] * 60) + (int)$targetSection['start_min'];
    $newEnd   = ((int)$targetSection['end_hour']   * 60) + (int)$targetSection['end_min'];

    while ($row = $result->fetch_assoc()) {
        if ($row['day'] !== $targetSection['day']) {
            continue;
        }

        $existStart = ((int)$row['start_hour'] * 60) + (int)$row['start_min'];
        $existEnd   = ((int)$row['end_hour']   * 60) + (int)$row['end_min'];

        // Overlap: times intersect directly
        $overlaps = ($newStart < $existEnd) && ($existStart < $newEnd);

        // Gap < 15 min: one ends and the other starts too soon after
        $tooClose = ($newStart >= $existEnd   && $newStart - $existEnd   < 15)
                 || ($existStart >= $newEnd   && $existStart - $newEnd   < 15);

        if ($overlaps || $tooClose) {
            $stmt->close();
            return $row;
        }
    }

    $stmt->close();
    return null;
}

/**
 * Returns the current enrollment count for a section.
 */
function current_section_enrollment(mysqli $conn, string $courseId,
                                    string $sectionId, string $semester,
                                    int $year): int
{
    $stmt = $conn->prepare("
        SELECT COUNT(*) AS total FROM takes
        WHERE course_id  = ? AND section_id = ?
          AND semester   = ? AND year       = ?
    ");
    $stmt->bind_param("sssi", $courseId, $sectionId, $semester, $year);
    $stmt->execute();
    $total = (int) $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();
    return $total;
}

/**
 * Registers a student for a section after running all validation checks:
 *   1. Add deadline not passed
 *   2. Not already registered in this section
 *   3. Not already taking this course this term
 *   4. Not already completed this course
 *   5. All prerequisites passed
 *   6. No time-slot conflict
 *   7. Section not full (capacity 15)
 */
function register_student(string $studentId, string $courseId,
                          string $sectionId, string $semester, int $year): array
{
    $conn = getDbConnection();

    $deadlines = get_registration_deadlines($conn, $semester, $year);
    if (!$deadlines) {
        return ['success' => false, 'message' => 'Registration not configured for this semester.'];
    }

    if (date('Y-m-d') > $deadlines['add_deadline']) {
        return ['success' => false, 'message' => 'Registration failed. Add deadline has passed.'];
    }

    if (is_duplicate_registration($conn, $studentId, $courseId, $sectionId, $semester, $year)) {
        return ['success' => false, 'message' => 'You are already registered in this section.'];
    }

    if (same_course_same_term($conn, $studentId, $courseId, $semester, $year)) {
        return ['success' => false, 'message' => 'You are already registered for this course this semester.'];
    }

    $completed = already_completed_course($conn, $studentId, $courseId);
    if ($completed) {
        return ['success' => false, 'message' =>
            "You already completed {$courseId} in {$completed['semester']} {$completed['year']} with grade {$completed['grade']}."
        ];
    }

    // Prerequisite check
    $missing = missing_prerequisites($conn, $studentId, $courseId);
    if (!empty($missing)) {
        $list = implode(', ', $missing);
        return ['success' => false, 'message' =>
            "Registration failed. You have not completed the required prerequisite(s): {$list}."
        ];
    }

    $section = fetch_section_with_timeslot($conn, $courseId, $sectionId, $semester, $year);
    if (!$section) {
        return ['success' => false, 'message' => 'Section not found.'];
    }

    $conflict = conflicting_schedule($conn, $studentId, $section);
    if ($conflict) {
        return ['success' => false, 'message' =>
            "Schedule conflict with {$conflict['course_id']} section {$conflict['section_id']}: "
            . "sections on the same day must have at least a 15-minute gap."
        ];
    }

    $enrolled = current_section_enrollment($conn, $courseId, $sectionId, $semester, $year);
    if ($enrolled >= 15) {
        return ['success' => false, 'message' => 'Section is full (capacity: 15).'];
    }

    $stmt = $conn->prepare("
        INSERT INTO takes (student_id, course_id, section_id, semester, year, grade)
        VALUES (?, ?, ?, ?, ?, NULL)
    ");
    $stmt->bind_param("ssssi", $studentId, $courseId, $sectionId, $semester, $year);

    if (!$stmt->execute()) {
        $err = $stmt->error;
        $stmt->close();
        return ['success' => false, 'message' => 'Registration failed: ' . $err];
    }

    $stmt->close();
    return ['success' => true, 'message' => 'Registered successfully.'];
}

/**
 * Returns a student's current enrollments (no grade yet) for a given term.
 */
function fetch_current_enrollments(string $studentId, string $semester,
                                   int $year): array
{
    $conn = getDbConnection();
    $stmt = $conn->prepare("
        SELECT t.course_id, c.title, t.section_id, t.semester, t.year,
               s.building, s.room_number,
               ts.day, ts.start_hour, ts.start_min, ts.end_hour, ts.end_min
        FROM takes t
        JOIN course    c  ON t.course_id  = c.course_id
        JOIN section   s  ON t.course_id  = s.course_id
                         AND t.section_id = s.section_id
                         AND t.semester   = s.semester
                         AND t.year       = s.year
        JOIN time_slot ts ON s.time_slot_id = ts.time_slot_id
        WHERE t.student_id = ? AND t.semester = ? AND t.year = ?
        ORDER BY ts.day, ts.start_hour, ts.start_min
    ");
    $stmt->bind_param("ssi", $studentId, $semester, $year);
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
}

/**
 * Drops a student from a course section after checking the drop deadline
 * and confirming active enrollment.
 */
function drop_student_course(string $studentId, string $courseId,
                             string $sectionId, string $semester, int $year): array
{
    $conn = getDbConnection();

    $deadlines = get_registration_deadlines($conn, $semester, $year);
    if (!$deadlines) {
        return ['success' => false, 'message' => 'Drop rules not configured for this semester.'];
    }

    if (date('Y-m-d') > $deadlines['drop_deadline']) {
        return ['success' => false, 'message' => 'Drop failed. Drop deadline has passed.'];
    }

    $check = $conn->prepare("
        SELECT 1 FROM takes
        WHERE student_id = ? AND course_id = ?
          AND section_id = ? AND semester  = ? AND year = ?
          AND grade IS NULL
        LIMIT 1
    ");
    $check->bind_param("ssssi", $studentId, $courseId, $sectionId, $semester, $year);
    $check->execute();
    $exists = (bool) $check->get_result()->fetch_assoc();
    $check->close();

    if (!$exists) {
        return ['success' => false, 'message' => 'You are not currently enrolled in this section.'];
    }

    $stmt = $conn->prepare("
        DELETE FROM takes
        WHERE student_id = ? AND course_id = ?
          AND section_id = ? AND semester  = ? AND year = ?
          AND grade IS NULL
    ");
    $stmt->bind_param("ssssi", $studentId, $courseId, $sectionId, $semester, $year);

    if (!$stmt->execute()) {
        $err = $stmt->error;
        $stmt->close();
        return ['success' => false, 'message' => 'Drop failed: ' . $err];
    }

    $stmt->close();
    return ['success' => true, 'message' => 'Course dropped successfully.'];
}