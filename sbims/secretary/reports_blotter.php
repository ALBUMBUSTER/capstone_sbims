<?php
$page_title = "Blotter Reports";
require_once '../config/auth.php';
require_once '../config/connection.php';
require_once '../config/functions.php';

Auth::checkAuth();
Auth::checkRole(['secretary']);

$database = new Database();
$db = $database->getConnection();

// Get filter parameters
$status = $_GET['status'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';
$incident_type = $_GET['incident_type'] ?? '';

$query = "SELECT b.*, r.first_name, r.last_name, r.contact_number, u.full_name as handled_by_name 
          FROM blotters b 
          JOIN residents r ON b.complainant_id = r.id 
          JOIN users u ON b.handled_by = u.id 
          WHERE 1=1";
$params = [];

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

if (!empty($incident_type)) {
    $query .= " AND b.incident_type = :incident_type";
    $params[':incident_type'] = $incident_type;
}

$query .= " ORDER BY b.incident_date DESC";

$stmt = $db->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$blotters = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get unique incident types
$types_query = "SELECT DISTINCT incident_type FROM blotters ORDER BY incident_type";
$types_stmt = $db->prepare($types_query);
$types_stmt->execute();
$incident_types = $types_stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/topbar.php'; ?>

<div class="main-container">
    <?php include '../includes/sidebar.php'; ?>
    
    <main class="content">
        <div class="page-header">
            <div class="page-title">
                <h1>Blotter Reports</h1>
                <p>Generate and export blotter case reports</p>
            </div>
            <div class="page-actions">
                <button onclick="exportToPDF()" class="btn btn-primary">Export to PDF</button>
                <button onclick="exportToExcel()" class="btn btn-success">Export to Excel</button>
            </div>
        </div>

        <!-- Report Filters -->
        <div class="form-container">
            <h3>Report Filters</h3>
            <form method="GET" class="filter-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="status">Case Status</label>
                        <select id="status" name="status">
                            <option value="">All Status</option>
                            <option value="Pending" <?php echo $status == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="Ongoing" <?php echo $status == 'Ongoing' ? 'selected' : ''; ?>>Ongoing</option>
                            <option value="Settled" <?php echo $status == 'Settled' ? 'selected' : ''; ?>>Settled</option>
                            <option value="Referred" <?php echo $status == 'Referred' ? 'selected' : ''; ?>>Referred</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="incident_type">Incident Type</label>
                        <select id="incident_type" name="incident_type">
                            <option value="">All Types</option>
                            <?php foreach ($incident_types as $type): ?>
                                <option value="<?php echo $type; ?>" <?php echo $incident_type == $type ? 'selected' : ''; ?>>
                                    <?php echo $type; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="date_from">From Date</label>
                        <input type="date" id="date_from" name="date_from" value="<?php echo $date_from; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="date_to">To Date</label>
                        <input type="date" id="date_to" name="date_to" value="<?php echo $date_to; ?>">
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Generate Report</button>
                    <a href="reports_blotter.php" class="btn btn-outline">Reset Filters</a>
                </div>
            </form>
        </div>

        <!-- Report Summary -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Cases</h3>
                <div class="stat-value"><?php echo count($blotters); ?></div>
            </div>
            
            <div class="stat-card">
                <h3>Pending</h3>
                <div class="stat-value">
                    <?php 
                    $pending_count = array_filter($blotters, function($b) { return $b['status'] == 'Pending'; });
                    echo count($pending_count);
                    ?>
                </div>
            </div>
            
            <div class="stat-card">
                <h3>Ongoing</h3>
                <div class="stat-value">
                    <?php 
                    $ongoing_count = array_filter($blotters, function($b) { return $b['status'] == 'Ongoing'; });
                    echo count($ongoing_count);
                    ?>
                </div>
            </div>
            
            <div class="stat-card">
                <h3>Settled</h3>
                <div class="stat-value">
                    <?php 
                    $settled_count = array_filter($blotters, function($b) { return $b['status'] == 'Settled'; });
                    echo count($settled_count);
                    ?>
                </div>
            </div>
        </div>

        <!-- Blotter Cases List -->
        <div class="data-table">
            <div class="table-header">
                <h3>Blotter Cases</h3>
                <div class="table-actions">
                    <span class="badge"><?php echo count($blotters); ?> cases</span>
                </div>
            </div>

            <table id="blotterTable">
                <thead>
                    <tr>
                        <th>Case ID</th>
                        <th>Complainant</th>
                        <th>Respondent</th>
                        <th>Incident Type</th>
                        <th>Incident Date</th>
                        <th>Status</th>
                        <th>Handled By</th>
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
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; color: #64748b;">
                                No blotter cases found matching the selected filters.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<script>
function exportToPDF() {
    alert('PDF export functionality would be implemented here');
}

function exportToExcel() {
    let table = document.getElementById('blotterTable');
    let html = table.outerHTML;
    
    let blob = new Blob([html], {type: 'application/vnd.ms-excel'});
    let url = URL.createObjectURL(blob);
    let a = document.createElement('a');
    a.href = url;
    a.download = 'blotter-report-<?php echo date('Y-m-d'); ?>.xls';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}
</script>

<?php include '../includes/footer.php'; ?>