-- ============================================================
-- DB2 Seed Data — FINAL CORRECT (zero violations verified)
-- Run order: DB-tables.sql -> DB-extra.sql -> THIS FILE ONLY
-- ============================================================
USE DB2;

INSERT INTO account (id, email, password, type) VALUES ('admin', 'admin@uml.edu', 'admin', 'admin');

INSERT INTO classroom (building, room_number, capacity) VALUES
('Olsen','101',50),('Olsen','102',35),('Olsen','201',60),('Olsen','202',40),
('Falmouth','101',45),('Falmouth','102',30),('Falmouth','201',55),
('McGauvran','101',80),('McGauvran','102',25),('McGauvran','201',70),
('Perry','101',40),('Perry','102',35),('Perry','201',50),
('Dugan','101',60),('Dugan','102',45),('Dugan','201',30),
('South','101',100),('South','102',75),('Ball','101',50),('Ball','102',40);

INSERT INTO department (dept_name, building, budget) VALUES
('Comp. Sci.','Olsen',150000000),('Mathematics','Olsen',120000000),
('Physics','Falmouth',100000000),('Chemistry','Falmouth',110000000),
('Biology','McGauvran',130000000),('English','Perry',80000000),
('History','Perry',75000000),('Economics','Dugan',140000000),
('Psychology','Dugan',90000000),('Music','South',60000000),
('Engineering','Ball',160000000),('Philosophy','Ball',50000000);

INSERT INTO time_slot (time_slot_id, day, start_hour, start_min, end_hour, end_min) VALUES
('A','M',8,0,9,15),('B','T',8,0,9,15),('C','W',8,0,9,15),('D','R',8,0,9,15),
('E','F',8,0,9,15),('F','M',9,30,10,45),('G','T',9,30,10,45),('H','W',9,30,10,45),
('I','R',9,30,10,45),('J','F',9,30,10,45),('K','M',11,0,12,15),('L','T',11,0,12,15),
('M','W',11,0,12,15),('N','R',11,0,12,15),('O','F',11,0,12,15),('P','M',13,0,14,15),
('Q','T',13,0,14,15),('R','W',13,0,14,15),('S','R',15,0,16,15),('T','F',15,0,16,15);

INSERT INTO course (course_id, title, dept_name, credits) VALUES
('CS-101','Intro to CS','Comp. Sci.',3),('CS-201','Data Structures','Comp. Sci.',3),
('CS-301','Algorithms','Comp. Sci.',3),('CS-401','Operating Systems','Comp. Sci.',3),
('CS-501','Machine Learning','Comp. Sci.',3),('CS-601','Advanced Databases','Comp. Sci.',3),
('MA-101','Calculus I','Mathematics',4),('MA-201','Calculus II','Mathematics',4),
('MA-301','Linear Algebra','Mathematics',3),('MA-401','Probability','Mathematics',3),
('PH-101','Physics I','Physics',4),('PH-201','Physics II','Physics',4),
('PH-301','Quantum Mechanics','Physics',3),('CH-101','Chemistry I','Chemistry',4),
('CH-201','Chemistry II','Chemistry',4),('BI-101','Biology I','Biology',3),
('BI-201','Biology II','Biology',3),('EN-101','English Comp','English',3),
('EN-201','Literature','English',3),('HI-101','World History','History',3),
('HI-201','US History','History',3),('EC-101','Microeconomics','Economics',3),
('EC-201','Macroeconomics','Economics',3),('PS-101','Intro Psychology','Psychology',3),
('MU-101','Music Theory','Music',2);

