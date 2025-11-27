-- Create database
CREATE DATABASE IF NOT EXISTS todoapp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE todoapp;

-- Create todos table
CREATE TABLE IF NOT EXISTS todos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    completed BOOLEAN DEFAULT FALSE,
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    due_date DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_completed (completed),
    INDEX idx_priority (priority),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample data
INSERT INTO todos (title, description, priority, completed, due_date) VALUES
('Setup Docker environment', 'Configure Docker containers for the application', 'high', true, '2025-11-20 18:00:00'),
('Implement CI/CD pipeline', 'Create GitHub Actions workflows for automated testing and deployment', 'high', false, '2025-11-25 23:59:59'),
('Add Prometheus monitoring', 'Setup Prometheus and Grafana for application monitoring', 'medium', false, '2025-11-28 17:00:00'),
('Write documentation', 'Create comprehensive README and project documentation', 'medium', false, '2025-11-30 12:00:00'),
('Deploy to Kubernetes', 'Deploy the application to Kubernetes cluster', 'low', false, '2025-12-05 15:00:00');
