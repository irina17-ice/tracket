CREATE DATABASE IF NOT EXISTS tracket_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tracket_db;

-- 1. Table des utilisateurs
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE, -- PROM-001, TCH-001, 20T1234
    password VARCHAR(255) NOT NULL,
    role ENUM('promoter', 'teacher', 'student') NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. Table des domaines
CREATE TABLE domains (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    created_by INT,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 3. Table des modules
CREATE TABLE modules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    domain_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    sort_order INT DEFAULT 0,
    FOREIGN KEY (domain_id) REFERENCES domains(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 4. Table des cours
CREATE TABLE courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    module_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    sort_order INT DEFAULT 0,
    FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 5. Table des leçons
CREATE TABLE lessons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    content_type ENUM('pdf', 'video', 'youtube') NOT NULL,
    content_path VARCHAR(255) NOT NULL, -- URL ou chemin du fichier
    sort_order INT DEFAULT 0,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 6. Table des évaluations (Une par module pour valider la progression)
CREATE TABLE evaluations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    module_id INT NOT NULL UNIQUE,
    title VARCHAR(150) NOT NULL,
    passing_score INT DEFAULT 70, -- Score requis en %
    FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 7. Table des questions d'évaluation
CREATE TABLE evaluation_questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    evaluation_id INT NOT NULL,
    question TEXT NOT NULL,
    option_a VARCHAR(255) NOT NULL,
    option_b VARCHAR(255) NOT NULL,
    option_c VARCHAR(255) NOT NULL,
    option_d VARCHAR(255) NOT NULL,
    correct_option ENUM('A', 'B', 'C', 'D') NOT NULL,
    FOREIGN KEY (evaluation_id) REFERENCES evaluations(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 8. Table des tentatives / résultats d'évaluations
CREATE TABLE evaluation_results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    evaluation_id INT NOT NULL,
    score INT NOT NULL, -- Pourcentage obtenu
    passed TINYINT(1) NOT NULL,
    attempt_number INT NOT NULL,
    taken_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (evaluation_id) REFERENCES evaluations(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 9. Table de suivi de la progression dans le module
CREATE TABLE module_progress (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    module_id INT NOT NULL,
    progress_percent INT DEFAULT 0,
    is_completed TINYINT(1) DEFAULT 0,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE,
    UNIQUE KEY unique_student_module (student_id, module_id)
) ENGINE=InnoDB;

-- 10. Table des certificats délivrés
CREATE TABLE certificates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    module_id INT NOT NULL,
    certificate_code VARCHAR(50) NOT NULL UNIQUE,
    file_path VARCHAR(255) NOT NULL,
    issued_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Insertion des comptes de test (password123 haché)
INSERT INTO users (username, password, role, full_name, email) VALUES
('PROM-001', '$2y$10$O0gZ4pG0wR2e9KzB2Z/iO.3B0p83pY6uXg9fW9Wn99gH8U8Q8Ym1.', 'promoter', 'Directeur Général', 'promoter@tracket.com'),
('TCH-001', '$2y$10$O0gZ4pG0wR2e9KzB2Z/iO.3B0p83pY6uXg9fW9Wn99gH8U8Q8Ym1.', 'teacher', 'Prof. Jean Dupont', 'teacher@tracket.com'),
('20T1234', '$2y$10$O0gZ4pG0wR2e9KzB2Z/iO.3B0p83pY6uXg9fW9Wn99gH8U8Q8Ym1.', 'student', 'Alexandre Traoré', 'student@tracket.com');