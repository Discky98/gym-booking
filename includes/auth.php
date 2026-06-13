<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function requireLogin(): void {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../login.php");
        exit;
    }
}

function requireRole(array $roles): void {
    requireLogin();
    if (!in_array($_SESSION['role'] ?? '', $roles, true)) {
        header("Location: dashboard.php");
        exit;
    }
}

function isActive(string $current, string $item): string {
    return $current === $item ? "active" : "";
}

function roleBadgeClass(string $role): string {
    return match ($role) {
        'admin' => 'badge-admin',
        'trainer' => 'badge-trainer',
        default => 'badge-user'
    };
}

function userInitials(string $name): string {
    $parts = preg_split('/\s+/', trim($name));
    if (!$parts || $parts[0] === '') {
        return 'GU';
    }
    $first = strtoupper(substr($parts[0], 0, 1));
    $second = isset($parts[1]) ? strtoupper(substr($parts[1], 0, 1)) : strtoupper(substr($parts[0], 1, 1));
    return trim($first . $second) ?: 'GU';
}
?>