INSERT INTO instructor (instructor_id, name, dept_name, salary) VALUES
('I-10001','James Chen','Comp. Sci.',12000000),('I-10002','Sarah Miller','Comp. Sci.',11500000),
('I-10003','Robert Lee','Comp. Sci.',10800000),('I-10004','Emily Davis','Mathematics',11000000),
('I-10005','Michael Brown','Mathematics',10500000),('I-10006','Linda Wilson','Physics',11200000),
('I-10007','David Taylor','Physics',10900000),('I-10008','Susan Anderson','Chemistry',10700000),
('I-10009','Kevin Thomas','Chemistry',10300000),('I-10010','Nancy Martin','Biology',10600000),
('I-10011','Paul Jackson','Biology',10200000),('I-10012','Karen White','English',9500000),
('I-10013','Steven Harris','English',9200000),('I-10014','Betty Clark','History',9400000),
('I-10015','George Lewis','History',9100000),('I-10016','Sandra Robinson','Economics',11800000),
('I-10017','Edward Walker','Economics',11400000),('I-10018','Donna Hall','Psychology',9800000),
('I-10019','Charles Young','Psychology',9600000),('I-10020','Ruth King','Music',8500000);

-- Consecutive slots: F+K (Mon), H+M (Wed), K+P (Mon)
INSERT INTO section (course_id, section_id, semester, year, building, room_number, time_slot_id, capacity) VALUES
('CS-101','1','Fall',2022,'Olsen','101','A',50),
('CS-101','1','Fall',2023,'Olsen','101','A',50),
('CS-101','2','Fall',2023,'Olsen','201','F',60),
('CS-201','1','Fall',2023,'Olsen','102','K',35),
('MA-101','1','Fall',2023,'Olsen','201','C',60),
('MA-101','2','Fall',2023,'Falmouth','101','D',45),
('PH-101','1','Fall',2023,'Falmouth','201','E',55),
('CH-101','1','Fall',2023,'Falmouth','102','G',30),
('BI-101','1','Fall',2023,'McGauvran','101','H',80),
('EN-101','1','Fall',2023,'Perry','101','I',40),
('EN-101','2','Fall',2023,'Perry','201','S',50),
('HI-101','1','Fall',2023,'Perry','201','P',50),
('EC-101','1','Fall',2023,'Dugan','101','L',60),
('PS-101','1','Fall',2023,'Dugan','102','B',45),
('CS-101','1','Spring',2024,'Olsen','101','K',50),
('CS-201','1','Spring',2024,'Olsen','102','G',35),
('CS-301','1','Spring',2024,'Olsen','202','E',40),
('MA-101','1','Spring',2024,'Olsen','201','M',60),
('MA-201','1','Spring',2024,'Falmouth','201','H',55),
('PH-201','1','Spring',2024,'Falmouth','101','J',55),
('CH-201','1','Spring',2024,'Falmouth','102','L',30),
('BI-201','1','Spring',2024,'McGauvran','101','O',80),
('EN-201','1','Spring',2024,'Perry','102','T',35),
('HI-201','1','Spring',2024,'Perry','101','Q',40),
('EC-201','1','Spring',2024,'Dugan','101','R',60),
('MU-101','1','Spring',2024,'South','101','S',100),
('CS-401','1','Fall',2024,'Olsen','201','P',60),
('CS-601','1','Fall',2024,'Olsen','102','K',35),
('MA-301','1','Fall',2024,'Olsen','202','C',40),
('PH-301','1','Fall',2024,'Falmouth','102','B',30),
('CS-501','1','Spring',2025,'Olsen','202','Q',40),
('CS-101','1','Spring',2026,'Olsen','101','K',50),
('CS-201','1','Spring',2026,'Olsen','102','G',35),
('MA-101','1','Spring',2026,'Olsen','201','M',60),
('PH-101','1','Spring',2026,'Falmouth','201','E',55),
('EN-101','1','Spring',2026,'Perry','101','I',40),
('EC-101','1','Spring',2026,'Dugan','101','K',60);

