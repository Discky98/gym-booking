<?php
require '../includes/auth.php';
require '../includes/db.php';
requireRole(['admin']);

$pdo = getDB();
$msg = "";
$initials = userInitials($_SESSION['user_name'] ?? 'Gym User');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'user';

    if (!in_array($role, ['user', 'trainer', 'admin'], true)) {
        $role = 'user';
    }

    if ($name === '' || $email === '' || $password === '') {
        $msg = "<div class='alert err'>All fields are required to create a user.</div>";
    } else {
        try {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $hash, $role]);
            $msg = "<div class='alert ok'>User created successfully.</div>";
        } catch (PDOException $e) {
            $msg = "<div class='alert err'>Could not create user. Email may already exist.</div>";
        }
    }
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id !== (int)$_SESSION['user_id']) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $msg = "<div class='alert ok'>User deleted.</div>";
    } else {
        $msg = "<div class='alert err'>You cannot delete your own account.</div>";
    }
}

$users = $pdo->query("SELECT id, name, email, role, created_at FROM users ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
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
        <a class="menu-link" data-icon="C" href="manage_classes.php">Manage Classes</a>
        <a class="menu-link active" data-icon="U" href="manage_users.php">Manage Users</a>
        <div class="menu-section">General</div>
        <a class="menu-link" data-icon="↩" href="logout.php">Logout</a>
    </aside>
    <main class="content">
        <div class="searchbar">
            <input class="search-input" type="text" placeholder="Search users" />
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
        <div class="topbar"><h2>Manage Users</h2></div>
        <div class="panel">
            <?= $msg ?>
            <h3 style="margin-top:0">Add New User</h3>
            <form method="POST">
                <input type="hidden" name="add_user" value="1">
                <label>Full Name</label>
                <input type="text" name="name" required>
                <label>Email</label>
                <input type="email" name="email" required>
                <label>Password</label>
                <input type="password" name="password" required minlength="6">
                <label>Role</label>
                <select name="role" required>
                    <option value="user">User</option>
                    <option value="trainer">Trainer</option>
                    <option value="admin">Admin</option>
                </select>
                <button class="btn btn-primary" type="submit">Create User</button>
            </form>
        </div>
        <div class="panel">
            <table>
                <tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Joined</th><th>Action</th></tr>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= $u['id'] ?></td>
                    <td><?= htmlspecialchars($u['name']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><span class="badge <?= roleBadgeClass($u['role']) ?>"><?= ucfirst($u['role']) ?></span></td>
                    <td><?= htmlspecialchars($u['created_at']) ?></td>
                    <td>
                        <?php if ((int)$u['id'] === (int)$_SESSION['user_id']): ?>
                            <span class="subtle">Current user</span>
                        <?php else: ?>
                            <a class="btn btn-danger btn-sm" href="manage_users.php?delete=<?= $u['id'] ?>" onclick="return confirm('Delete this user?')">Delete</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </main>
</div>
</body>
</html>
