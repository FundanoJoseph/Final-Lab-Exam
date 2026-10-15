-- =============================================================
--  Student Information System – SQLite schema
-- -------------------------------------------------------------
--  Used for local demos/tests when no MySQL server is available.
--  The production schema for InfinityFree is schema.sql (MySQL).
--
--  Quick setup:
--      sqlite3 database/demo.sqlite < database/schema-sqlite.sql
-- =============================================================

PRAGMA foreign_keys = ON;

DROP TABLE IF EXISTS student_academics;
DROP TABLE IF EXISTS students;
DROP TABLE IF EXISTS courses;
DROP TABLE IF EXISTS departments;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id            INTEGER PRIMARY KEY AUTOINCREMENT,
    username      TEXT    NOT NULL UNIQUE,
    password_hash TEXT    NOT NULL,
    full_name     TEXT    NOT NULL,
    role          TEXT    NOT NULL DEFAULT 'admin',
    last_login_at TEXT    NULL,
    created_at    TEXT    NOT NULL
);

CREATE TABLE departments (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    code        TEXT    NOT NULL UNIQUE,
    name        TEXT    NOT NULL,
    description TEXT    NULL,
    created_at  TEXT    NOT NULL,
    updated_at  TEXT    NOT NULL
);

CREATE TABLE courses (
    id            INTEGER PRIMARY KEY AUTOINCREMENT,
    department_id INTEGER NOT NULL,
    code          TEXT    NOT NULL UNIQUE,
    name          TEXT    NOT NULL,
    description   TEXT    NULL,
    created_at    TEXT    NOT NULL,
    updated_at    TEXT    NOT NULL,
    FOREIGN KEY (department_id) REFERENCES departments (id)
        ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE students (
    id                INTEGER PRIMARY KEY AUTOINCREMENT,
    student_no        TEXT    NOT NULL UNIQUE,
    first_name        TEXT    NOT NULL,
    middle_name       TEXT    NULL,
    last_name         TEXT    NOT NULL,
    gender            TEXT    NOT NULL DEFAULT 'Male',
    civil_status      TEXT    NULL,
    date_of_birth     TEXT    NULL,
    contact_no        TEXT    NULL,
    email             TEXT    NULL,
    present_address   TEXT    NULL,
    permanent_address TEXT    NULL,
    course_id         INTEGER NULL,
    status            TEXT    NOT NULL DEFAULT 'Active',
    created_at        TEXT    NOT NULL,
    updated_at        TEXT    NOT NULL,
    FOREIGN KEY (course_id) REFERENCES courses (id)
        ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE student_academics (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    student_id  INTEGER NOT NULL,
    course_id   INTEGER NULL,
    semester    TEXT    NOT NULL,
    school_year TEXT    NOT NULL,
    year_level  TEXT    NOT NULL,
    beg_status  TEXT    NOT NULL DEFAULT 'Enrolled',
    end_status  TEXT    NOT NULL DEFAULT 'In Progress',
    created_at  TEXT    NOT NULL,
    updated_at  TEXT    NOT NULL,
    FOREIGN KEY (student_id) REFERENCES students (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses (id)
        ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE INDEX idx_courses_department  ON courses (department_id);
CREATE INDEX idx_students_course     ON students (course_id);
CREATE INDEX idx_academics_student   ON student_academics (student_id);
CREATE INDEX idx_academics_course    ON student_academics (course_id);

-- =============================================================
--  Seed data  (admin / admin123)
-- =============================================================
INSERT INTO users (username, password_hash, full_name, role, created_at) VALUES
('admin', '$2y$10$l1znMxoQOq08zAC4cSRaLuJd8eTdSRUvLVcetX4DpfEAUuJT0T7EG', 'System Administrator', 'admin', datetime('now'));

INSERT INTO departments (id, code, name, description, created_at, updated_at) VALUES
(1, 'CCS', 'College of Computer Studies', 'Programs for software, data and computing.', datetime('now'), datetime('now')),
(2, 'CIT', 'College of Information Technology', 'Programs for IT infrastructure and applications.', datetime('now'), datetime('now')),
(3, 'CEA', 'College of Engineering and Architecture', 'Engineering and architecture programs.', datetime('now'), datetime('now')),
(4, 'CAS', 'College of Arts and Sciences', 'General education and arts programs.', datetime('now'), datetime('now'));

INSERT INTO courses (id, department_id, code, name, description, created_at, updated_at) VALUES
(1, 2, 'BSIT',  'Bachelor of Science in Information Technology', 'Four-year IT degree program.', datetime('now'), datetime('now')),
(2, 1, 'BSCS',  'Bachelor of Science in Computer Science', 'Four-year computer science degree program.', datetime('now'), datetime('now')),
(3, 2, 'ACT',   'Associate in Computer Technology', 'Two-year computer technology program.', datetime('now'), datetime('now')),
(4, 3, 'BSCpE', 'Bachelor of Science in Computer Engineering', 'Four-year computer engineering program.', datetime('now'), datetime('now')),
(5, 4, 'BEEd',  'Bachelor of Elementary Education', 'Four-year elementary education program.', datetime('now'), datetime('now'));

INSERT INTO students (id, student_no, first_name, middle_name, last_name, gender, civil_status, date_of_birth, contact_no, email, present_address, permanent_address, course_id, status, created_at, updated_at) VALUES
(1, '221040603807', 'Mark',      'D.',      'Kasper',     'Male',   'Single', '2007-01-20', '09171234567', 'mark.kasper@email.com',  'This is my sample present address.',  'This is my sample permanent address.',  1, 'Active',    datetime('now'), datetime('now')),
(2, '221040603808', 'Juan',      'Miguel',  'Reyes',      'Male',   'Single', '2006-05-14', '09182345678', 'juan.reyes@email.com',   '123 Rizal Ave., Manila',              '123 Rizal Ave., Manila',              1, 'Active',    datetime('now'), datetime('now')),
(3, '221040603809', 'Maria',     'Clara',   'Santos',     'Female', 'Single', '2006-09-02', '09193456789', 'maria.santos@email.com', '45 Bonifacio St., Quezon City',       '45 Bonifacio St., Quezon City',       2, 'Active',    datetime('now'), datetime('now')),
(4, '221040603810', 'Jose',      'Rizal',   'Dela Cruz',  'Male',   'Single', '2005-12-11', '09204567890', 'jose.delacruz@email.com','88 Mabini Rd., Cebu City',            '88 Mabini Rd., Cebu City',            3, 'Active',    datetime('now'), datetime('now')),
(5, '221040603811', 'Angelica',  'L.',      'Mendoza',    'Female', 'Single', '2007-03-25', '09215678901', 'angelica.m@email.com',   '7 Sampaguita Ave., Davao City',       '7 Sampaguita Ave., Davao City',       2, 'Inactive',  datetime('now'), datetime('now')),
(6, '221040603812', 'Paolo',     'S.',      'Villanueva', 'Male',   'Single', '2006-07-30', '09226789012', 'paolo.v@email.com',      '15 Narra St., Makati City',           '15 Narra St., Makati City',           4, 'Graduated', datetime('now'), datetime('now'));

INSERT INTO student_academics (id, student_id, course_id, semester, school_year, year_level, beg_status, end_status, created_at, updated_at) VALUES
(1,  1, 1, '1st Semester', '2023-2024', '1st Year', 'Enrolled', 'Passed',      datetime('now'), datetime('now')),
(2,  1, 1, '2nd Semester', '2023-2024', '1st Year', 'Enrolled', 'Passed',      datetime('now'), datetime('now')),
(3,  1, 1, '1st Semester', '2024-2025', '2nd Year', 'Enrolled', 'Passed',      datetime('now'), datetime('now')),
(4,  1, 1, '2nd Semester', '2024-2025', '2nd Year', 'Enrolled', 'Passed',      datetime('now'), datetime('now')),
(5,  1, 1, 'Summer',       '2024-2025', '2nd Year', 'Enrolled', 'Incomplete',  datetime('now'), datetime('now')),
(6,  1, 1, '1st Semester', '2025-2026', '3rd Year', 'Enrolled', 'In Progress', datetime('now'), datetime('now')),
(7,  2, 1, '1st Semester', '2023-2024', '1st Year', 'Enrolled', 'Passed',      datetime('now'), datetime('now')),
(8,  2, 1, '2nd Semester', '2023-2024', '1st Year', 'Enrolled', 'Passed',      datetime('now'), datetime('now')),
(9,  2, 1, '1st Semester', '2024-2025', '2nd Year', 'Enrolled', 'In Progress', datetime('now'), datetime('now')),
(10, 3, 2, '1st Semester', '2024-2025', '1st Year', 'Enrolled', 'Passed',      datetime('now'), datetime('now')),
(11, 3, 2, '2nd Semester', '2024-2025', '1st Year', 'Enrolled', 'In Progress', datetime('now'), datetime('now')),
(12, 4, 3, '1st Semester', '2022-2023', '1st Year', 'Enrolled', 'Passed',      datetime('now'), datetime('now')),
(13, 4, 3, '2nd Semester', '2022-2023', '1st Year', 'Enrolled', 'Passed',      datetime('now'), datetime('now')),
(14, 5, 2, '1st Semester', '2024-2025', '1st Year', 'On Leave','Incomplete',  datetime('now'), datetime('now')),
(15, 6, 4, '1st Semester', '2021-2022', '1st Year', 'Enrolled', 'Passed',      datetime('now'), datetime('now'));
