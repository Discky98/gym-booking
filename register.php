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
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = 'user';

    try {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $hash, $role]);
        header("Location: login.php");
        exit;
    } catch (PDOException $e) {
        $msg = "<div class='alert err'>Email already exists or invalid input.</div>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Register | Gym Booking</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card">
        <h1>Create Account</h1>
        <p>Register as a gym member and start booking classes.</p>
        <?= $msg ?>
        <form method="POST">
            <label>Full Name</label>
            <input type="text" name="name" required>
            <label>Email</label>
            <input type="email" name="email" required>
            <label>Password</label>
            <input type="password" name="password" required minlength="6">
            <button type="submit" class="btn btn-primary" style="width:100%">Register</button>
        </form>
        <p style="margin-top:12px">Already registered? <a href="login.php" style="color:#e94f76">Login</a></p>
    </div>
</div>
</body>
</html>
