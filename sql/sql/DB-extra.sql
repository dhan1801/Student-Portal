USE db2;

-- created an account table to store login details
CREATE TABLE account
(
    id        VARCHAR(8) NOT NULL,
    email    VARCHAR(50)  NOT NULL,
    password VARCHAR(100) NOT NULL,
    type     VARCHAR(20)  NOT NULL,
    PRIMARY KEY (email)
);

-- adding email column to student table so students can log in
ALTER TABLE student
ADD COLUMN email VARCHAR(50) UNIQUE AFTER student_id;

-- adding email column to instructor table so instructors can also log in
ALTER TABLE instructor
ADD COLUMN email VARCHAR(50) UNIQUE AFTER instructor_id;

-- discussion edits: add display columns with defaults
ALTER TABLE discussion
ADD COLUMN discussion_id VARCHAR(8) NOT NULL DEFAULT '0',
ADD COLUMN reply_id VARCHAR(8) NOT NULL DEFAULT '0';

-- Drop foreign keys before modifying the primary key
ALTER TABLE discussion DROP FOREIGN KEY discussion_ibfk_1;
ALTER TABLE discussion DROP FOREIGN KEY discussion_ibfk_2;

-- Replace PK to allow multiple posts per student per section
ALTER TABLE discussion DROP PRIMARY KEY;
ALTER TABLE discussion ADD PRIMARY KEY (discussion_id, course_id, section_id, semester, year);

-- Re-add foreign keys
ALTER TABLE discussion
ADD CONSTRAINT discussion_ibfk_1
FOREIGN KEY (course_id, section_id, semester, year)
REFERENCES section (course_id, section_id, semester, year)
ON DELETE CASCADE;

ALTER TABLE discussion
ADD CONSTRAINT discussion_ibfk_2
FOREIGN KEY (student_id)
REFERENCES student (student_id)
ON DELETE CASCADE;

CREATE TABLE registration_deadline (
    semester VARCHAR(10) NOT NULL,
    year INT NOT NULL,
    add_deadline DATE NOT NULL,
    drop_deadline DATE NOT NULL,
    PRIMARY KEY (semester, year)
);