<?php
$page_title = "Certificate Approvals";
require_once '../config/auth.php';
require_once '../config/connection.php';
require_once '../config/functions.php';

Auth::checkAuth();
Auth::checkRole(['captain']);

$database = new Database();
$db = $database->getConnection();

// Get pending certificates for approval
$pending_query = "SELECT c.*, r.first_name, r.last_name, r.address, r.purok, u.full_name as issued_by_name
                  FROM certificates c 
                  JOIN residents r ON c.resident_id = r.id 
                  JOIN users u ON c.issued_by = u.id 
                  WHERE c.status = 'Pending' 
                  ORDER BY c.created_at DESC";
$pending_stmt = $db->prepare($pending_query);
$pending_stmt->execute();
$pending_certificates = $pending_stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle approvals
if (isset($_GET['approve'])) {
    $cert_id = $_GET['approve'];
    $approve_query = "UPDATE certificates SET status = 'Approved', approved_by = :approved_by, issued_date = NOW() WHERE id = :id";
    $approve_stmt = $db->prepare($approve_query);
    $approve_stmt->bindParam(':approved_by', $_SESSION['user_id']);
    $approve_stmt->bindParam(':id', $cert_id);
    
    if ($approve_stmt->execute()) {
        // Get certificate info for logging
        $cert_info_query = "SELECT certificate_id FROM certificates WHERE id = :id";
        $cert_info_stmt = $db->prepare($cert_info_query);
        $cert_info_stmt->bindParam(':id', $cert_id);
        $cert_info_stmt->execute();
        $cert_info = $cert_info_stmt->fetch(PDO::FETCH_ASSOC);
        
        Auth::logActivity($_SESSION['user_id'], 'Approve Certificate', "Approved certificate: " . $cert_info['certificate_id']);
        $_SESSION['success'] = "Certificate approved successfully!";
        header("Location: approvals.php");
        exit();
    }
}

// Handle rejections
if (isset($_GET['reject'])) {
    $cert_id = $_GET['reject'];
    $reject_query = "UPDATE certificates SET status = 'Rejected', approved_by = :approved_by WHERE id = :id";
    $reject_stmt = $db->prepare($reject_query);
    $reject_stmt->bindParam(':approved_by', $_SESSION['user_id']);
    $reject_stmt->bindParam(':id', $cert_id);
    
    if ($reject_stmt->execute()) {
        Auth::logActivity($_SESSION['user_id'], 'Reject Certificate', "Rejected certificate ID: $cert_id");
        $_SESSION['success'] = "Certificate rejected!";
        header("Location: approvals.php");
        exit();
    }
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/topbar.php'; ?>

<div class="main-container">
    <?php include '../includes/sidebar.php'; ?>
    
    <main class="content">
        <div class="page-header">
            <h1>Certificate Approvals</h1>
            <p>Review and approve certificate requests</p>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <div class="data-table">
            <div class="table-header">
                <h3>Pending Approvals</h3>
                <div class="table-actions">
                    <span class="badge"><?php echo count($pending_certificates); ?> pending</span>
                </div>
            </div>

            <?php if (count($pending_certificates) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Certificate ID</th>
                            <th>Resident</th>
                            <th>Certificate Type</th>
                            <th>Purpose</th>
                            <th>Issued By</th>
                            <th>Date Requested</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pending_certificates as $cert): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($cert['certificate_id']); ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($cert['first_name'] . ' ' . $cert['last_name']); ?></strong><br>
                                <small><?php echo htmlspecialchars($cert['purok']); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($cert['certificate_type']); ?></td>
                            <td><?php echo htmlspecialchars($cert['purpose']); ?></td>
                            <td><?php echo htmlspecialchars($cert['issued_by_name']); ?></td>
                            <td><?php echo formatDateTime($cert['created_at']); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="../secretary/certificate_clearance.php?id=<?php echo $cert['id']; ?>" class="btn btn-sm btn-outline" target="_blank">Review</a>
                                    <a href="approvals.php?approve=<?php echo $cert['id']; ?>" class="btn btn-sm btn-success" onclick="return confirm('Approve this certificate?')">Approve</a>
                                    <a href="approvals.php?reject=<?php echo $cert['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Reject this certificate?')">Reject</a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-data">
                    <p>No pending certificate approvals</p>
                    <p class="text-muted">All certificate requests have been processed.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Recently Approved -->
        <?php
        $recent_approved_query = "SELECT c.*, r.first_name, r.last_name, u.full_name as approved_by_name
                                 FROM certificates c 
                                 JOIN residents r ON c.resident_id = r.id 
                                 JOIN users u ON c.approved_by = u.id 
                                 WHERE c.status = 'Approved' 
                                 ORDER BY c.issued_date DESC 
                                 LIMIT 10";
        $recent_approved_stmt = $db->prepare($recent_approved_query);
        $recent_approved_stmt->execute();
        $recent_approved = $recent_approved_stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <?php if (count($recent_approved) > 0): ?>
        <div class="data-table">
            <div class="table-header">
                <h3>Recently Approved Certificates</h3>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Certificate ID</th>
                        <th>Resident</th>
                        <th>Type</th>
                        <th>Approved By</th>
                        <th>Date Approved</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_approved as $cert): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($cert['certificate_id']); ?></td>
                        <td><?php echo htmlspecialchars($cert['first_name'] . ' ' . $cert['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($cert['certificate_type']); ?></td>
                        <td><?php echo htmlspecialchars($cert['approved_by_name']); ?></td>
                        <td><?php echo $cert['issued_date'] ? formatDateTime($cert['issued_date']) : '-'; ?></td>
                        <td><span class="status status-approved">Approved</span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </main>
</div>

<style>
.action-buttons {
    display: flex;
    gap: 0.3rem;
    flex-wrap: wrap;
}

.action-buttons .btn {
    font-size: 0.8rem;
    padding: 0.2rem 0.5rem;
}

.badge {
    background: var(--warning);
    color: white;
    padding: 0.3rem 0.8rem;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 500;
}

.no-data {
    text-align: center;
    padding: 3rem;
    color: var(--secondary);
}

.text-muted {
    color: #94a3b8;
    font-size: 0.9rem;
}
</style>

<?php include '../includes/footer.php'; ?>