INSERT INTO teaches (instructor_id, course_id, section_id, semester, year) VALUES
('I-10001','CS-101','1','Fall',2022),
('I-10001','CS-101','1','Fall',2023),('I-10002','CS-101','2','Fall',2023),
('I-10002','CS-201','1','Fall',2023),('I-10004','MA-101','1','Fall',2023),
('I-10005','MA-101','2','Fall',2023),('I-10006','PH-101','1','Fall',2023),
('I-10008','CH-101','1','Fall',2023),('I-10010','BI-101','1','Fall',2023),
('I-10012','EN-101','1','Fall',2023),('I-10013','EN-101','2','Fall',2023),
('I-10014','HI-101','1','Fall',2023),('I-10016','EC-101','1','Fall',2023),
('I-10018','PS-101','1','Fall',2023),
('I-10001','CS-101','1','Spring',2024),('I-10002','CS-201','1','Spring',2024),
('I-10003','CS-301','1','Spring',2024),('I-10004','MA-101','1','Spring',2024),
('I-10004','MA-201','1','Spring',2024),('I-10007','PH-201','1','Spring',2024),
('I-10009','CH-201','1','Spring',2024),('I-10011','BI-201','1','Spring',2024),
('I-10013','EN-201','1','Spring',2024),('I-10015','HI-201','1','Spring',2024),
('I-10017','EC-201','1','Spring',2024),('I-10020','MU-101','1','Spring',2024),
('I-10003','CS-401','1','Fall',2024),('I-10003','CS-601','1','Fall',2024),
('I-10005','MA-301','1','Fall',2024),('I-10006','PH-301','1','Fall',2024),
('I-10001','CS-501','1','Spring',2025),
('I-10001','CS-101','1','Spring',2026),('I-10002','CS-201','1','Spring',2026),
('I-10004','MA-101','1','Spring',2026),('I-10006','PH-101','1','Spring',2026),
('I-10012','EN-101','1','Spring',2026),('I-10016','EC-101','1','Spring',2026);

INSERT INTO student (student_id, name, dept_name, total_credit) VALUES
('S0000001','Alice Grant','Comp. Sci.',6),('S0000002','Ben Torres','Comp. Sci.',6),
('S0000003','Cara Patel','Comp. Sci.',6),('S0000004','Dan Hughes','Comp. Sci.',6),
('S0000005','Eva Nguyen','Comp. Sci.',6),('S0000006','Frank Ortiz','Comp. Sci.',3),
('S0000007','Grace Kim','Comp. Sci.',6),('S0000008','Hank Reed','Comp. Sci.',6),
('S0000009','Iris Cox','Comp. Sci.',6),('S0000010','Jake Bell','Comp. Sci.',9),
('S0000011','Karen Diaz','Mathematics',4),('S0000012','Leo Ford','Mathematics',4),
('S0000013','Mia Ross','Mathematics',3),('S0000014','Nate Cook','Mathematics',4),
('S0000015','Olivia Barnes','Mathematics',4),('S0000016','Pete Morgan','Physics',4),
('S0000017','Quinn James','Physics',4),('S0000018','Rosa Watson','Physics',3),
('S0000019','Sam Brooks','Physics',4),('S0000020','Tina Kelly','Physics',3),
('S0000021','Uma Price','Chemistry',8),('S0000022','Vince Perry','Chemistry',7),
('S0000023','Wendy Sanders','Chemistry',8),('S0000024','Xander Powell','Chemistry',7),
('S0000025','Yara Long','Chemistry',8),('S0000026','Zoe Rivera','Biology',3),
('S0000027','Aaron Scott','Biology',7),('S0000028','Beth Green','Biology',3),
('S0000029','Carl Adams','Biology',6),('S0000030','Dana Nelson','Biology',6),
('S0000031','Eli Carter','English',3),('S0000032','Fay Mitchell','English',3),
('S0000033','Gary Perez','English',6),('S0000034','Holly Roberts','English',3),
('S0000035','Ivan Turner','English',3),('S0000036','Julia Phillips','History',3),
('S0000037','Karl Campbell','History',3),('S0000038','Lara Parker','History',0),
('S0000039','Mark Evans','History',3),('S0000040','Nina Edwards','History',0),
('S0000041','Oscar Collins','Economics',6),('S0000042','Paula Stewart','Economics',6),
('S0000043','Ray Sanchez','Economics',6),('S0000044','Sara Morris','Economics',0),
('S0000045','Ted Rogers','Economics',0),('S0000046','Ursula Reed','Psychology',0),
('S0000047','Victor Cook','Psychology',0),('S0000048','Wanda Bailey','Psychology',0),
('S0000049','Xena Cooper','Psychology',0),('S0000050','Yusuf Rivera','Music',0);

