<?php
require '../includes/auth.php';
require '../includes/db.php';
requireRole(['trainer', 'admin']);

$pdo = getDB();
$role = $_SESSION['role'];
$initials = userInitials($_SESSION['user_name'] ?? 'Gym User');
$msg = "";

if (isset($_POST['update_status'])) {
    $bookingId = (int)$_POST['booking_id'];
    $status = $_POST['status'] ?? 'pending';
    if (in_array($status, ['accepted', 'rejected'], true)) {
        $stmt = $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?");
        $stmt->execute([$status, $bookingId]);
        $msg = "<div class='alert ok'>Booking status updated.</div>";
    }
}

$bookings = $pdo->query("SELECT b.id, u.name, u.email, c.class_name, c.schedule, b.status
                         FROM bookings b
                         JOIN users u ON u.id = b.user_id
                         JOIN classes c ON c.id = b.class_id
                         WHERE b.status = 'pending'
                         ORDER BY b.booking_date ASC")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Pending Bookings</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div class="brand">Gym Booking</div>
        <div class="menu-section">Menu</div>
        <a class="menu-link" data-icon="◧" href="dashboard.php">Dashboard</a>
        <?php if ($role === 'admin'): ?><a class="menu-link" data-icon="▣" href="bookings.php">All Bookings</a><?php endif; ?>
        <a class="menu-link active" data-icon="!" href="pending_bookings.php">Pending Requests</a>
        <?php if ($role === 'admin'): ?>
            <a class="menu-link" data-icon="C" href="manage_classes.php">Manage Classes</a>
            <a class="menu-link" data-icon="U" href="manage_users.php">Manage Users</a>
        <?php endif; ?>
        <div class="menu-section">General</div>
        <a class="menu-link" data-icon="↩" href="logout.php">Logout</a>
    </aside>
    <main class="content">
        <div class="searchbar">
            <input class="search-input" type="text" placeholder="Search pending requests" />
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
        <div class="topbar"><h2>Pending Bookings</h2></div>
        <div class="panel">
            <?= $msg ?>
            <table>
                <tr><th>#</th><th>User</th><th>Email</th><th>Class</th><th>Schedule</th><th>Status</th><th>Actions</th></tr>
                <?php foreach ($bookings as $i => $b): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($b['name']) ?></td>
                    <td><?= htmlspecialchars($b['email']) ?></td>
                    <td><?= htmlspecialchars($b['class_name']) ?></td>
                    <td><?= htmlspecialchars($b['schedule']) ?></td>
                    <td><span class="badge badge-pending">Pending</span></td>
                    <td class="actions">
                        <form method="POST" style="display:inline">
                            <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                            <input type="hidden" name="status" value="accepted">
                            <button type="submit" name="update_status" class="btn btn-blue btn-sm">Accept</button>
                        </form>
                        <form method="POST" style="display:inline">
                            <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                            <input type="hidden" name="status" value="rejected">
                            <button type="submit" name="update_status" class="btn btn-danger btn-sm">Reject</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </main>
</div>
</body>
</html>
