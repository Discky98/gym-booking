<?php
session_start();
session_unset();
session_destroy();
$_SESSION['flash_message'] = ['type' => 'ok', 'text' => 'You have been logged out successfully.'];
header("Location: ../login.php");
exit;
