# Mock University – Student Portal

An Android app and PHP API for a mock university, built by a team of three for the Database II course at UMass Lowell (Spring 2026). Students log in from the Android app to browse and register for courses, view their transcript, submit course evaluations and use a threaded discussion board. A MySQL/MariaDB database stores the data.

## Features

**Students**
- **Login and recovery:** log in with an email and password, reset a password, or look up your login information from your email.
- **Browse and register:** browse the Spring 2026 course offerings and register for a section. Registration is refused with a clear message if the add deadline has passed, you are already registered or already taking or completed the course, a prerequisite is missing, the schedule conflicts with another section, or the section is full (capacity 15).
- **Current enrollments and drop:** view current enrollments and drop a course until the drop deadline.
- **Transcript:** lists every course with its credits and grade, and calculates a credit-weighted GPA and earned credits. A completed course shows "Evaluation Required" in place of its grade until the student submits a course evaluation.
- **Course evaluations:** rate a course from 1 to 5 and leave a comment.
- **Discussion board:** post to the board of a course you are enrolled in and reply to other posts. Teaching assistants and graders can delete posts.

**Instructors and admins** can log in and reach their own home screens. The rest of the features above are for students.

## How It Fits Together

```
Android app (Java)  -->  HTTP requests  -->  PHP API (endpoints and services)  -->  MySQL / MariaDB (DB2)
```

| Folder | Contents |
| --- | --- |
| `phase3/app` | The Android app (activities, layouts and the API client). |
| `phase3/api` | The PHP API used by the app. `endpoints/` holds one file per request and `services/` holds the logic. |
| `phase3-sql/sql` | Database scripts for the app: `DB-tables.sql`, `DB-extra.sql` and `DB-data.sql`. |
| `phase2` | The earlier PHP web version, with its own SQL scripts. |

## Tech Stack

- Android (Java), minimum SDK 24
- PHP 8.2+ and Apache (XAMPP)
- MySQL/MariaDB
- Gradle (Android Studio)

## Setup

1. **Install the tools:** [XAMPP](https://www.apachefriends.org/) with Apache and MariaDB running, and [Android Studio](https://developer.android.com/studio).
2. **Create the database:** in phpMyAdmin, run the three scripts in `phase3-sql/sql` in this exact order: `DB-tables.sql`, `DB-extra.sql`, `DB-data.sql`. `DB-tables.sql` recreates the `DB2` database from scratch.
3. **Host the API:** put this repository's folder in XAMPP's `htdocs` as `database2`, so the API is served from `http://localhost/database2/phase3/api/endpoints/`. The database connection is set in `phase3/api/config/db.php`, and it expects the XAMPP defaults (user `root`, no password, database `DB2`).
4. **Open the app:** open the `phase3` folder in Android Studio and let Gradle sync.
5. **Set the server address:** in `ApiClient.java`, `BASE_URL` defaults to `http://10.0.2.2/database2/phase3/api/endpoints/`, which works on the Android emulator. On a real phone, replace `10.0.2.2` with your computer's local IP address.
6. **Run:** start an emulator or connect a device, then press Run.

## Test Accounts

The sample data creates an admin, 20 instructors and 50 students.

| Role | Email | Password |
| --- | --- | --- |
| Student | `alice_grant@student.uml.edu` | `S0000001` |
| Instructor | `james_chen@uml.edu` | `I-10001` |
| Admin | `admin@uml.edu` | `admin` |

Other students and instructors follow the same pattern: `firstname_lastname@student.uml.edu` (or `@uml.edu` for instructors), and the password is the user's ID. The sample add and drop deadlines for Spring 2026 are set to 2026-12-31.

## Credits and Notes

- The base schema in `DB-tables.sql` (classrooms, departments, courses, sections, students, instructors and related tables) comes from a script provided by the course instructor.
- The team added the `account` table, `email` columns, threaded discussion columns (`discussion_id`, `reply_id`) and the `registration_deadline` table (`DB-extra.sql`).
- The sample data in `DB-data.sql` was generated with Claude for testing.

## Known Limitations

This is a course project meant to run on a local machine.

- Passwords are stored and compared as plain text, and the API has no login tokens.
- The app talks to the API over plain HTTP, and the database uses the XAMPP root account with no password.
- Browsing is fixed to Spring 2026, and the section capacity of 15 is hard-coded in the API.

## My Contributions (Dhanvika Nakka)

- **Registration checks:** a student cannot register for a course unless they have passed its prerequisite (for example, CS-101 before CS-201), and cannot register for a section they are already registered in, so there are no duplicate enrollments.
- **Discussion board:** students can read the discussions of the sections they are enrolled in, post new messages and reply to a specific post. Teaching assistants and graders can delete posts, which the server checks before deleting.
- **Database extensions:** the `account` table used for login, `email` columns on `student` and `instructor`, the `discussion_id` and `reply_id` columns (with a new primary key) that make discussions threaded, and the `registration_deadline` table.

## Contributors

- [Nicholas Calabro](https://github.com/ncalabro18)
- [Akash Reddy Vangala](https://github.com/akashreddyvangala6-afk)
- [Dhanvika Nakka](https://github.com/dhan1801)


https://github.com/user-attachments/assets/85c933ca-436f-43e6-b39c-9586cd836521

