<?php
require '../includes/auth.php';
require '../includes/db.php';
requireLogin();

$pdo = getDB();
$role = $_SESSION['role'];
$userId = $_SESSION['user_id'];
$initials = userInitials($_SESSION['user_name'] ?? 'Gym User');

$totalClasses = (int)$pdo->query("SELECT COUNT(*) FROM classes")->fetchColumn();
$pendingCount = (int)$pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'pending'")->fetchColumn();
$acceptedCount = (int)$pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'accepted'")->fetchColumn();
$myBookings = 0;

if ($role === 'user') {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE user_id = ?");
    $stmt->execute([$userId]);
    $myBookings = (int)$stmt->fetchColumn();
} else {
    $myBookings = (int)$pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
}

if ($role === 'user') {
    $stmt = $pdo->prepare("SELECT c.class_name, c.instructor, c.schedule, b.status
                           FROM bookings b
                           JOIN classes c ON c.id = b.class_id
                           WHERE b.user_id = ?
                           ORDER BY b.booking_date DESC LIMIT 5");
    $stmt->execute([$userId]);
} else {
    $stmt = $pdo->query("SELECT u.name, c.class_name, c.schedule, b.status
                         FROM bookings b
                         JOIN users u ON u.id = b.user_id
                         JOIN classes c ON c.id = b.class_id
                         ORDER BY b.booking_date DESC LIMIT 8");
}
$recent = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Dashboard | Gym Booking</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div class="brand">Gym Booking</div>
        <div class="menu-section">Menu</div>
        <a class="menu-link <?= isActive('dashboard', 'dashboard') ?>" data-icon="◧" href="dashboard.php">Dashboard</a>
        <?php if ($role === 'user'): ?><a class="menu-link" data-icon="+" href="book.php">Book a Class</a><?php endif; ?>
        <?php if ($role !== 'trainer'): ?><a class="menu-link" data-icon="▣" href="bookings.php">Bookings</a><?php endif; ?>
        <?php if ($role !== 'user'): ?><a class="menu-link" data-icon="!" href="pending_bookings.php">Pending Requests</a><?php endif; ?>
        <?php if ($role === 'admin'): ?>
            <a class="menu-link" data-icon="C" href="manage_classes.php">Manage Classes</a>
            <a class="menu-link" data-icon="U" href="manage_users.php">Manage Users</a>
        <?php endif; ?>
        <div class="menu-section">General</div>
        <a class="menu-link" data-icon="↩" href="../logout.php">Logout</a>
    </aside>

    <main class="content">
        <div class="searchbar">
            <input class="search-input" type="text" placeholder="Search class or user" />
            <div class="top-actions">
               
                <span class="profile-chip">
                    <span class="avatar"><?= htmlspecialchars($initials) ?></span>
                    <span class="profile-meta">
                        <span class="profile-name"><?= htmlspecialchars($_SESSION['user_name']) ?></span><br>
                        <span class="profile-role"><?= ucfirst($role) ?></span>
                    </span>
                </span>
            </div>
        </div>
        <div class="topbar">
            <div>
                <h2>Dashboard</h2>
                <p class="subtle">Plan, organize, and manage gym bookings with ease.</p>
            </div>
            <a href="<?= $role === 'user' ? 'book.php' : ($role === 'admin' ? 'manage_classes.php' : 'pending_bookings.php') ?>" class="btn btn-primary">
                <?= $role === 'user' ? 'Book Class' : ($role === 'admin' ? 'Add Class' : 'Review Requests') ?>
            </a>
        </div>

        <section class="card-grid">
            <div class="stat-card primary">
                <div class="stat-title"><?= $role === 'user' ? 'My Bookings' : 'Total Bookings' ?></div>
                <div class="stat-value"><?= $myBookings ?></div>
                <div class="stat-sub">Updated from latest activity</div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Accepted Bookings</div>
                <div class="stat-value"><?= $acceptedCount ?></div>
                <div class="stat-sub">Current confirmed bookings</div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Running Classes</div>
                <div class="stat-value"><?= $totalClasses ?></div>
                <div class="stat-sub">All listed gym classes</div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Pending Requests</div>
                <div class="stat-value"><?= $pendingCount ?></div>
                <div class="stat-sub">Awaiting trainer/admin action</div>
            </div>
        </section>

        <section class="panel-grid">
            <article class="panel">
                <h3 style="margin:0 0 4px">Booking Analytics</h3>
                <p class="subtle" style="margin-bottom:8px">Weekly booking trend snapshot</p>
                <div class="mini-bars">
                    <span style="height:78px"></span>
                    <span class="active" style="height:96px"></span>
                    <span class="mid" style="height:84px"></span>
                    <span class="active" style="height:110px"></span>
                    <span style="height:72px"></span>
                    <span style="height:66px"></span>
                    <span style="height:76px"></span>
                </div>
            </article>
            <article class="panel">
                <h3 style="margin:0 0 8px">Quick Actions</h3>
                <p class="subtle" style="margin:0 0 12px">Most used role actions</p>
                <div class="actions">
                    <?php if ($role === 'user'): ?>
                        <a class="btn btn-primary btn-sm" href="book.php">Book Class</a>
                        <a class="btn btn-gray btn-sm" href="bookings.php">View Bookings</a>
                    <?php elseif ($role === 'trainer'): ?>
                        <a class="btn btn-primary btn-sm" href="pending_bookings.php">Review Pending</a>
                    <?php else: ?>
                        <a class="btn btn-primary btn-sm" href="manage_classes.php">Manage Classes</a>
                        <a class="btn btn-gray btn-sm" href="manage_users.php">Manage Users</a>
                    <?php endif; ?>
                </div>
            </article>
        </section>

        <section class="panel">
            <h3 style="margin-top:0">Recent Bookings</h3>
            <table>
                <tr>
                    <th><?= $role === 'user' ? 'Class' : 'Member' ?></th>
                    <th><?= $role === 'user' ? 'Instructor' : 'Class' ?></th>
                    <th>Schedule</th>
                    <th>Status</th>
                </tr>
                <?php foreach ($recent as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($role === 'user' ? $row['class_name'] : $row['name']) ?></td>
                        <td><?= htmlspecialchars($role === 'user' ? $row['instructor'] : $row['class_name']) ?></td>
                        <td><?= htmlspecialchars($row['schedule']) ?></td>
                        <td><span class="badge badge-<?= htmlspecialchars($row['status']) ?>"><?= ucfirst($row['status']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </section>
    </main>
</div>
</body>
</html>
