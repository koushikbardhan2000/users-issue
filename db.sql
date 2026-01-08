-- 1. Create the database
CREATE DATABASE IF NOT EXISTS issue;
USE issue;

-- 2. Create the users table
CREATE TABLE users (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    phone VARCHAR(15),
    password VARCHAR(255) NOT NULL,
    role ENUM('MANAGER','SUPPORT_ENGINEER') NOT NULL,
    status ENUM('ACTIVE','INACTIVE') DEFAULT 'ACTIVE',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Create the calls table
CREATE TABLE calls (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    problem_type VARCHAR(50) NOT NULL,
    description TEXT NOT NULL,
    status ENUM('PENDING','ONGOING','CLOSED') DEFAULT 'PENDING',
    resolution_status ENUM('RESOLVED','NOT_RESOLVED') NULL,
    user_name VARCHAR(100),
    user_email VARCHAR(150),
    user_phone VARCHAR(15),
    manager_id BIGINT NULL,
    engineer_id BIGINT NULL,
    final_remark TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL,
    closed_at TIMESTAMP NULL,
    FOREIGN KEY (manager_id) REFERENCES users(id),
    FOREIGN KEY (engineer_id) REFERENCES users(id)
);

-- 4. Insert demo users (1 manager, 3 support engineers)
INSERT INTO users (name, email, phone, password, role, status) VALUES
  ('Alice Johnson',   'alice.johnson@example.com',   '123-456-7890', 'managerPass123', 'MANAGER',          'ACTIVE'),
  ('Bob Smith',       'bob.smith@example.com',       '123-456-7891', 'password123',    'SUPPORT_ENGINEER', 'ACTIVE'),
  ('Charlie Davis',   'charlie.davis@example.com',   '123-456-7892', 'password123',    'SUPPORT_ENGINEER', 'ACTIVE'),
  ('Dana Lee',        'dana.lee@example.com',        '123-456-7893', 'password123',    'SUPPORT_ENGINEER', 'ACTIVE');

-- 5. Insert demo calls (3 pending, 2 ongoing, 2 closed)
INSERT INTO calls (problem_type, description, status, resolution_status, user_name, user_email, user_phone, manager_id, engineer_id, final_remark, created_at, updated_at, closed_at) VALUES
  ('Network',  'Cannot connect to VPN',               'PENDING', NULL,       'Eve Adams',    'eve.adams@example.com',    '555-000-1111', NULL, NULL, NULL,                         '2025-01-01 08:00:00', '2025-01-01 08:00:00', NULL),
  ('Hardware', 'Laptop battery not charging',         'PENDING', NULL,       'Frank Wright', 'frank.wright@example.com','555-000-2222', NULL, NULL, NULL,                         '2025-01-02 09:30:00', '2025-01-02 09:30:00', NULL),
  ('Software', 'Unable to install software',          'PENDING', NULL,       'Grace Hopper', 'grace.hopper@example.com','555-000-3333', NULL, NULL, NULL,                         '2025-01-03 10:45:00', '2025-01-03 10:45:00', NULL),
  ('Network',  'WiFi disconnects frequently',         'ONGOING', 'NOT_RESOLVED','Hank Miller',  'hank.miller@example.com', '555-000-4444', 1,    2,    NULL,                         '2025-01-01 09:00:00', '2025-01-02 11:00:00', NULL),
  ('Software', 'Application crashes on launch',       'ONGOING', 'NOT_RESOLVED','Ivy Nelson',   'ivy.nelson@example.com',  '555-000-5555', 1,    3,    NULL,                         '2025-01-02 12:15:00', '2025-01-03 14:20:00', NULL),
  ('Hardware', 'Server overheating',                  'CLOSED',  'RESOLVED',    'Jackie Chan',  'jackie.chan@example.com', '555-000-6666', 1,    4,    'Replaced faulty fan, issue resolved.',   '2024-12-30 15:00:00', '2025-01-01 10:00:00', '2025-01-01 11:00:00'),
  ('Software', 'Data sync failure across offices',    'CLOSED',  'NOT_RESOLVED','Karen Joy',    'karen.joy@example.com',   '555-000-7777', 1,    2,    'Issue closed due to lack of resources, not resolved.', '2024-12-25 08:30:00', '2024-12-30 16:45:00', '2024-12-31 09:00:00');
