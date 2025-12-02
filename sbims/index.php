<?php
require_once 'config/auth.php';
require_once 'config/connection.php';

// Redirect based on user role
if (isset($_SESSION['user_role'])) {
    switch ($_SESSION['user_role']) {
        case 'admin':
            header("Location: admin/dashboard.php");
            break;
        case 'captain':
            header("Location: captain/dashboard.php");
            break;
        case 'secretary':
            header("Location: secretary/dashboard.php");
            break;
        case 'resident':
            header("Location: resident/dashboard.php");
            break;
        default:
            header("Location: login.php");
    }
    exit();
} else {
    header("Location: login.php");
    exit();
}
?>