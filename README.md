# 🏋️ GYM CLASS BOOKING SYSTEM
## Multi-Role System | Admin · User · Trainer

**PHP · MySQL · PDO · HTML · CSS**  
*School Class Project | 2025*

---

## 📌 1. Project Overview

The **Gym Class Booking System** is a role-based web application where:
- **Users** can browse and book gym classes.
- **Trainers** can accept or reject booking requests.
- **Admin** manages all users, classes, and oversees all bookings.

The system demonstrates all CRUD operations (INSERT, SELECT, UPDATE, DELETE) using **PDO prepared statements** and features a **modern dashboard UI** inspired by the [Task Management Dashboard](https://dribbble.com/shots/25241984-Task-Management-Dashboard) design (dark sidebar + card layout).

### What the System Does (by Role)

| Operation | User | Trainer | Admin |
|-----------|------|---------|-------|
| Register/Login | ✅ | ✅ | ✅ |
| Browse classes | ✅ | ✅ | ✅ |
| Book a class (INSERT) | ✅ | ❌ | ❌ |
| View own bookings | ✅ | ❌ | ✅ (all) |
| Accept/Reject booking (UPDATE) | ❌ | ✅ | ✅ |
| Cancel own booking (DELETE) | ✅ | ❌ | ✅ |
| Add/Edit/Delete classes | ❌ | ❌ | ✅ |
| Manage all users | ❌ | ❌ | ✅ |

### Technologies Used

| Technology | Purpose |
|------------|---------|
| PHP | Server logic, sessions, role handling |
| MySQL | Database for users, classes, bookings |
| PDO | Secure DB connection & prepared statements |
| HTML/CSS | Dashboard UI (dark sidebar, cards, tables) |
| JavaScript | Confirm dialogs, form validation |

---

## 🗂️ 2. Project File Structure

Place the entire `gym/` folder inside `htdocs` (XAMPP) or `www` (WAMP).
gym/
├── setup.sql # Run once in phpMyAdmin
├── style.css # Shared styles (dark sidebar + cards)
├── includes/
│ └── db.php # PDO connection
├── register.php # Registration (role selection)
├── login.php # Login with role detection
├── logout.php # Destroy session
├── pages/
│ ├── dashboard.php # Role-specific dashboard
│ ├── bookings.php # My Bookings (for User/Admin)
│ ├── book.php # Book a class (User only)
│ ├── manage_classes.php # Admin only: CRUD classes
│ ├── manage_users.php # Admin only: view/delete users
│ └── pending_bookings.php # Trainer/Admin: accept/reject
└── assets/ # (optional) images, icons

text

---

## 🗄️ 3. Database Setup

### Step 1 – Create Database & Tables

Open **phpMyAdmin** → SQL tab → paste & run the entire `setup.sql` (provided below).

### `setup.sql` – Complete Schema

```sql
CREATE DATABASE IF NOT EXISTS gym_booking;
USE gym_booking;

-- Users table (supports 3 roles)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'trainer', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Classes table
CREATE TABLE classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_name VARCHAR(100) NOT NULL,
    instructor VARCHAR(100) NOT NULL,
    schedule VARCHAR(100) NOT NULL,
    capacity INT DEFAULT 20
);

-- Bookings table (with status for accept/reject)
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    class_id INT NOT NULL,
    status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE
);

-- Insert sample data
INSERT INTO users (name, email, password, role) VALUES
('Admin User', 'admin@gym.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('John Member', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user'),
('Sarah Trainer', 'sarah@gym.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'trainer');

INSERT INTO classes (class_name, instructor, schedule, capacity) VALUES
('Yoga Flow', 'Emma Watson', 'Mon/Wed 8am', 15),
('HIIT Blast', 'Mike Ross', 'Tue/Thu 6pm', 20),
('Spin Cycle', 'Sarah Trainer', 'Fri 5pm', 12);

-- Sample booking (pending)
INSERT INTO bookings (user_id, class_id, status) VALUES (2, 1, 'pending');
Default passwords (for testing): all sample users have password = password

🔌 4. PDO Connection (includes/db.php)
php
<?php
function getDB() {
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=gym_booking", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}
?>
🔐 5. Role-Based Access & CRUD Matrix
Page	Role Access	CRUD	Description
register.php	All	INSERT	Choose role during registration
login.php	All	SELECT	Redirects to role-specific dashboard
pages/book.php	User only	INSERT	Book a class (status = pending)
pages/bookings.php	User, Admin	SELECT, DELETE	User views own; Admin views all
pages/pending_bookings.php	Trainer, Admin	UPDATE	Accept/reject bookings (status → accepted/rejected)
pages/manage_classes.php	Admin only	INSERT, UPDATE, DELETE	Full class management
pages/manage_users.php	Admin only	SELECT, DELETE	View/delete any user
pages/dashboard.php	All	SELECT	Role-specific stats & summary
🎨 6. UI Design (Inspired by Dribbble Dashboard)
The interface follows the dark sidebar + card layout from the reference design.

Element	Description
Sidebar	Dark background (#1a1a2e), navigation icons + labels
Top Bar	Logo, user greeting, role badge (User/Trainer/Admin)
Stat Cards	3 cards showing key metrics (e.g., "My Bookings", "Pending Requests", "Total Classes")
Data Tables	Clean tables with action buttons (Edit, Delete, Accept, Reject)
Forms	Stacked inputs with floating labels
Alerts	Green success / red error messages (session-based flash)
Sample dashboard layout for User:

Sidebar links: Dashboard, Book a Class, My Bookings, Logout

Main area: "Welcome, John" + stats cards + recent bookings table

🚀 7. How to Run the Project
Install XAMPP and start Apache + MySQL.

Copy gym/ folder to C:\xampp\htdocs\

Open http://localhost/phpmyadmin → SQL tab → paste setup.sql → Go.

Visit:

Register: http://localhost/gym/register.php

Login: http://localhost/gym/login.php

Test Accounts (after running setup.sql)
Role	Email	Password
Admin	admin@gym.com	password
User	john@example.com	password
Trainer	sarah@gym.com	password
🔄 8. How Booking Acceptance Works (Trainer/Admin)
Code Snippet – Accept Booking (pending_bookings.php)
php
if (isset($_POST['update_status'])) {
    $booking_id = $_POST['booking_id'];
    $new_status = $_POST['status']; // 'accepted' or 'rejected'
    $stmt = $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?");
    $stmt->execute([$new_status, $booking_id]);
    header("Location: pending_bookings.php?msg=updated");
}
📚 9. OOP & PHP Concepts Demonstrated
Concept	File/Location
PDO Class	includes/db.php
PDOException	Try-catch blocks
Session Handling	session_start(), $_SESSION['user_id'], $_SESSION['role']
Prepared Statements	All queries (prepare() + execute([]))
Password Hashing	password_hash() / password_verify()
Role-Based Redirects	if ($_SESSION['role'] == 'trainer') {...}
Foreign Key Constraints	ON DELETE CASCADE in SQL
GET/POST	$_GET['delete'], $_POST['class_id']
Flash Messages	Session variable + unset after display
🧪 10. Testing CRUD Operations
Operation	How to Test (as correct role)
INSERT (booking)	Login as User → Book a Class → Check bookings table
SELECT (bookings)	User → My Bookings shows pending bookings
UPDATE (status)	Login as Trainer → Pending Bookings → Accept/Reject
DELETE (booking)	User → My Bookings → Cancel booking
INSERT (class)	Admin → Manage Classes → Add new class
DELETE (user)	Admin → Manage Users → Delete a user (cascades their bookings)
📄 11. Example Code: Registration with Role
php
// register.php snippet
$role = $_POST['role'] ?? 'user'; // user, trainer, admin
$hashed = password_hash($_POST['password'], PASSWORD_DEFAULT);
$stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
$stmt->execute([$_POST['name'], $_POST['email'], $hashed, $role]);
🤝 12. Acknowledgments
UI inspiration: Task Management Dashboard on Dribbble

Built with PHP, MySQL, and ❤️ for school project.