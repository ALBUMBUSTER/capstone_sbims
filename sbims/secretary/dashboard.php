<?php
$page_title = "Secretary Dashboard";
require_once '../config/auth.php';
require_once '../config/connection.php';
require_once '../config/functions.php';

Auth::checkAuth();
Auth::checkRole(['secretary']);

// Get statistics
$database = new Database();
$db = $database->getConnection();

// Count residents
$resident_query = "SELECT COUNT(*) as total FROM residents";
$resident_stmt = $db->prepare($resident_query);
$resident_stmt->execute();
$total_residents = $resident_stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Count active blotters
$blotter_query = "SELECT COUNT(*) as active FROM blotters WHERE status IN ('Pending', 'Ongoing')";
$blotter_stmt = $db->prepare($blotter_query);
$blotter_stmt->execute();
$active_blotters = $blotter_stmt->fetch(PDO::FETCH_ASSOC)['active'];

// Count pending certificates
$cert_query = "SELECT COUNT(*) as pending FROM certificates WHERE status = 'Pending'";
$cert_stmt = $db->prepare($cert_query);
$cert_stmt->execute();
$pending_certificates = $cert_stmt->fetch(PDO::FETCH_ASSOC)['pending'];

// Recent certificate requests
$recent_certs_query = "SELECT c.*, r.first_name, r.last_name 
                      FROM certificates c 
                      JOIN residents r ON c.resident_id = r.id 
                      ORDER BY c.created_at DESC 
                      LIMIT 5";
$recent_certs_stmt = $db->prepare($recent_certs_query);
$recent_certs_stmt->execute();
$recent_certificates = $recent_certs_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/topbar.php'; ?>

<div class="main-container">
    <?php include '../includes/sidebar.php'; ?>
    
    <main class="content">
        <div class="page-header">
            <div class="page-title">
                <h1>Secretary Dashboard</h1>
                <p>Barangay Management Panel</p>
            </div>
            <div class="page-actions">
                <a href="residents_add.php" class="btn btn-primary">Add New Resident</a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Residents</h3>
                <div class="stat-value"><?php echo $total_residents; ?></div>
            </div>
            <div class="stat-card">
                <h3>Active Blotter Cases</h3>
                <div class="stat-value"><?php echo $active_blotters; ?></div>
            </div>
            <div class="stat-card">
                <h3>Pending Certificates</h3>
                <div class="stat-value"><?php echo $pending_certificates; ?></div>
            </div>
            <div class="stat-card">
                <h3>Certificates Today</h3>
                <div class="stat-value">0</div>
            </div>
        </div>

        <!-- Recent Certificate Requests -->
        <div class="data-table">
            <div class="table-header">
                <h3>Recent Certificate Requests</h3>
                <a href="certificates.php" class="btn btn-outline">View All</a>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Certificate ID</th>
                        <th>Resident Name</th>
                        <th>Type</th>
                        <th>Date Requested</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_certificates as $cert): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($cert['certificate_id']); ?></td>
                        <td><?php echo htmlspecialchars($cert['first_name'] . ' ' . $cert['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($cert['certificate_type']); ?></td>
                        <td><?php echo formatDate($cert['created_at']); ?></td>
                        <td><span class="status status-<?php echo strtolower($cert['status']); ?>"><?php echo $cert['status']; ?></span></td>
                        <td>
                            <a href="certificate_<?php echo strtolower($cert['certificate_type']); ?>.php?id=<?php echo $cert['id']; ?>" class="btn btn-sm btn-outline">Process</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>