-- Copyright (C) 2026 James Chen
--
-- MySQL schema setup script for the Database II course at UMass Lowell (Spring 2026).
-- It resets the DB2 database and recreates the full schema from scratch.
--
-- Warning: Running this script on an existing database will remove current table data.
-- Back up your database before executing this file.
--
-- Expected database name: `DB2`.
--
-- With Apache and MySQL services running with XAMPP, you can visit phpMyAdmin at
--
--     http://localhost/phpmyadmin/
-- 
-- to manage your MySQL databases. Select the `SQL` tab, copy and paste the contents of this file
-- into the text area, and click the `Go` button at the bottom.
--
-- Feel free to add functions, constraints, views, triggers, and any other SQL objects to the
-- database as needed for your assignments. For reproducibility, please always include the SQL code
-- for creating those objects in your assignment submission.
--
-- If this script does not work as expected, please open an issue on the GitHub repository for this
-- course:
--
--     https://github.com/TypingHare/database2-movie-demo
--
-- The formatter of this SQL file is sqruff, with 100 character line width and 4 spaces for
-- indentation.

-- Reset the whole database and all contained objects.
DROP DATABASE IF EXISTS DB2;
CREATE DATABASE DB2;
USE DB2;

-- The `classroom` table stores classrooms in the system.
CREATE TABLE classroom (
    building VARCHAR(15)
    COMMENT 'The building where the classroom is.',

    room_number VARCHAR(7)
    COMMENT 'The room number of the classroom.',

    capacity NUMERIC(4, 0) NOT NULL
    COMMENT 'The number of students the classroom can hold.',

    PRIMARY KEY (building, room_number)
);

-- The `department` table stores departments in the system.
CREATE TABLE department (
    dept_name VARCHAR(20)
    COMMENT 'The name of the department.',

    building VARCHAR(15)
    COMMENT 'The building where the department is in.',

    budget BIGINT
    COMMENT 'The budget of the department in U.S. cents.'
    CHECK (budget >= 0),

    PRIMARY KEY (dept_name)
);

-- The `course` table stores courses the system provides.
CREATE TABLE course (
    course_id VARCHAR(8)
    COMMENT 'The ID of the course.',

    title VARCHAR(50)
    COMMENT 'The title of the course.',

    dept_name VARCHAR(20)
    COMMENT 'The name of the department managing the course.',

    credits NUMERIC(2, 0)
    COMMENT 'The number of credits student can earn after taking the course.'
    CHECK (credits > 0),

    PRIMARY KEY (course_id),
    FOREIGN KEY (dept_name) REFERENCES department (dept_name)
    ON DELETE SET NULL
);

-- The `instructor` table stores instructors in the system.
CREATE TABLE instructor (
    instructor_id VARCHAR(8)
    COMMENT 'The ID of the instructor.',

    name VARCHAR(20) NOT NULL
    COMMENT 'The name of the instructor.',

    dept_name VARCHAR(20)
    COMMENT 'The department where the instructor works.',

    salary BIGINT
    COMMENT 'The instructor salary in U.S. cents.'
    CHECK (salary > 2900000),

    PRIMARY KEY (instructor_id),
    FOREIGN KEY (dept_name) REFERENCES department (dept_name)
    ON DELETE SET NULL
);

-- The `section` table stores specific course offerings by term.
CREATE TABLE section (
    course_id VARCHAR(8)
    COMMENT 'The course ID this section belongs to.',

    section_id VARCHAR(8)
    COMMENT 'The section identifier.',

    semester VARCHAR(6)
    COMMENT 'The semester when this section is offered.'
    CHECK (semester IN ('Fall', 'Winter', 'Spring', 'Summer')),

    year NUMERIC(4, 0)
    COMMENT 'The year when this section is offered.'
    CHECK (year > 1701 AND year < 2100),

    building VARCHAR(15)
    COMMENT 'The building where this section meets.',

    room_number VARCHAR(7)
    COMMENT 'The room number where this section meets.',

    time_slot_id VARCHAR(4)
    COMMENT 'The time slot identifier for this section.',

    capacity NUMERIC(4, 0) NOT NULL
    COMMENT 'The maximum number of students that can enroll in this section.',

    PRIMARY KEY (course_id, section_id, semester, year),
    FOREIGN KEY (course_id) REFERENCES course (course_id)
    ON DELETE CASCADE,
    FOREIGN KEY (building, room_number) REFERENCES classroom (
        building, room_number
    )
    ON DELETE SET NULL
);