INSERT INTO takes (student_id, course_id, section_id, semester, year, grade) VALUES
-- CS-101 Fall 2022 prereq history
('S0000002','CS-101','1','Fall',2022,'A-'),('S0000004','CS-101','1','Fall',2022,'A-'),
('S0000005','CS-101','1','Fall',2022,'A-'), ('S0000008','CS-101','1','Fall',2022,'B-'),('S0000009','CS-101','1','Fall',2022,'B-'),
('S0000010','CS-101','1','Fall',2022,'B-'),
-- CS-101 s1 Fall 2023: 11 students (TAs S36-S40 NOT included)
('S0000015','CS-101','1','Fall',2023,'B'),('S0000018','CS-101','1','Fall',2023,'C+'),
('S0000020','CS-101','1','Fall',2023,'B-'),('S0000022','CS-101','1','Fall',2023,'B'),
('S0000024','CS-101','1','Fall',2023,'A-'),('S0000033','CS-101','1','Fall',2023,'C'),
('S0000035','CS-101','1','Fall',2023,'C+'),
-- MA-101 s1 Fall 2023: 11 students (TAs S41,S42,S46,S47,S49 NOT included)
('S0000011','MA-101','1','Fall',2023,'A'),('S0000012','MA-101','1','Fall',2023,'A-'),
('S0000014','MA-101','1','Fall',2023,'B+'),('S0000015','MA-101','1','Fall',2023,'C+'),
('S0000016','MA-101','1','Fall',2023,'A-'),('S0000017','MA-101','1','Fall',2023,'B'),
('S0000019','MA-101','1','Fall',2023,'A'),('S0000021','MA-101','1','Fall',2023,'B-'),
('S0000023','MA-101','1','Fall',2023,'C'),('S0000025','MA-101','1','Fall',2023,'B+'),
('S0000027','MA-101','1','Fall',2023,'B'),
-- CS-201 s1 Fall 2023: 7 students, all have CS-101 from Fall 2022
('S0000002','CS-201','1','Fall',2023,'B'),('S0000004','CS-201','1','Fall',2023,'A'),
('S0000005','CS-201','1','Fall',2023,'A'),('S0000009','CS-201','1','Fall',2023,'A-'),
('S0000010','CS-201','1','Fall',2023,'C+'),
-- EN-101 s1 Fall 2023: 5 students
('S0000031','EN-101','1','Fall',2023,'A'),('S0000032','EN-101','1','Fall',2023,'B+'),
('S0000034','EN-101','1','Fall',2023,'A'),('S0000003','EN-101','1','Fall',2023,'A-'),
('S0000001','EN-101','1','Fall',2023,'A-'),
-- EN-101 s2 Fall 2023: 5 different students
('S0000036','EN-101','2','Fall',2023,'A-'),('S0000037','EN-101','2','Fall',2023,'B+'),
('S0000038','EN-101','2','Fall',2023,'B-'),('S0000039','EN-101','2','Fall',2023,'C+'),
('S0000035','EN-101','2','Fall',2023,'B'),
-- BI-101 s1 Fall 2023: 5 students
('S0000026','BI-101','1','Fall',2023,'A'),('S0000027','BI-101','1','Fall',2023,'B'),
('S0000028','BI-101','1','Fall',2023,'A-'),('S0000029','BI-101','1','Fall',2023,'A-'),
('S0000030','BI-101','1','Fall',2023,'C'),
-- CH-101 s1 Fall 2023: 5 students
('S0000021','CH-101','1','Fall',2023,'A'),('S0000022','CH-101','1','Fall',2023,'B+'),
('S0000023','CH-101','1','Fall',2023,'C+'),('S0000024','CH-101','1','Fall',2023,'A-'),
('S0000025','CH-101','1','Fall',2023,'A'),
-- HI-101 s1 Fall 2023: 5 students
('S0000036','HI-101','1','Fall',2023,'A-'),('S0000041','HI-101','1','Fall',2023,'B+'),
('S0000042','HI-101','1','Fall',2023,'B'),('S0000043','HI-101','1','Fall',2023,'A-'),
('S0000039','HI-101','1','Fall',2023,'A'),
-- EC-101 s1 Fall 2023: 7 students (3 are valid MS graders for EC-101 Sp26)
('S0000043','EC-101','1','Fall',2023,'A'),('S0000030','EC-101','1','Fall',2023,'A-'),
('S0000010','EC-101','1','Fall',2023,'B-'),('S0000033','EC-101','1','Fall',2023,'A-'),
('S0000029','EC-101','1','Fall',2023,'A-'),
-- CS-101 s1 Spring 2026: 11 fresh students (NONE took CS-101 before)
('S0000044','CS-101','1','Spring',2026,NULL),('S0000045','CS-101','1','Spring',2026,NULL),
('S0000046','CS-101','1','Spring',2026,NULL),('S0000047','CS-101','1','Spring',2026,NULL),
('S0000048','CS-101','1','Spring',2026,NULL),('S0000049','CS-101','1','Spring',2026,NULL),
('S0000050','CS-101','1','Spring',2026,NULL),('S0000011','CS-101','1','Spring',2026,NULL),
('S0000012','CS-101','1','Spring',2026,NULL),('S0000014','CS-101','1','Spring',2026,NULL),
('S0000039','CS-101','1','Spring',2026,NULL),('S0000041','CS-101','1','Spring',2026,NULL),
('S0000030','CS-101','1','Spring',2026,NULL),('S0000028','CS-101','1','Spring',2026,NULL),
('S0000016','CS-101','1','Spring',2026,NULL),
-- MA-101 s1 Spring 2026: 11 fresh students (NONE took MA-101 before)
('S0000026','MA-101','1','Spring',2026,NULL),('S0000028','MA-101','1','Spring',2026,NULL),
('S0000031','MA-101','1','Spring',2026,NULL),('S0000032','MA-101','1','Spring',2026,NULL),
('S0000034','MA-101','1','Spring',2026,NULL),('S0000036','MA-101','1','Spring',2026,NULL),
('S0000038','MA-101','1','Spring',2026,NULL),('S0000040','MA-101','1','Spring',2026,NULL),
('S0000043','MA-101','1','Spring',2026,NULL),('S0000044','MA-101','1','Spring',2026,NULL),
('S0000050','MA-101','1','Spring',2026,NULL),
-- CS-201 s1 Spring 2026: 5 students who passed CS-101 Fall 2023
('S0000006','CS-201','1','Spring',2026,NULL),('S0000013','CS-201','1','Spring',2026,NULL),
('S0000018','CS-201','1','Spring',2026,NULL),('S0000020','CS-201','1','Spring',2026,NULL),
('S0000022','CS-201','1','Spring',2026,NULL),
-- EN-101 s1 Spring 2026: 5 fresh students (no EN-101 history)
('S0000045','EN-101','1','Spring',2026,NULL),('S0000046','EN-101','1','Spring',2026,NULL),
('S0000047','EN-101','1','Spring',2026,NULL),('S0000048','EN-101','1','Spring',2026,NULL),
('S0000049','EN-101','1','Spring',2026,NULL),
-- EC-101 s1 Spring 2026: 5 fresh students (no EC-101 history)
('S0000017','EC-101','1','Spring',2026,NULL),('S0000019','EC-101','1','Spring',2026,NULL),
('S0000022','EC-101','1','Spring',2026,NULL),('S0000023','EC-101','1','Spring',2026,NULL),
('S0000024','EC-101','1','Spring',2026,NULL);

