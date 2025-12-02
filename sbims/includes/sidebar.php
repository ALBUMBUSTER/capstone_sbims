<?php
$current_role = $_SESSION['user_role'];
?>
<nav class="sidebar">
    <ul class="sidebar-menu">
        <li class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
            <a href="dashboard.php">
                <span>📊</span>
                <span class="menu-text">Dashboard</span>
            </a>
        </li>
        
        <?php if ($current_role == 'admin'): ?>
            <!-- Admin Menu -->
            <li class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>">
                <a href="users.php">
                    <span>👥</span>
                    <span class="menu-text">User Management</span>
                </a>
            </li>
            <li class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'barangay_info.php' ? 'active' : ''; ?>">
                <a href="barangay_info.php">
                    <span>🏢</span>
                    <span class="menu-text">Barangay Info</span>
                </a>
            </li>
            <li class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'system_logs.php' ? 'active' : ''; ?>">
                <a href="system_logs.php">
                    <span>📋</span>
                    <span class="menu-text">System Logs</span>
                </a>
            </li>
            <li class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'backup.php' ? 'active' : ''; ?>">
                <a href="backup.php">
                    <span>💾</span>
                    <span class="menu-text">Backup & Restore</span>
                </a>
            </li>
            
        <?php elseif ($current_role == 'secretary'): ?>
            <!-- Secretary Menu -->
            <li class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'residents.php' ? 'active' : ''; ?>">
                <a href="residents.php">
                    <span>👥</span>
                    <span class="menu-text">Resident Records</span>
                </a>
            </li>
            <li class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'blotter.php' ? 'active' : ''; ?>">
                <a href="blotter.php">
                    <span>📝</span>
                    <span class="menu-text">Blotter Cases</span>
                </a>
            </li>
            <li class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'certificates.php' ? 'active' : ''; ?>">
                <a href="certificates.php">
                    <span>📄</span>
                    <span class="menu-text">Certificates</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="reports_residents.php">
                    <span>📊</span>
                    <span class="menu-text">Reports</span>
                </a>
            </li>
            
        <?php elseif ($current_role == 'captain'): ?>
            <!-- Captain Menu -->
            <li class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'approvals.php' ? 'active' : ''; ?>">
                <a href="approvals.php">
                    <span>✅</span>
                    <span class="menu-text">Approvals</span>
                </a>
            </li>
            <li class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'blotter_view.php' ? 'active' : ''; ?>">
                <a href="blotter_view.php">
                    <span>📋</span>
                    <span class="menu-text">Blotter Records</span>
                </a>
            </li>
            <li class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'resident_stats.php' ? 'active' : ''; ?>">
                <a href="resident_stats.php">
                    <span>📈</span>
                    <span class="menu-text">Statistics</span>
                </a>
            </li>
            <li class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'announcements.php' ? 'active' : ''; ?>">
                <a href="announcements.php">
                    <span>📢</span>
                    <span class="menu-text">Announcements</span>
                </a>
            </li>
            
        <?php elseif ($current_role == 'resident'): ?>
            <!-- Resident Menu -->
            <li class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'announcements.php' ? 'active' : ''; ?>">
                <a href="announcements.php">
                    <span>📢</span>
                    <span class="menu-text">Announcements</span>
                </a>
            </li>
            <li class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'certificate_status.php' ? 'active' : ''; ?>">
                <a href="certificate_status.php">
                    <span>📄</span>
                    <span class="menu-text">My Certificates</span>
                </a>
            </li>
            <li class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'blotter_summary.php' ? 'active' : ''; ?>">
                <a href="blotter_summary.php">
                    <span>📝</span>
                    <span class="menu-text">Blotter Summary</span>
                </a>
            </li>
            <li class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'stats.php' ? 'active' : ''; ?>">
                <a href="stats.php">
                    <span>📊</span>
                    <span class="menu-text">Community Stats</span>
                </a>
            </li>
        <?php endif; ?>
        
        <li class="menu-item">
            <a href="../logout.php">
                <span>🔒</span>
                <span class="menu-text">Logout</span>
            </a>
        </li>
    </ul>
</nav>