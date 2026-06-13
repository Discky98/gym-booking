CREATE DATABASE IF NOT EXISTS gym_booking;
USE gym_booking;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'trainer', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_name VARCHAR(100) NOT NULL,
    instructor VARCHAR(100) NOT NULL,
    schedule VARCHAR(100) NOT NULL,
    capacity INT DEFAULT 20
);

CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    class_id INT NOT NULL,
    status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE
);

INSERT INTO users (name, email, password, role) VALUES
('Admin User', 'admin@gym.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('John Member', 'john@gym.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user'),
('Sarah Trainer', 'sarah@gym.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'trainer')
ON DUPLICATE KEY UPDATE name = VALUES(name), role = VALUES(role);

INSERT INTO classes (class_name, instructor, schedule, capacity) VALUES
('Yoga Flow', 'Emma Watson', 'Mon/Wed 8am', 15),
('HIIT Blast', 'Mike Ross', 'Tue/Thu 6pm', 20),
('Spin Cycle', 'Sarah Trainer', 'Fri 5pm', 12);