-- The `teaches` table maps instructors to sections they teach.
CREATE TABLE teaches (
    instructor_id VARCHAR(8)
    COMMENT 'The instructor ID.',

    course_id VARCHAR(8)
    COMMENT 'The course ID being taught.',

    section_id VARCHAR(8)
    COMMENT 'The section ID being taught.',

    semester VARCHAR(6)
    COMMENT 'The semester of the taught section.',

    year NUMERIC(4, 0)
    COMMENT 'The year of the taught section.',

    PRIMARY KEY (instructor_id, course_id, section_id, semester, year),
    FOREIGN KEY (course_id, section_id, semester, year)
    REFERENCES section (course_id, section_id, semester, year)
    ON DELETE CASCADE,
    FOREIGN KEY (instructor_id) REFERENCES instructor (instructor_id)
    ON DELETE CASCADE
);

-- The `student` table stores students in the system.
CREATE TABLE student (
    student_id VARCHAR(8)
    COMMENT 'The ID of the student.',

    name VARCHAR(20) NOT NULL
    COMMENT 'The name of the student.',

    dept_name VARCHAR(20)
    COMMENT 'The department where the student is enrolled.',

    total_credit NUMERIC(3, 0)
    COMMENT 'The total credits earned by the student.'
    CHECK (total_credit >= 0),

    PRIMARY KEY (student_id),
    FOREIGN KEY (dept_name) REFERENCES department (dept_name)
    ON DELETE SET NULL
);

-- The `takes` table maps students to sections they take.
CREATE TABLE takes (
    student_id VARCHAR(8)
    COMMENT 'The student ID.',

    course_id VARCHAR(8)
    COMMENT 'The course ID being taken.',

    section_id VARCHAR(8)
    COMMENT 'The section ID being taken.',

    semester VARCHAR(6)
    COMMENT 'The semester of the taken section.',

    year NUMERIC(4, 0)
    COMMENT 'The year of the taken section.',

    grade VARCHAR(2)
    COMMENT 'The grade earned by the student for the section.',

    PRIMARY KEY (student_id, course_id, section_id, semester, year),
    FOREIGN KEY (course_id, section_id, semester, year)
    REFERENCES section (course_id, section_id, semester, year)
    ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES student (student_id)
    ON DELETE CASCADE
);

-- The `advising` table stores advising relationships for students.
CREATE TABLE advising (
    student_id VARCHAR(8)
    COMMENT 'The student ID of the advisee.',

    instructor_id VARCHAR(8)
    COMMENT 'The instructor ID of the advisor.',

    PRIMARY KEY (student_id),
    FOREIGN KEY (instructor_id) REFERENCES instructor (instructor_id)
    ON DELETE SET NULL,
    FOREIGN KEY (student_id) REFERENCES student (student_id)
    ON DELETE CASCADE
);

-- The `time_slot` table stores possible class time slots.
CREATE TABLE time_slot (
    time_slot_id VARCHAR(4)
    COMMENT 'The ID of the time slot.',

    day VARCHAR(1)
    COMMENT 'The day code for the time slot.',

    start_hour NUMERIC(2)
    COMMENT 'Start hour in 24-hour format.'
    CHECK (start_hour >= 0 AND start_hour < 24),

    start_min NUMERIC(2)
    COMMENT 'Start minute.'
    CHECK (start_min >= 0 AND start_min < 60),

    end_hour NUMERIC(2)
    COMMENT 'End hour in 24-hour format.'
    CHECK (end_hour >= 0 AND end_hour < 24),

    end_min NUMERIC(2)
    COMMENT 'End minute.'
    CHECK (end_min >= 0 AND end_min < 60),

    PRIMARY KEY (time_slot_id)
);

ALTER TABLE section
ADD FOREIGN KEY (time_slot_id) REFERENCES time_slot (time_slot_id)
ON DELETE SET NULL;

-- The `prereq` table stores course prerequisite relationships.
CREATE TABLE prereq (
    course_id VARCHAR(8)
    COMMENT 'The course ID that has a prerequisite.',

    prereq_id VARCHAR(8)
    COMMENT 'The prerequisite course ID.',

    PRIMARY KEY (course_id, prereq_id),
    FOREIGN KEY (course_id) REFERENCES course (course_id)
    ON DELETE CASCADE,
    FOREIGN KEY (prereq_id) REFERENCES course (course_id)
);

-- The `teacher_assistant` table maps students to sections they assist as teacher assistants.
CREATE TABLE teacher_assistant (
    student_id VARCHAR(8)
    COMMENT 'The student ID of the teacher assistant.',

    course_id VARCHAR(8)
    COMMENT 'The course ID being assisted.',

    section_id VARCHAR(8)
    COMMENT 'The section ID being assisted.',

    semester VARCHAR(6)
    COMMENT 'The semester of the assisted section.',

    year NUMERIC(4, 0)
    COMMENT 'The year of the assisted section.',

    PRIMARY KEY (student_id, course_id, section_id, semester, year),
    FOREIGN KEY (course_id, section_id, semester, year)
    REFERENCES section (course_id, section_id, semester, year)
    ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES student (student_id)
    ON DELETE CASCADE
);

