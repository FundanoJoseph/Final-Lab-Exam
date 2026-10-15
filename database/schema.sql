-- =============================================================
--  Student Information System – MySQL schema
-- -------------------------------------------------------------
--  Setup (InfinityFree / phpMyAdmin / any MySQL host):
--    1. Create a database (e.g. via phpMyAdmin or the control panel).
--    2. Import this file (phpMyAdmin > Import > Go).
--    3. Update config/config.php with your database credentials.
--
--  Default admin access:
--    Username: admin
--    Password: admin123
-- =============================================================

SET FOREIGN_KEY_CHECKS = 0;

-- -------------------------------------------------------------
--  users – login accounts
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `student_academics`;
DROP TABLE IF EXISTS `students`;
DROP TABLE IF EXISTS `courses`;
DROP TABLE IF EXISTS `departments`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
    `id`            INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `username`      VARCHAR(50)      NOT NULL,
    `password_hash` VARCHAR(255)     NOT NULL,
    `full_name`     VARCHAR(100)     NOT NULL,
    `role`          VARCHAR(20)      NOT NULL DEFAULT 'admin',
    `last_login_at` DATETIME         NULL,
    `created_at`    DATETIME         NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_users_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------------
--  departments – Manage Department
-- -------------------------------------------------------------
CREATE TABLE `departments` (
    `id`          INT UNSIGNED   NOT NULL AUTO_INCREMENT,
    `code`        VARCHAR(20)    NOT NULL,
    `name`        VARCHAR(100)   NOT NULL,
    `description` VARCHAR(255)   NULL,
    `created_at`  DATETIME       NOT NULL,
    `updated_at`  DATETIME       NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_departments_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------------
--  courses – Course Management (belongs to a department)
-- -------------------------------------------------------------
CREATE TABLE `courses` (
    `id`            INT UNSIGNED   NOT NULL AUTO_INCREMENT,
    `department_id` INT UNSIGNED   NOT NULL,
    `code`          VARCHAR(20)    NOT NULL,
    `name`          VARCHAR(120)   NOT NULL,
    `description`   VARCHAR(255)   NULL,
    `created_at`    DATETIME       NOT NULL,
    `updated_at`    DATETIME       NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_courses_code` (`code`),
    KEY `idx_courses_department` (`department_id`),
    CONSTRAINT `fk_courses_department`
        FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------------
--  students – Student Information and Records
-- -------------------------------------------------------------
CREATE TABLE `students` (
    `id`                 INT UNSIGNED   NOT NULL AUTO_INCREMENT,
    `student_no`         VARCHAR(30)    NOT NULL,
    `first_name`         VARCHAR(50)    NOT NULL,
    `middle_name`        VARCHAR(50)    NULL,
    `last_name`          VARCHAR(50)    NOT NULL,
    `gender`             VARCHAR(10)    NOT NULL DEFAULT 'Male',
    `civil_status`       VARCHAR(20)    NULL,
    `date_of_birth`      DATE           NULL,
    `contact_no`         VARCHAR(20)    NULL,
    `email`              VARCHAR(100)   NULL,
    `present_address`    VARCHAR(255)   NULL,
    `permanent_address`  VARCHAR(255)   NULL,
    `course_id`          INT UNSIGNED   NULL,
    `status`             VARCHAR(20)    NOT NULL DEFAULT 'Active',
    `created_at`         DATETIME       NOT NULL,
    `updated_at`         DATETIME       NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_students_no` (`student_no`),
    KEY `idx_students_course` (`course_id`),
    CONSTRAINT `fk_students_course`
        FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------------
--  student_academics – academic history (one row per term)
-- -------------------------------------------------------------
CREATE TABLE `student_academics` (
    `id`          INT UNSIGNED   NOT NULL AUTO_INCREMENT,
    `student_id`  INT UNSIGNED   NOT NULL,
    `course_id`   INT UNSIGNED   NULL,
    `semester`    VARCHAR(20)    NOT NULL,
    `school_year` VARCHAR(9)     NOT NULL,
    `year_level`  VARCHAR(10)    NOT NULL,
    `beg_status`  VARCHAR(20)    NOT NULL DEFAULT 'Enrolled',
    `end_status`  VARCHAR(20)    NOT NULL DEFAULT 'In Progress',
    `created_at`  DATETIME       NOT NULL,
    `updated_at`  DATETIME       NOT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_academics_student` (`student_id`),
    KEY `idx_academics_course` (`course_id`),
    CONSTRAINT `fk_academics_student`
        FOREIGN KEY (`student_id`) REFERENCES `students` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_academics_course`
        FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================================
--  Seed data
-- =============================================================

-- Default admin account (password: admin123, bcrypt hash)
INSERT INTO `users` (`username`, `password_hash`, `full_name`, `role`, `created_at`) VALUES
('admin', '$2y$10$l1znMxoQOq08zAC4cSRaLuJd8eTdSRUvLVcetX4DpfEAUuJT0T7EG', 'System Administrator', 'admin', NOW());

INSERT INTO `departments` (`id`, `code`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'CCS', 'College of Computer Studies', 'Programs for software, data and computing.', NOW(), NOW()),
(2, 'CIT', 'College of Information Technology', 'Programs for IT infrastructure and applications.', NOW(), NOW()),
(3, 'CEA', 'College of Engineering and Architecture', 'Engineering and architecture programs.', NOW(), NOW()),
(4, 'CAS', 'College of Arts and Sciences', 'General education and arts programs.', NOW(), NOW());

INSERT INTO `courses` (`id`, `department_id`, `code`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 2, 'BSIT',  'Bachelor of Science in Information Technology', 'Four-year IT degree program.', NOW(), NOW()),
(2, 1, 'BSCS',  'Bachelor of Science in Computer Science', 'Four-year computer science degree program.', NOW(), NOW()),
(3, 2, 'ACT',   'Associate in Computer Technology', 'Two-year computer technology program.', NOW(), NOW()),
(4, 3, 'BSCpE', 'Bachelor of Science in Computer Engineering', 'Four-year computer engineering program.', NOW(), NOW()),
(5, 4, 'BEEd',  'Bachelor of Elementary Education', 'Four-year elementary education program.', NOW(), NOW());

INSERT INTO `students` (`id`, `student_no`, `first_name`, `middle_name`, `last_name`, `gender`, `civil_status`, `date_of_birth`, `contact_no`, `email`, `present_address`, `permanent_address`, `course_id`, `status`, `created_at`, `updated_at`) VALUES
(1, '221040603807', 'Mark',      'D.',      'Kasper',     'Male',   'Single', '2007-01-20', '09171234567', 'mark.kasper@email.com',  'This is my sample present address.',  'This is my sample permanent address.',  1, 'Active',     NOW(), NOW()),
(2, '221040603808', 'Juan',      'Miguel',  'Reyes',      'Male',   'Single', '2006-05-14', '09182345678', 'juan.reyes@email.com',   '123 Rizal Ave., Manila',              '123 Rizal Ave., Manila',              1, 'Active',     NOW(), NOW()),
(3, '221040603809', 'Maria',     'Clara',   'Santos',     'Female', 'Single', '2006-09-02', '09193456789', 'maria.santos@email.com', '45 Bonifacio St., Quezon City',       '45 Bonifacio St., Quezon City',       2, 'Active',     NOW(), NOW()),
(4, '221040603810', 'Jose',      'Rizal',   'Dela Cruz',  'Male',   'Single', '2005-12-11', '09204567890', 'jose.delacruz@email.com','88 Mabini Rd., Cebu City',            '88 Mabini Rd., Cebu City',            3, 'Active',     NOW(), NOW()),
(5, '221040603811', 'Angelica',  'L.',      'Mendoza',    'Female', 'Single', '2007-03-25', '09215678901', 'angelica.m@email.com',   '7 Sampaguita Ave., Davao City',       '7 Sampaguita Ave., Davao City',       2, 'Inactive',   NOW(), NOW()),
(6, '221040603812', 'Paolo',     'S.',      'Villanueva', 'Male',   'Single', '2006-07-30', '09226789012', 'paolo.v@email.com',      '15 Narra St., Makati City',           '15 Narra St., Makati City',           4, 'Graduated',  NOW(), NOW());

INSERT INTO `student_academics` (`id`, `student_id`, `course_id`, `semester`, `school_year`, `year_level`, `beg_status`, `end_status`, `created_at`, `updated_at`) VALUES
-- Student 1 (matches the sample academic history in the exam guide)
(1,  1, 1, '1st Semester', '2023-2024', '1st Year', 'Enrolled', 'Passed',      NOW(), NOW()),
(2,  1, 1, '2nd Semester', '2023-2024', '1st Year', 'Enrolled', 'Passed',      NOW(), NOW()),
(3,  1, 1, '1st Semester', '2024-2025', '2nd Year', 'Enrolled', 'Passed',      NOW(), NOW()),
(4,  1, 1, '2nd Semester', '2024-2025', '2nd Year', 'Enrolled', 'Passed',      NOW(), NOW()),
(5,  1, 1, 'Summer',       '2024-2025', '2nd Year', 'Enrolled', 'Incomplete',  NOW(), NOW()),
(6,  1, 1, '1st Semester', '2025-2026', '3rd Year', 'Enrolled', 'In Progress', NOW(), NOW()),
-- Other students
(7,  2, 1, '1st Semester', '2023-2024', '1st Year', 'Enrolled', 'Passed',      NOW(), NOW()),
(8,  2, 1, '2nd Semester', '2023-2024', '1st Year', 'Enrolled', 'Passed',      NOW(), NOW()),
(9,  2, 1, '1st Semester', '2024-2025', '2nd Year', 'Enrolled', 'In Progress', NOW(), NOW()),
(10, 3, 2, '1st Semester', '2024-2025', '1st Year', 'Enrolled', 'Passed',      NOW(), NOW()),
(11, 3, 2, '2nd Semester', '2024-2025', '1st Year', 'Enrolled', 'In Progress', NOW(), NOW()),
(12, 4, 3, '1st Semester', '2022-2023', '1st Year', 'Enrolled', 'Passed',      NOW(), NOW()),
(13, 4, 3, '2nd Semester', '2022-2023', '1st Year', 'Enrolled', 'Passed',      NOW(), NOW()),
(14, 5, 2, '1st Semester', '2024-2025', '1st Year', 'On Leave','Incomplete',  NOW(), NOW()),
(15, 6, 4, '1st Semester', '2021-2022', '1st Year', 'Enrolled', 'Passed',      NOW(), NOW());
