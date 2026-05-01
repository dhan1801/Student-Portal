<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);

require_once "../config/db.php";
header("Content-Type: application/json");

$student_id = trim($_GET["student_id"] ?? "");

if ($student_id === "") {
    echo json_encode([
        "success" => false,
        "message" => "student_id is required"
    ]);
    exit;
}

$sql = "
    SELECT c.course_id, c.title, t.semester, t.year, t.grade, c.credits
    FROM takes t
    JOIN course c ON t.course_id = c.course_id
    WHERE t.student_id = ?
    ORDER BY t.year, t.semester, t.course_id
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Prepare failed: " . $conn->error]);
    exit;
}
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

$courses = [];
$totalCredits = 0;
$totalPoints = 0.0;

$gradeMap = [
    "A" => 4.0, "A-" => 3.7,
    "B+" => 3.3, "B" => 3.0, "B-" => 2.7,
    "C+" => 2.3, "C" => 2.0,
    "D" => 1.0, "F" => 0.0
];

while ($row = $result->fetch_assoc()) {
    $displayGrade = $row["grade"] === null ? "In Progress" : $row["grade"];

    $courses[] = [
        "course_id" => $row["course_id"],
        "title" => $row["title"],
        "semester" => $row["semester"],
        "year" => (int)$row["year"],
        "grade" => $displayGrade
    ];

    if ($row["grade"] !== null && isset($gradeMap[$row["grade"]])) {
        $credits = (int)$row["credits"];
        $totalCredits += $credits;
        $totalPoints += $gradeMap[$row["grade"]] * $credits;
    }
}
$stmt->close();

$gpa = $totalCredits > 0 ? round($totalPoints / $totalCredits, 2) : 0.0;

echo json_encode([
    "success" => true,
    "courses" => $courses,
    "gpa" => $gpa,
    "earned_credits" => $totalCredits
]);
exit;