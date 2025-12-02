<?php
$page_title = "Admin Dashboard";
require_once '../config/auth.php';
require_once '../config/connection.php';
require_once '../config/functions.php';

Auth::checkAuth();
Auth::checkRole(['admin']);

// Get statistics
$database = new Database();
$db = $database->getConnection();

// Count users by role
$user_stats = [];
$roles = ['admin', 'captain', 'secretary', 'resident'];
foreach ($roles as $role) {
    $query = "SELECT COUNT(*) as count FROM users WHERE role = :role AND is_active = 1";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':role', $role);
    $stmt->execute();
    $user_stats[$role] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
}

// Recent activity logs
$activity_query = "SELECT al.*, u.username 
                  FROM activity_logs al 
                  JOIN users u ON al.user_id = u.id 
                  ORDER BY al.created_at DESC 
                  LIMIT 10";
$activity_stmt = $db->prepare($activity_query);
$activity_stmt->execute();
$recent_activities = $activity_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/topbar.php'; ?>

<div class="main-container">
    <?php include '../includes/sidebar.php'; ?>
    
    <main class="content">
        <div class="page-header">
            <div class="page-title">
                <h1>Admin Dashboard</h1>
                <p>System Administration Panel</p>
            </div>
            <div class="page-actions">
                <a href="system_logs.php" class="btn btn-outline">View All Logs</a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Administrators</h3>
                <div class="stat-value"><?php echo $user_stats['admin']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Barangay Captains</h3>
                <div class="stat-value"><?php echo $user_stats['captain']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Secretaries</h3>
                <div class="stat-value"><?php echo $user_stats['secretary']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Residents</h3>
                <div class="stat-value"><?php echo $user_stats['resident']; ?></div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="data-table">
            <div class="table-header">
                <h3>Recent System Activity</h3>
                <a href="system_logs.php" class="btn btn-outline">View All</a>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Action</th>
                        <th>Description</th>
                        <th>Date & Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_activities as $activity): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($activity['username']); ?></td>
                        <td><?php echo htmlspecialchars($activity['action']); ?></td>
                        <td><?php echo htmlspecialchars($activity['description']); ?></td>
                        <td><?php echo formatDateTime($activity['created_at']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>