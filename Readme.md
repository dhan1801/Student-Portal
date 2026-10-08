# Student Portal Mobile Application

An Android student portal built by a team of three for the Database II course at UMass Lowell (Spring 2026). Students and instructors log in to an Android app backed by a relational SQL database that stores accounts, courses, enrollments, grades and course discussions.

## Features

- **Accounts and login:** a shared `account` table with student and instructor accounts, each linked to a student or instructor record by email.
- **Discussion board:** students post to a course section's board, and replies are threaded under the post they answer. Supports create, read, update and delete (CRUD) operations.
- **Transcript:** shows each student's courses with a credit-weighted GPA and total earned credits.
- **Course evaluations:** a completed course shows "Evaluation Required" in place of the grade until the student submits an evaluation.
- **Add/drop deadlines:** a `registration_deadline` table stores each term's add and drop dates.

## Tech Stack

- Android (Java)
- SQL (relational schema, constraints and queries)
- PHP (backend transcript logic) `[PLACEHOLDER: confirm which folder holds the PHP code, or remove this line]`

## Repository Structure

```
android/   Android app source
sql/       Database scripts (schema extensions and sample data)
```

## My Contributions

`[PLACEHOLDER: list exactly what you built, for example the transcript module, discussion board, schema changes. Keep it to work that is yours.]`

## Database Notes

- The base schema (departments, courses, sections, students, instructors and related tables) comes from a script provided by the course instructor, and it is not my original work.
- The team's additions to that schema are the `account` table, `email` columns on `student` and `instructor`, threaded discussion columns (`discussion_id`, `reply_id`) and the `registration_deadline` table.
- The sample data (students, instructors, courses and enrollments) was generated with Claude for testing.

## Running the Project

1. Create the database by running the course schema script, then the extension script in `sql/`. `[PLACEHOLDER: exact file names and order]`
2. Load the sample data script. `[PLACEHOLDER: file name]`
3. Open the `android/` folder in Android Studio and run the app. `[PLACEHOLDER: minimum Android version, and how the app reaches the database]`

## Team

- Dhanvika Nakka
- `[PLACEHOLDER: teammate name]`
- `[PLACEHOLDER: teammate name]`
