<?php
$page_title = "Certificate Requests";
require_once '../config/auth.php';
require_once '../config/connection.php';
require_once '../config/functions.php';

Auth::checkAuth();
Auth::checkRole(['secretary']);

$database = new Database();
$db = $database->getConnection();

// Search and filter
$search = $_GET['search'] ?? '';
$certificate_type = $_GET['certificate_type'] ?? '';
$status = $_GET['status'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';

$query = "SELECT c.*, r.first_name, r.last_name, r.address, r.purok, 
                 u1.full_name as issued_by_name, u2.full_name as approved_by_name
          FROM certificates c 
          JOIN residents r ON c.resident_id = r.id 
          LEFT JOIN users u1 ON c.issued_by = u1.id 
          LEFT JOIN users u2 ON c.approved_by = u2.id 
          WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND (c.certificate_id LIKE :search OR r.first_name LIKE :search OR r.last_name LIKE :search)";
    $params[':search'] = "%$search%";
}

if (!empty($certificate_type)) {
    $query .= " AND c.certificate_type = :certificate_type";
    $params[':certificate_type'] = $certificate_type;
}

if (!empty($status)) {
    $query .= " AND c.status = :status";
    $params[':status'] = $status;
}

if (!empty($date_from)) {
    $query .= " AND DATE(c.created_at) >= :date_from";
    $params[':date_from'] = $date_from;
}

if (!empty($date_to)) {
    $query .= " AND DATE(c.created_at) <= :date_to";
    $params[':date_to'] = $date_to;
}

$query .= " ORDER BY c.created_at DESC";

$stmt = $db->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$certificates = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/topbar.php'; ?>

<div class="main-container">
    <?php include '../includes/sidebar.php'; ?>
    
    <main class="content">
        <div class="page-header">
            <div class="page-title">
                <h1>Certificate Requests</h1>
                <p>Manage barangay certificate issuance</p>
            </div>
            <div class="page-actions">
                <a href="certificate_clearance.php" class="btn btn-primary">Issue Certificate</a>
            </div>
        </div>

        <!-- Search and Filter -->
        <div class="data-table">
            <div class="table-header">
                <h3>Certificate Requests</h3>
                <div class="table-actions">
                    <form method="GET" class="search-form" style="display: flex; gap: 0.5rem; align-items: center;">
                        <select name="certificate_type" onchange="this.form.submit()">
                            <option value="">All Types</option>
                            <option value="Clearance" <?php echo $certificate_type == 'Clearance' ? 'selected' : ''; ?>>Clearance</option>
                            <option value="Indigency" <?php echo $certificate_type == 'Indigency' ? 'selected' : ''; ?>>Indigency</option>
                            <option value="Residency" <?php echo $certificate_type == 'Residency' ? 'selected' : ''; ?>>Residency</option>
                        </select>
                        
                        <select name="status" onchange="this.form.submit()">
                            <option value="">All Status</option>
                            <option value="Pending" <?php echo $status == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="Approved" <?php echo $status == 'Approved' ? 'selected' : ''; ?>>Approved</option>
                            <option value="Released" <?php echo $status == 'Released' ? 'selected' : ''; ?>>Released</option>
                        </select>
                        
                        <input type="date" name="date_from" placeholder="From Date" value="<?php echo $date_from; ?>">
                        <input type="date" name="date_to" placeholder="To Date" value="<?php echo $date_to; ?>">
                        
                        <input type="text" name="search" placeholder="Search certificates..." value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit" class="btn btn-outline">Search</button>
                        <a href="certificates.php" class="btn btn-outline">Reset</a>
                    </form>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Certificate ID</th>
                        <th>Resident</th>
                        <th>Type</th>
                        <th>Purpose</th>
                        <th>Date Requested</th>
                        <th>Status</th>
                        <th>Approved By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($certificates) > 0): ?>
                        <?php foreach ($certificates as $cert): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($cert['certificate_id']); ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($cert['first_name'] . ' ' . $cert['last_name']); ?></strong><br>
                                <small><?php echo htmlspecialchars($cert['purok']); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($cert['certificate_type']); ?></td>
                            <td><?php echo htmlspecialchars($cert['purpose']); ?></td>
                            <td><?php echo formatDate($cert['created_at']); ?></td>
                            <td>
                                <span class="status status-<?php echo strtolower($cert['status']); ?>">
                                    <?php echo $cert['status']; ?>
                                </span>
                            </td>
                            <td><?php echo $cert['approved_by_name'] ? htmlspecialchars($cert['approved_by_name']) : '-'; ?></td>
                            <td>
                                <?php if ($cert['certificate_type'] == 'Clearance'): ?>
                                    <a href="certificate_clearance.php?id=<?php echo $cert['id']; ?>" class="btn btn-sm btn-outline">View</a>
                                <?php elseif ($cert['certificate_type'] == 'Indigency'): ?>
                                    <a href="certificate_indigency.php?id=<?php echo $cert['id']; ?>" class="btn btn-sm btn-outline">View</a>
                                <?php elseif ($cert['certificate_type'] == 'Residency'): ?>
                                    <a href="certificate_residency.php?id=<?php echo $cert['id']; ?>" class="btn btn-sm btn-outline">View</a>
                                <?php endif; ?>
                                
                                <?php if ($cert['status'] == 'Pending'): ?>
                                    <a href="certificates.php?approve=<?php echo $cert['id']; ?>" class="btn btn-sm btn-success" onclick="return confirm('Approve this certificate?')">Approve</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" style="text-align: center; color: #64748b;">
                                No certificate requests found. <a href="certificate_clearance.php">Issue the first certificate</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<?php
// Handle approval
if (isset($_GET['approve'])) {
    $cert_id = $_GET['approve'];
    $approve_query = "UPDATE certificates SET status = 'Approved', approved_by = :approved_by WHERE id = :id";
    $approve_stmt = $db->prepare($approve_query);
    $approve_stmt->bindParam(':approved_by', $_SESSION['user_id']);
    $approve_stmt->bindParam(':id', $cert_id);
    
    if ($approve_stmt->execute()) {
        Auth::logActivity($_SESSION['user_id'], 'Approve Certificate', "Approved certificate ID: " . $certificates[array_search($cert_id, array_column($certificates, 'id'))]['certificate_id']);
        $_SESSION['success'] = "Certificate approved successfully!";
        header("Location: certificates.php");
        exit();
    }
}
?>

<?php include '../includes/footer.php'; ?>