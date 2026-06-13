<?php
session_start();
require 'includes/db.php';

if (isset($_SESSION['user_id'])) {
    header("Location: pages/dashboard.php");
    exit;
}

$pdo = getDB();
$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['role'] = $user['role'];
        header("Location: pages/dashboard.php");
        exit;
    }

    $msg = "<div class='alert err'>Invalid email or password.</div>";
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card">
        <h1>Welcome Back</h1>
        <p>Sign in to your gym dashboard.</p>
        <?= $msg ?>
        <form method="POST">
            <label>Email</label>
            <input type="email" name="email" required>
            <label>Password</label>
            <input type="password" name="password" required>
            <button type="submit" class="btn btn-primary" style="width:100%">Login</button>
        </form>
        <p style="margin-top:12px">No account? <a href="register.php" style="color:#e94f76">Register</a></p>
    </div>
</div>
</body>
</html>
