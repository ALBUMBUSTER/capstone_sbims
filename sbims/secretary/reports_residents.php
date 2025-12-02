<?php
$page_title = "Resident Reports";
require_once '../config/auth.php';
require_once '../config/connection.php';
require_once '../config/functions.php';

Auth::checkAuth();
Auth::checkRole(['secretary']);

$database = new Database();
$db = $database->getConnection();

// Get filter parameters
$purok = $_GET['purok'] ?? '';
$civil_status = $_GET['civil_status'] ?? '';
$gender = $_GET['gender'] ?? '';
$voter_status = $_GET['voter_status'] ?? '';

$query = "SELECT * FROM residents WHERE 1=1";
$params = [];

if (!empty($purok)) {
    $query .= " AND purok = :purok";
    $params[':purok'] = $purok;
}

if (!empty($civil_status)) {
    $query .= " AND civil_status = :civil_status";
    $params[':civil_status'] = $civil_status;
}

if (!empty($gender)) {
    $query .= " AND gender = :gender";
    $params[':gender'] = $gender;
}

if (!empty($voter_status)) {
    if ($voter_status == 'voter') {
        $query .= " AND is_voter = 1";
    } elseif ($voter_status == 'non_voter') {
        $query .= " AND is_voter = 0";
    }
}

$query .= " ORDER BY last_name, first_name";

$stmt = $db->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$residents = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get unique values for filters
$puroks_query = "SELECT DISTINCT purok FROM residents ORDER BY purok";
$puroks_stmt = $db->prepare($puroks_query);
$puroks_stmt->execute();
$puroks = $puroks_stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/topbar.php'; ?>

<div class="main-container">
    <?php include '../includes/sidebar.php'; ?>
    
    <main class="content">
        <div class="page-header">
            <div class="page-title">
                <h1>Resident Reports</h1>
                <p>Generate and export resident demographic reports</p>
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
                        <label for="purok">Purok</label>
                        <select id="purok" name="purok">
                            <option value="">All Purok</option>
                            <?php foreach ($puroks as $p): ?>
                                <option value="<?php echo $p; ?>" <?php echo $purok == $p ? 'selected' : ''; ?>>
                                    <?php echo $p; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="civil_status">Civil Status</label>
                        <select id="civil_status" name="civil_status">
                            <option value="">All Status</option>
                            <option value="Single" <?php echo $civil_status == 'Single' ? 'selected' : ''; ?>>Single</option>
                            <option value="Married" <?php echo $civil_status == 'Married' ? 'selected' : ''; ?>>Married</option>
                            <option value="Widowed" <?php echo $civil_status == 'Widowed' ? 'selected' : ''; ?>>Widowed</option>
                            <option value="Divorced" <?php echo $civil_status == 'Divorced' ? 'selected' : ''; ?>>Divorced</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select id="gender" name="gender">
                            <option value="">All Gender</option>
                            <option value="Male" <?php echo $gender == 'Male' ? 'selected' : ''; ?>>Male</option>
                            <option value="Female" <?php echo $gender == 'Female' ? 'selected' : ''; ?>>Female</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="voter_status">Voter Status</label>
                        <select id="voter_status" name="voter_status">
                            <option value="">All</option>
                            <option value="voter" <?php echo $voter_status == 'voter' ? 'selected' : ''; ?>>Voter</option>
                            <option value="non_voter" <?php echo $voter_status == 'non_voter' ? 'selected' : ''; ?>>Non-Voter</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Generate Report</button>
                    <a href="reports_residents.php" class="btn btn-outline">Reset Filters</a>
                </div>
            </form>
        </div>

        <!-- Report Summary -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Residents</h3>
                <div class="stat-value"><?php echo count($residents); ?></div>
            </div>
            
            <div class="stat-card">
                <h3>Male</h3>
                <div class="stat-value">
                    <?php 
                    $male_count = array_filter($residents, function($r) { return $r['gender'] == 'Male'; });
                    echo count($male_count);
                    ?>
                </div>
            </div>
            
            <div class="stat-card">
                <h3>Female</h3>
                <div class="stat-value">
                    <?php 
                    $female_count = array_filter($residents, function($r) { return $r['gender'] == 'Female'; });
                    echo count($female_count);
                    ?>
                </div>
            </div>
            
            <div class="stat-card">
                <h3>Voters</h3>
                <div class="stat-value">
                    <?php 
                    $voter_count = array_filter($residents, function($r) { return $r['is_voter']; });
                    echo count($voter_count);
                    ?>
                </div>
            </div>
        </div>

        <!-- Resident List -->
        <div class="data-table">
            <div class="table-header">
                <h3>Resident List</h3>
                <div class="table-actions">
                    <span class="badge"><?php echo count($residents); ?> residents</span>
                </div>
            </div>

            <table id="residentTable">
                <thead>
                    <tr>
                        <th>Resident ID</th>
                        <th>Name</th>
                        <th>Gender</th>
                        <th>Birthdate</th>
                        <th>Civil Status</th>
                        <th>Purok</th>
                        <th>Contact</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($residents) > 0): ?>
                        <?php foreach ($residents as $resident): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($resident['resident_id']); ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($resident['first_name'] . ' ' . $resident['last_name']); ?></strong>
                                <?php if ($resident['middle_name']): ?>
                                    <br><small><?php echo htmlspecialchars($resident['middle_name']); ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($resident['gender']); ?></td>
                            <td><?php echo formatDate($resident['birthdate']); ?></td>
                            <td><?php echo htmlspecialchars($resident['civil_status']); ?></td>
                            <td><?php echo htmlspecialchars($resident['purok']); ?></td>
                            <td><?php echo htmlspecialchars($resident['contact_number']); ?></td>
                            <td>
                                <?php
                                $badges = [];
                                if ($resident['is_voter']) $badges[] = '<span class="status status-approved">Voter</span>';
                                if ($resident['is_4ps']) $badges[] = '<span class="status status-pending">4PS</span>';
                                if ($resident['is_senior']) $badges[] = '<span class="status status-settled">Senior</span>';
                                if ($resident['is_pwd']) $badges[] = '<span class="status status-referred">PWD</span>';
                                echo implode(' ', $badges);
                                ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" style="text-align: center; color: #64748b;">
                                No residents found matching the selected filters.
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
    alert('PDF export functionality would be implemented here with a PDF library like TCPDF or Dompdf');
    // In a real implementation, this would generate and download a PDF
}

function exportToExcel() {
    // Simple Excel export using table data
    let table = document.getElementById('residentTable');
    let html = table.outerHTML;
    
    // Create a blob and download
    let blob = new Blob([html], {type: 'application/vnd.ms-excel'});
    let url = URL.createObjectURL(blob);
    let a = document.createElement('a');
    a.href = url;
    a.download = 'resident-report-<?php echo date('Y-m-d'); ?>.xls';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}
</script>

<style>
.filter-form {
    background: #f8fafc;
    padding: 1.5rem;
    border-radius: 8px;
    margin-bottom: 2rem;
}

.filter-form .form-row {
    grid-template-columns: repeat(4, 1fr);
}

.badge {
    background: var(--primary);
    color: white;
    padding: 0.3rem 0.8rem;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 500;
}

@media (max-width: 1024px) {
    .filter-form .form-row {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .filter-form .form-row {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include '../includes/footer.php'; ?>