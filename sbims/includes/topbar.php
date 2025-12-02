<?php
require_once '../config/auth.php';
Auth::checkTimeout();
?>
<header class="header">
    <div class="logo-container">
        <div class="logo">BL</div>
        <div class="system-title">
            <h1>SBIMS-PRO</h1>
            <p>Brgy. Libertad, Isabel, Leyte</p>
        </div>
    </div>
    
    <div class="user-menu">
        <div class="notification-icon">
            <span>🔔</span>
            <span class="notification-badge">3</span>
        </div>
        <div class="user-profile">
            <span><?php echo $_SESSION['full_name']; ?> (<?php echo ucfirst($_SESSION['user_role']); ?>)</span>
            <div class="user-dropdown">
                <a href="../logout.php">Logout</a>
            </div>
        </div>
    </div>
</header>