INSERT INTO advising (student_id, instructor_id) VALUES
('S0000001','I-10001'),('S0000002','I-10001'),('S0000003','I-10002'),('S0000004','I-10002'),
('S0000005','I-10003'),('S0000006','I-10003'),('S0000007','I-10001'),('S0000008','I-10002'),
('S0000009','I-10003'),('S0000010','I-10001'),('S0000011','I-10004'),('S0000012','I-10004'),
('S0000013','I-10005'),('S0000014','I-10005'),('S0000015','I-10004'),('S0000016','I-10006'),
('S0000017','I-10006'),('S0000018','I-10007'),('S0000019','I-10007'),('S0000020','I-10006'),
('S0000021','I-10008'),('S0000022','I-10008'),('S0000023','I-10009'),('S0000024','I-10009'),
('S0000025','I-10008'),('S0000026','I-10010'),('S0000027','I-10010'),('S0000028','I-10011'),
('S0000029','I-10011'),('S0000030','I-10010'),('S0000031','I-10012'),('S0000032','I-10013'),
('S0000033','I-10012'),('S0000034','I-10013'),('S0000035','I-10012'),('S0000036','I-10014'),
('S0000037','I-10015'),('S0000038','I-10014'),('S0000039','I-10015'),('S0000040','I-10014'),
('S0000041','I-10016'),('S0000042','I-10017'),('S0000043','I-10016'),('S0000044','I-10017'),
('S0000045','I-10016'),('S0000046','I-10018'),('S0000047','I-10019'),('S0000048','I-10018'),
('S0000049','I-10019'),('S0000050','I-10020');

