# 🏋️ GYM CLASS BOOKING SYSTEM
## Multi-Role System | Admin · User · Trainer

**PHP · MySQL · HTML · CSS**  
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

