<?php
require '../includes/auth.php';
require '../includes/db.php';
requireRole(['admin']);

$pdo = getDB();
$msg = "";
$editing = null;
$initials = userInitials($_SESSION['user_name'] ?? 'Gym User');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $className = trim($_POST['class_name'] ?? '');
    $instructor = trim($_POST['instructor'] ?? '');
    $schedule = trim($_POST['schedule'] ?? '');
    $capacity = (int)($_POST['capacity'] ?? 20);

    if (!empty($_POST['class_id'])) {
        $stmt = $pdo->prepare("UPDATE classes SET class_name = ?, instructor = ?, schedule = ?, capacity = ? WHERE id = ?");
        $stmt->execute([$className, $instructor, $schedule, $capacity, (int)$_POST['class_id']]);
        $msg = "<div class='alert ok'>Class updated.</div>";
    } else {
        $stmt = $pdo->prepare("INSERT INTO classes (class_name, instructor, schedule, capacity) VALUES (?, ?, ?, ?)");
        $stmt->execute([$className, $instructor, $schedule, $capacity]);
        $msg = "<div class='alert ok'>Class added.</div>";
    }
}

if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM classes WHERE id = ?");
    $stmt->execute([(int)$_GET['delete']]);
    $msg = "<div class='alert ok'>Class deleted.</div>";
}

if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM classes WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $editing = $stmt->fetch();
}

$classes = $pdo->query("SELECT * FROM classes ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Manage Classes</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div class="brand">Gym Booking</div>
        <div class="menu-section">Menu</div>
        <a class="menu-link" data-icon="◧" href="dashboard.php">Dashboard</a>
        <a class="menu-link" data-icon="▣" href="bookings.php">All Bookings</a>
        <a class="menu-link" data-icon="!" href="pending_bookings.php">Pending Requests</a>
        <a class="menu-link active" data-icon="C" href="manage_classes.php">Manage Classes</a>
        <a class="menu-link" data-icon="U" href="manage_users.php">Manage Users</a>
        <div class="menu-section">General</div>
        <a class="menu-link" data-icon="↩" href="../logout.php">Logout</a>
    </aside>
    <main class="content">
        <div class="searchbar">
            <input class="search-input" type="text" placeholder="Search class list" />
            <div class="top-actions">
             
                <span class="profile-chip">
                    <span class="avatar"><?= htmlspecialchars($initials) ?></span>
                    <span class="profile-meta">
                        <span class="profile-name"><?= htmlspecialchars($_SESSION['user_name']) ?></span><br>
                        <span class="profile-role">Admin</span>
                    </span>
                </span>
            </div>
        </div>
        <div class="topbar"><h2>Manage Classes</h2></div>
        <div class="panel">
            <?= $msg ?>
            <form method="POST">
                <input type="hidden" name="class_id" value="<?= $editing['id'] ?? '' ?>">
                <label>Class Name</label><input name="class_name" required value="<?= htmlspecialchars($editing['class_name'] ?? '') ?>">
                <label>Instructor</label><input name="instructor" required value="<?= htmlspecialchars($editing['instructor'] ?? '') ?>">
                <label>Schedule</label><input name="schedule" required value="<?= htmlspecialchars($editing['schedule'] ?? '') ?>">
                <label>Capacity</label><input name="capacity" type="number" min="1" required value="<?= htmlspecialchars((string)($editing['capacity'] ?? 20)) ?>">
                <button class="btn btn-primary" type="submit"><?= $editing ? 'Update Class' : 'Add Class' ?></button>
                <?php if ($editing): ?><a class="btn btn-gray" href="manage_classes.php">Cancel</a><?php endif; ?>
            </form>
        </div>

        <div class="panel">
            <table>
                <tr><th>#</th><th>Class</th><th>Instructor</th><th>Schedule</th><th>Capacity</th><th>Actions</th></tr>
                <?php foreach ($classes as $c): ?>
                <tr>
                    <td><?= $c['id'] ?></td>
                    <td><?= htmlspecialchars($c['class_name']) ?></td>
                    <td><?= htmlspecialchars($c['instructor']) ?></td>
                    <td><?= htmlspecialchars($c['schedule']) ?></td>
                    <td><?= htmlspecialchars((string)$c['capacity']) ?></td>
                    <td class="actions">
                        <a class="btn btn-blue btn-sm" href="manage_classes.php?edit=<?= $c['id'] ?>">Edit</a>
                        <a class="btn btn-danger btn-sm" href="manage_classes.php?delete=<?= $c['id'] ?>" onclick="return confirm('Delete this class?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </main>
</div>
</body>
</html>