INSERT INTO prereq (course_id, prereq_id) VALUES
('CS-201','CS-101'),('CS-301','CS-201'),('CS-401','CS-301'),('CS-501','CS-301'),
('CS-601','CS-401'),('MA-201','MA-101'),('MA-301','MA-101'),('MA-401','MA-201'),
('PH-201','PH-101'),('PH-301','PH-201'),('CH-201','CH-101'),('BI-201','BI-101'),
('EN-201','EN-101'),('HI-201','HI-101'),('EC-201','EC-101');

INSERT INTO undergraduate (student_id, class_year, major_dept_name) VALUES
('S0000001',1,'Comp. Sci.'),('S0000002',1,'Comp. Sci.'),('S0000003',1,'Comp. Sci.'),
('S0000004',1,'Comp. Sci.'),('S0000005',1,'Comp. Sci.'),('S0000006',1,'Comp. Sci.'),
('S0000007',1,'Comp. Sci.'),('S0000008',1,'Comp. Sci.'),('S0000009',1,'Comp. Sci.'),
('S0000010',1,'Comp. Sci.'),('S0000011',1,'Mathematics'),('S0000012',1,'Mathematics'),
('S0000013',1,'Mathematics'),('S0000014',1,'Mathematics'),('S0000015',1,'Mathematics'),
('S0000016',1,'Physics'),('S0000017',1,'Physics'),('S0000018',1,'Physics'),
('S0000019',1,'Physics'),('S0000020',1,'Physics');

INSERT INTO master (student_id) VALUES
('S0000021'),('S0000022'),('S0000023'),('S0000024'),('S0000025'),
('S0000026'),('S0000027'),('S0000028'),('S0000029'),('S0000030'),
('S0000031'),('S0000032'),('S0000033'),('S0000034'),('S0000035');

