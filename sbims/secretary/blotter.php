<?php
$page_title = "Blotter Cases";
require_once '../config/auth.php';
require_once '../config/connection.php';
require_once '../config/functions.php';

Auth::checkAuth();
Auth::checkRole(['secretary']);

$database = new Database();
$db = $database->getConnection();

// Search and filter
$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';

$query = "SELECT b.*, r.first_name, r.last_name, r.contact_number, u.full_name as handled_by_name 
          FROM blotters b 
          JOIN residents r ON b.complainant_id = r.id 
          JOIN users u ON b.handled_by = u.id 
          WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND (b.case_id LIKE :search OR r.first_name LIKE :search OR r.last_name LIKE :search OR b.respondent_name LIKE :search)";
    $params[':search'] = "%$search%";
}

if (!empty($status)) {
    $query .= " AND b.status = :status";
    $params[':status'] = $status;
}

if (!empty($date_from)) {
    $query .= " AND DATE(b.incident_date) >= :date_from";
    $params[':date_from'] = $date_from;
}

if (!empty($date_to)) {
    $query .= " AND DATE(b.incident_date) <= :date_to";
    $params[':date_to'] = $date_to;
}

$query .= " ORDER BY b.created_at DESC";

$stmt = $db->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$blotters = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/topbar.php'; ?>

<div class="main-container">
    <?php include '../includes/sidebar.php'; ?>
    
    <main class="content">
        <div class="page-header">
            <div class="page-title">
                <h1>Blotter Cases</h1>
                <p>Manage barangay blotter records and disputes</p>
            </div>
            <div class="page-actions">
                <a href="blotter_add.php" class="btn btn-primary">Add New Case</a>
            </div>
        </div>

        <!-- Search and Filter -->
        <div class="data-table">
            <div class="table-header">
                <h3>Blotter Cases</h3>
                <div class="table-actions">
                    <form method="GET" class="search-form" style="display: flex; gap: 0.5rem; align-items: center;">
                        <select name="status" onchange="this.form.submit()">
                            <option value="">All Status</option>
                            <option value="Pending" <?php echo $status == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="Ongoing" <?php echo $status == 'Ongoing' ? 'selected' : ''; ?>>Ongoing</option>
                            <option value="Settled" <?php echo $status == 'Settled' ? 'selected' : ''; ?>>Settled</option>
                            <option value="Referred" <?php echo $status == 'Referred' ? 'selected' : ''; ?>>Referred</option>
                        </select>
                        
                        <input type="date" name="date_from" placeholder="From Date" value="<?php echo $date_from; ?>">
                        <input type="date" name="date_to" placeholder="To Date" value="<?php echo $date_to; ?>">
                        
                        <input type="text" name="search" placeholder="Search cases..." value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit" class="btn btn-outline">Search</button>
                        <a href="blotter.php" class="btn btn-outline">Reset</a>
                    </form>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Case ID</th>
                        <th>Complainant</th>
                        <th>Respondent</th>
                        <th>Incident Type</th>
                        <th>Incident Date</th>
                        <th>Status</th>
                        <th>Handled By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($blotters) > 0): ?>
                        <?php foreach ($blotters as $blotter): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($blotter['case_id']); ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($blotter['first_name'] . ' ' . $blotter['last_name']); ?></strong><br>
                                <small><?php echo htmlspecialchars($blotter['contact_number']); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($blotter['respondent_name']); ?></td>
                            <td><?php echo htmlspecialchars($blotter['incident_type']); ?></td>
                            <td><?php echo formatDateTime($blotter['incident_date']); ?></td>
                            <td>
                                <span class="status status-<?php echo strtolower($blotter['status']); ?>">
                                    <?php echo $blotter['status']; ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($blotter['handled_by_name']); ?></td>
                            <td>
                                <a href="blotter_view.php?id=<?php echo $blotter['id']; ?>" class="btn btn-sm btn-outline">View</a>
                                <a href="blotter_edit.php?id=<?php echo $blotter['id']; ?>" class="btn btn-sm btn-outline">Edit</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" style="text-align: center; color: #64748b;">
                                No blotter cases found. <a href="blotter_add.php">Add the first case</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>