-- The `grader` table maps students to sections they assist as graders.
CREATE TABLE grader (
    student_id VARCHAR(8)
    COMMENT 'The student ID of the grader.',

    course_id VARCHAR(8)
    COMMENT 'The course ID being graded.',

    section_id VARCHAR(8)
    COMMENT 'The section ID being graded.',

    semester VARCHAR(6)
    COMMENT 'The semester of the graded section.',

    year NUMERIC(4, 0)
    COMMENT 'The year of the graded section.',

    PRIMARY KEY (student_id, course_id, section_id, semester, year),
    FOREIGN KEY (course_id, section_id, semester, year)
    REFERENCES section (course_id, section_id, semester, year)
    ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES student (student_id)
    ON DELETE CASCADE
);

-- The `undergraduate` table stores undergraduate-specific student attributes.
CREATE TABLE undergraduate (
    student_id VARCHAR(8)
    COMMENT 'The ID of the undergraduate student.',

    class_year NUMERIC(1, 0)
    COMMENT 'The undergraduate class year (1 to 4).'
    CHECK (class_year IN (1, 2, 3, 4)),

    major_dept_name VARCHAR(20)
    COMMENT 'The major department name of the undergraduate student.',

    PRIMARY KEY (student_id),
    FOREIGN KEY (student_id) REFERENCES student (student_id)
    ON DELETE CASCADE,
    FOREIGN KEY (major_dept_name) REFERENCES department (dept_name)
    ON DELETE SET NULL
);

-- The `master` table stores master-student-specific attributes.
CREATE TABLE master (
    student_id VARCHAR(8)
    COMMENT 'The ID of the master student.',

    PRIMARY KEY (student_id),
    FOREIGN KEY (student_id) REFERENCES student (student_id)
    ON DELETE CASCADE
);

-- The `phd` table stores PhD-student-specific attributes.
CREATE TABLE phd (
    student_id VARCHAR(8)
    COMMENT 'The ID of the PhD student.',

    dissertation_title VARCHAR(150)
    COMMENT 'The dissertation title of the PhD student.',

    qualifier_passed BOOLEAN
    COMMENT 'Whether the PhD student has passed the qualifying exam.'
    DEFAULT FALSE,

    PRIMARY KEY (student_id),
    FOREIGN KEY (student_id) REFERENCES student (student_id)
    ON DELETE CASCADE
);

-- The `discussion` table stores discussions posted by students for specific sections.
CREATE TABLE discussion (
    student_id VARCHAR(8)
    COMMENT 'The ID of the student who posts the discussion.',

    course_id VARCHAR(8)
    COMMENT 'The course ID associated with the discussion.',

    section_id VARCHAR(8)
    COMMENT 'The section ID associated with the discussion.',

    semester VARCHAR(6)
    COMMENT 'The semester of the section.',

    year NUMERIC(4, 0)
    COMMENT 'The year of the section.',

    content TEXT
    COMMENT 'The content of the discussion.',

    PRIMARY KEY (student_id, course_id, section_id, semester, year),
    FOREIGN KEY (course_id, section_id, semester, year)
    REFERENCES section (course_id, section_id, semester, year)
    ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES student (student_id)
    ON DELETE CASCADE
);

-- The `course_evaluation` table stores course evaluation entries created by students.
CREATE TABLE course_evaluation (
    student_id VARCHAR(8)
    COMMENT 'The ID of the student who evaluates the course.',

    course_id VARCHAR(8)
    COMMENT 'The course ID associated with the evaluation.',

    section_id VARCHAR(8)
    COMMENT 'The section ID associated with the evaluation.',

    semester VARCHAR(6)
    COMMENT 'The semester of the section.',

    year NUMERIC(4, 0)
    COMMENT 'The year of the section.',

    rating NUMERIC(1, 0)
    COMMENT 'Student rating to the course from 1 to 5.'
    CHECK (rating >= 1 AND rating <= 5),

    comment TEXT DEFAULT ''
    COMMENT 'Student comment on the course.',

    PRIMARY KEY (student_id, course_id, section_id, semester, year),
    FOREIGN KEY (course_id, section_id, semester, year)
    REFERENCES section (course_id, section_id, semester, year),
    FOREIGN KEY (student_id) REFERENCES student (student_id)
    ON DELETE CASCADE
);