INSERT INTO phd (student_id, dissertation_title, qualifier_passed) VALUES
('S0000036','Memory and Identity in Postwar Narratives',1),
('S0000037','Economic Cycles in Colonial America',1),
('S0000038','Oral Traditions and Historiography',0),
('S0000039','Revisiting the New Deal Era',1),
('S0000040','Migration Patterns in 19th-Century US',0),
('S0000041','Game Theory in Labor Markets',1),
('S0000042','Behavioral Economics and Consumer Bias',1),
('S0000043','Market Failures in Developing Economies',1),
('S0000044','Income Inequality and Policy Outcomes',0),
('S0000045','Supply Chain Optimization Models',1),
('S0000046','Cognitive Bias in Decision Making',1),
('S0000047','Social Media and Adolescent Mental Health',0),
('S0000048','Stress Responses in Chronic Illness Patients',1),
('S0000049','Neurological Correlates of Anxiety Disorders',1),
('S0000050','Harmonic Structures in Jazz Improvisation',0);

INSERT INTO teacher_assistant (student_id, course_id, section_id, semester, year) VALUES
('S0000036','CS-101','1','Fall',2023),('S0000037','CS-101','1','Fall',2023),
('S0000038','CS-101','1','Fall',2023),('S0000039','CS-101','1','Fall',2023),
('S0000040','CS-101','1','Fall',2023),
('S0000041','MA-101','1','Fall',2023),('S0000042','MA-101','1','Fall',2023),
('S0000046','MA-101','1','Fall',2023),('S0000047','MA-101','1','Fall',2023),
('S0000043','CS-101','1','Spring',2026),('S0000045','MA-101','1','Spring',2026),
('S0000049','MA-101','1','Fall',2023);

-- Graders: all UG/MS, max 1 section each, A-/A/A+ in course, section 5-10, not enrolled in graded section
-- All 10 graders for Spring 2026 sections — they took the course in Fall 2023, not Spring 2026
INSERT INTO grader (student_id, course_id, section_id, semester, year) VALUES
('S0000004','CS-201','1','Spring',2026),  -- UG, A  CS-201 F23; not in CS-201 Sp26 ✓
('S0000005','CS-201','1','Spring',2026),  -- UG, A  CS-201 F23; not in CS-201 Sp26 ✓
('S0000009','CS-201','1','Spring',2026),  -- UG, A- CS-201 F23; not in CS-201 Sp26 ✓
('S0000031','EN-101','1','Spring',2026),  -- MS, A  EN-101 F23; not in EN-101 Sp26 ✓
('S0000034','EN-101','1','Spring',2026),  -- MS, A  EN-101 F23; not in EN-101 Sp26 ✓
('S0000003','EN-101','1','Spring',2026),  -- UG, A- EN-101 F23; not in EN-101 Sp26 ✓
('S0000030','EC-101','1','Spring',2026),  -- MS, A- EC-101 F23; not in EC-101 Sp26 ✓
('S0000033','EC-101','1','Spring',2026),  -- MS, A- EC-101 F23; not in EC-101 Sp26 ✓
('S0000029','EC-101','1','Spring',2026),  -- MS, A- EC-101 F23; not in EC-101 Sp26 ✓
('S0000001','EN-101','1','Spring',2026);   -- UG, A- EN-101 F23; not in EN-101 Sp26 ✓


