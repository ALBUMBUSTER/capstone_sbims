<?php
$page_title = "Captain Dashboard";
require_once '../config/auth.php';
require_once '../config/connection.php';
require_once '../config/functions.php';

Auth::checkAuth();
Auth::checkRole(['captain']);

// Get statistics
$database = new Database();
$db = $database->getConnection();

// Count pending approvals
$pending_certs_query = "SELECT COUNT(*) as pending FROM certificates WHERE status = 'Pending'";
$pending_certs_stmt = $db->prepare($pending_certs_query);
$pending_certs_stmt->execute();
$pending_certificates = $pending_certs_stmt->fetch(PDO::FETCH_ASSOC)['pending'];

// Count active blotters
$active_blotters_query = "SELECT COUNT(*) as active FROM blotters WHERE status IN ('Pending', 'Ongoing')";
$active_blotters_stmt = $db->prepare($active_blotters_query);
$active_blotters_stmt->execute();
$active_blotters = $active_blotters_stmt->fetch(PDO::FETCH_ASSOC)['active'];

// Count total residents
$residents_query = "SELECT COUNT(*) as total FROM residents";
$residents_stmt = $db->prepare($residents_query);
$residents_stmt->execute();
$total_residents = $residents_stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Count settled cases this month
$settled_query = "SELECT COUNT(*) as settled FROM blotters WHERE status = 'Settled' AND MONTH(created_at) = MONTH(CURRENT_DATE())";
$settled_stmt = $db->prepare($settled_query);
$settled_stmt->execute();
$settled_cases = $settled_stmt->fetch(PDO::FETCH_ASSOC)['settled'];

// Pending certificate requests
$cert_requests_query = "SELECT c.*, r.first_name, r.last_name 
                       FROM certificates c 
                       JOIN residents r ON c.resident_id = r.id 
                       WHERE c.status = 'Pending' 
                       ORDER BY c.created_at DESC 
                       LIMIT 5";
$cert_requests_stmt = $db->prepare($cert_requests_query);
$cert_requests_stmt->execute();
$pending_requests = $cert_requests_stmt->fetchAll(PDO::FETCH_ASSOC);

// Recent blotter cases
$recent_blotters_query = "SELECT b.*, r.first_name, r.last_name 
                         FROM blotters b 
                         JOIN residents r ON b.complainant_id = r.id 
                         ORDER BY b.created_at DESC 
                         LIMIT 5";
$recent_blotters_stmt = $db->prepare($recent_blotters_query);
$recent_blotters_stmt->execute();
$recent_blotters = $recent_blotters_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/topbar.php'; ?>

<div class="main-container">
    <?php include '../includes/sidebar.php'; ?>
    
    <main class="content">
        <div class="page-header">
            <div class="page-title">
                <h1>Captain Dashboard</h1>
                <p>Barangay Oversight and Approval Panel</p>
            </div>
            <div class="page-actions">
                <a href="approvals.php" class="btn btn-primary">Review Approvals</a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Pending Approvals</h3>
                <div class="stat-value"><?php echo $pending_certificates; ?></div>
                <div class="stat-trend">Certificate requests awaiting approval</div>
            </div>
            
            <div class="stat-card">
                <h3>Active Blotter Cases</h3>
                <div class="stat-value"><?php echo $active_blotters; ?></div>
                <div class="stat-trend">Cases requiring attention</div>
            </div>
            
            <div class="stat-card">
                <h3>Total Residents</h3>
                <div class="stat-value"><?php echo $total_residents; ?></div>
                <div class="stat-trend">Registered in system</div>
            </div>
            
            <div class="stat-card">
                <h3>Cases Settled</h3>
                <div class="stat-value"><?php echo $settled_cases; ?></div>
                <div class="stat-trend">This month</div>
            </div>
        </div>

        <div class="dashboard-grid">
            <!-- Pending Certificate Requests -->
            <div class="data-table">
                <div class="table-header">
                    <h3>Pending Certificate Approvals</h3>
                    <a href="approvals.php" class="btn btn-outline">View All</a>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Certificate ID</th>
                            <th>Resident Name</th>
                            <th>Type</th>
                            <th>Purpose</th>
                            <th>Date Requested</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($pending_requests) > 0): ?>
                            <?php foreach ($pending_requests as $request): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($request['certificate_id']); ?></td>
                                <td><?php echo htmlspecialchars($request['first_name'] . ' ' . $request['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($request['certificate_type']); ?></td>
                                <td><?php echo htmlspecialchars($request['purpose']); ?></td>
                                <td><?php echo formatDate($request['created_at']); ?></td>
                                <td>
                                    <a href="approvals.php?action=review&id=<?php echo $request['id']; ?>" class="btn btn-sm btn-primary">Review</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; color: #64748b;">No pending certificate requests</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Recent Blotter Cases -->
            <div class="data-table">
                <div class="table-header">
                    <h3>Recent Blotter Cases</h3>
                    <a href="blotter_view.php" class="btn btn-outline">View All</a>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Case ID</th>
                            <th>Complainant</th>
                            <th>Incident Type</th>
                            <th>Date Filed</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_blotters as $blotter): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($blotter['case_id']); ?></td>
                            <td><?php echo htmlspecialchars($blotter['first_name'] . ' ' . $blotter['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($blotter['incident_type']); ?></td>
                            <td><?php echo formatDate($blotter['created_at']); ?></td>
                            <td><span class="status status-<?php echo strtolower($blotter['status']); ?>"><?php echo $blotter['status']; ?></span></td>
                            <td>
                                <a href="blotter_view.php?id=<?php echo $blotter['id']; ?>" class="btn btn-sm btn-outline">View</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>