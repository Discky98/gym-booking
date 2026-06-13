<?php
require '../includes/auth.php';
require '../includes/db.php';
requireRole(['user', 'admin']);

$pdo = getDB();
$role = $_SESSION['role'];
$userId = $_SESSION['user_id'];
$initials = userInitials($_SESSION['user_name'] ?? 'Gym User');
$msg = "";

if (isset($_GET['delete'])) {
    $bookingId = (int)$_GET['delete'];
    if ($role === 'admin') {
        $stmt = $pdo->prepare("DELETE FROM bookings WHERE id = ?");
        $stmt->execute([$bookingId]);
    } else {
        $stmt = $pdo->prepare("DELETE FROM bookings WHERE id = ? AND user_id = ?");
        $stmt->execute([$bookingId, $userId]);
    }
    $msg = "<div class='alert ok'>Booking deleted successfully.</div>";
}

if ($role === 'admin') {
    $stmt = $pdo->query("SELECT b.id, u.name, c.class_name, c.instructor, c.schedule, b.status
                         FROM bookings b
                         JOIN users u ON u.id = b.user_id
                         JOIN classes c ON c.id = b.class_id
                         ORDER BY b.booking_date DESC");
} else {
    $stmt = $pdo->prepare("SELECT b.id, c.class_name, c.instructor, c.schedule, b.status
                           FROM bookings b
                           JOIN classes c ON c.id = b.class_id
                           WHERE b.user_id = ?
                           ORDER BY b.booking_date DESC");
    $stmt->execute([$userId]);
}
$bookings = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Bookings</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div class="brand">Gym Booking</div>
        <div class="menu-section">Menu</div>
        <a class="menu-link" data-icon="◧" href="dashboard.php">Dashboard</a>
        <?php if ($role === 'user'): ?><a class="menu-link" data-icon="+" href="book.php">Book a Class</a><?php endif; ?>
        <a class="menu-link active" data-icon="▣" href="bookings.php"><?= $role === 'admin' ? 'All Bookings' : 'My Bookings' ?></a>
        <?php if ($role === 'admin'): ?>
            <a class="menu-link" data-icon="!" href="pending_bookings.php">Pending Requests</a>
            <a class="menu-link" data-icon="C" href="manage_classes.php">Manage Classes</a>
            <a class="menu-link" data-icon="U" href="manage_users.php">Manage Users</a>
        <?php endif; ?>
        <div class="menu-section">General</div>
        <a class="menu-link" data-icon="↩" href="../logout.php">Logout</a>
    </aside>
    <main class="content">
        <div class="searchbar">
            <input class="search-input" type="text" placeholder="Search booking" />
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
        <div class="topbar"><h2><?= $role === 'admin' ? 'All Bookings' : 'My Bookings' ?></h2></div>
        <div class="panel">
            <?= $msg ?>
            <table>
                <tr>
                    <th>#</th>
                    <?php if ($role === 'admin'): ?><th>User</th><?php endif; ?>
                    <th>Class</th>
                    <th>Instructor</th>
                    <th>Schedule</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($bookings as $i => $b): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <?php if ($role === 'admin'): ?><td><?= htmlspecialchars($b['name']) ?></td><?php endif; ?>
                    <td><?= htmlspecialchars($b['class_name']) ?></td>
                    <td><?= htmlspecialchars($b['instructor']) ?></td>
                    <td><?= htmlspecialchars($b['schedule']) ?></td>
                    <td><span class="badge badge-<?= htmlspecialchars($b['status']) ?>"><?= ucfirst($b['status']) ?></span></td>
                    <td>
                        <a class="btn btn-danger btn-sm" href="bookings.php?delete=<?= $b['id'] ?>" onclick="return confirm('Delete this booking?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </main>
</div>
</body>
</html>