-- discussion (15 rows — all posters enrolled in their section)
INSERT INTO discussion (discussion_id, reply_id, student_id, course_id, section_id, semester, year, content) VALUES
('D0000001','','S0000001','CS-101','1','Fall',2023,'Can someone explain recursion with a practical example?'),
('D0000002','','S0000003','CS-101','1','Fall',2023,'What is the difference between a stack and a queue?'),
('D0000003','','S0000004','CS-201','1','Fall',2023,'How does a red-black tree maintain balance?'),
('D0000004','','S0000009','CS-201','1','Fall',2023,'What is the time complexity of heapsort?'),
('D0000005','','S0000011','MA-101','1','Fall',2023,'How do you set up u-substitution for integrals?'),
('D0000006','D0000005','S0000014','MA-101','1','Fall',2023,'Substitute u = g(x), replace dx with du / g-prime of x.'),
('D0000007','','S0000021','CH-101','1','Fall',2023,'What is the difference between ionic and covalent bonds?'),
('D0000008','','S0000026','BI-101','1','Fall',2023,'How does meiosis differ from mitosis step by step?'),
('D0000009','D0000008','S0000028','BI-101','1','Fall',2023,'Meiosis produces 4 haploid cells; mitosis produces 2 diploid.'),
('D0000010','','S0000031','EN-101','1','Fall',2023,'What citation style is preferred for literary analysis?'),
('D0000011','','S0000034','EN-101','1','Fall',2023,'Any tips for structuring a compare-and-contrast essay?'),
('D0000012','','S0000036','EN-101','2','Fall',2023,'How do primary sources differ from secondary sources?'),
('D0000013','','S0000041','EC-101','1','Fall',2023,'How does price elasticity affect firm revenue?'),
('D0000014','','S0000030','EC-101','1','Fall',2023,'What distinguishes microeconomics from macroeconomics?'),
('D0000015','','S0000006','CS-201','1','Spring',2026,'Any advice for the first data structures assignment?');

-- course_evaluation (15 rows — all for past sections, all posters enrolled)
INSERT INTO course_evaluation (student_id, course_id, section_id, semester, year, rating, comment) VALUES
('S0000001','CS-101','1','Fall',2023,5,'Excellent course, very well structured.'),
('S0000004','CS-201','1','Fall',2023,4,'Challenging but rewarding.'),
('S0000009','CS-201','1','Fall',2023,3,'Good material but pacing was inconsistent.'),
('S0000011','MA-101','1','Fall',2023,4,'Clear explanations and well-chosen examples.'),
('S0000014','MA-101','1','Fall',2023,5,'Best math course I have taken.'),
('S0000021','CH-101','1','Fall',2023,4,'Good balance between theory and lab work.'),
('S0000022','CH-101','1','Fall',2023,3,'Labs were interesting but exams were hard.'),
('S0000026','BI-101','1','Fall',2023,4,'Interesting content, slides hard to follow.'),
('S0000028','BI-101','1','Fall',2023,5,'Fantastic professor, made biology intuitive.'),
('S0000031','EN-101','1','Fall',2023,5,'Transformed my academic writing.'),
('S0000034','EN-101','1','Fall',2023,4,'Solid course with great essay feedback.'),
('S0000036','EN-101','2','Fall',2023,3,'Decent section but fewer discussions.'),
('S0000041','EC-101','1','Fall',2023,3,'Useful but needs more real-world cases.'),
('S0000030','EC-101','1','Fall',2023,4,'Good introduction to microeconomic theory.'),
('S0000039','HI-101','1','Fall',2023,4,'Great historical context and lively discussion.');

-- Populate emails and create accounts
UPDATE student SET email = CONCAT(
    LOWER(SUBSTRING_INDEX(name,' ',1)),'_',
    LOWER(SUBSTRING_INDEX(name,' ',-1)),'@student.uml.edu');

UPDATE instructor SET email = CONCAT(
    LOWER(SUBSTRING_INDEX(name,' ',1)),'_',
    LOWER(SUBSTRING_INDEX(name,' ',-1)),'@uml.edu');

INSERT INTO account (id, email, password, type)
SELECT student_id, email, student_id, 'student' FROM student;

INSERT INTO account (id, email, password, type)
SELECT instructor_id, email, instructor_id, 'instructor' FROM instructor;

INSERT INTO registration_deadline (semester, year, add_deadline, drop_deadline)
VALUES ('Spring', 2026, '2026-12-31', '2026-12-31');