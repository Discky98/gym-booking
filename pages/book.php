<?php
require '../includes/auth.php';
require '../includes/db.php';
requireRole(['user']);

$pdo = getDB();
$msg = "";
$selected = $_GET['class_id'] ?? '';
$initials = userInitials($_SESSION['user_name'] ?? 'Gym User');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $classId = (int)($_POST['class_id'] ?? 0);
    $stmt = $pdo->prepare("INSERT INTO bookings (user_id, class_id, status) VALUES (?, ?, 'pending')");
    $stmt->execute([$_SESSION['user_id'], $classId]);
    $msg = "<div class='alert ok'>Class booked successfully. It is now pending approval.</div>";
}

$classes = $pdo->query("SELECT * FROM classes ORDER BY class_name ASC")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Book a Class</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div class="brand">Gym Booking</div>
        <div class="menu-section">Menu</div>
        <a class="menu-link" data-icon="◧" href="dashboard.php">Dashboard</a>
        <a class="menu-link active" data-icon="+" href="book.php">Book a Class</a>
        <a class="menu-link" data-icon="▣" href="bookings.php">My Bookings</a>
        <div class="menu-section">General</div>
        <a class="menu-link" data-icon="↩" href="../logout.php">Logout</a>
    </aside>
    <main class="content">
        <div class="searchbar">
            <input class="search-input" type="text" placeholder="Search class" />
            <div class="top-actions">
                <span class="profile-chip">
                    <span class="avatar"><?= htmlspecialchars($initials) ?></span>
                    <span class="profile-meta">
                        <span class="profile-name"><?= htmlspecialchars($_SESSION['user_name']) ?></span><br>
                        <span class="profile-role">User</span>
                    </span>
                </span>
            </div>
        </div>
        <div class="topbar"><h2>Book a Class</h2></div>
        <div class="panel" style="max-width:560px">
            <?= $msg ?>
            <form method="POST">
                <label>Select Class</label>
                <select name="class_id" required>
                    <option value="">-- Choose a class --</option>
                    <?php foreach ($classes as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= ((string)$selected === (string)$c['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['class_name']) ?> | <?= htmlspecialchars($c['instructor']) ?> | <?= htmlspecialchars($c['schedule']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <button class="btn btn-primary" type="submit">Confirm Booking</button>
            </form>
        </div>
    </main>
</div>
</body>
</html>
