<?php
require_once __DIR__ . '/../config/db.php';

function grade_to_points(string $grade): float
{
    $map = [
        "A"  => 4.0,
        "A-" => 3.7,
        "B+" => 3.3,
        "B"  => 3.0,
        "B-" => 2.7,
        "C+" => 2.3,
        "C"  => 2.0,
        "D"  => 1.0,
        "F"  => 0.0
    ];

    return $map[$grade] ?? 0.0;
}

function is_passing_grade(?string $grade): bool
{
    return in_array($grade, ["A", "A-", "B+", "B", "B-", "C+", "C", "D"], true);
}

function has_submitted_evaluation(mysqli $conn, string $studentId, string $courseId, string $sectionId, string $semester, int $year): bool
{
    $stmt = $conn->prepare("
        SELECT 1
        FROM course_evaluation
        WHERE student_id = ?
          AND course_id = ?
          AND section_id = ?
          AND semester = ?
          AND year = ?
        LIMIT 1
    ");
    $stmt->bind_param("ssssi", $studentId, $courseId, $sectionId, $semester, $year);
    $stmt->execute();
    $exists = (bool)$stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $exists;
}

function fetch_transcript(string $studentId): array
{
    $conn = getDbConnection();

    $studentStmt = $conn->prepare("
        SELECT student_id, name, total_credit
        FROM student
        WHERE student_id = ?
        LIMIT 1
    ");
    $studentStmt->bind_param("s", $studentId);
    $studentStmt->execute();
    $student = $studentStmt->get_result()->fetch_assoc();
    $studentStmt->close();

    if (!$student) {
        return ['success' => false, 'message' => 'Student not found.'];
    }

    $stmt = $conn->prepare("
        SELECT t.course_id, c.title, c.credits, t.section_id, t.semester, t.year, t.grade
        FROM takes t
        JOIN course c ON t.course_id = c.course_id
        WHERE t.student_id = ?
        ORDER BY t.year, t.semester, t.course_id
    ");
    $stmt->bind_param("s", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();

    $courses = [];
    $earnedCredits = 0;
    $totalPoints = 0.0;
    $totalCreditsForGpa = 0;

    while ($row = $result->fetch_assoc()) {
        $grade = $row['grade'];
        $displayGrade = empty($grade) ? "In Progress" : $grade;
        $status = empty($grade) ? "Currently Taking" : "Completed";

        if (!empty($grade) && !has_submitted_evaluation(
            $conn,
            $studentId,
            $row['course_id'],
            $row['section_id'],
            $row['semester'],
            (int)$row['year']
        )) {
            $displayGrade = "Evaluation Required";
        }

        if (!empty($grade) && is_passing_grade($grade)) {
            $earnedCredits += (int)$row['credits'];
        }

        if (!empty($grade)) {
            $totalPoints += grade_to_points($grade) * (int)$row['credits'];
            $totalCreditsForGpa += (int)$row['credits'];
        }

        $courses[] = [
            'course_id' => $row['course_id'],
            'title' => $row['title'],
            'credits' => (int)$row['credits'],
            'section_id' => $row['section_id'],
            'semester' => $row['semester'],
            'year' => (int)$row['year'],
            'status' => $status,
            'grade' => $displayGrade
        ];
    }

    $stmt->close();

    $gpa = $totalCreditsForGpa === 0 ? 0.0 : round($totalPoints / $totalCreditsForGpa, 2);

    return [
        'success' => true,
        'student' => [
            'student_id' => $student['student_id'],
            'name' => $student['name'],
            'total_credit' => (int)$student['total_credit']
        ],
        'earned_credits' => $earnedCredits,
        'gpa' => $gpa,
        'courses' => $courses
    ];
}