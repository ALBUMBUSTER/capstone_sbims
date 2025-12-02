<?php
$page_title = "View Blotter Case";
require_once '../config/auth.php';
require_once '../config/connection.php';
require_once '../config/functions.php';

Auth::checkAuth();
Auth::checkRole(['captain']);

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ../secretary/blotter.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

$blotter_id = $_GET['id'];
$query = "SELECT b.*, r.first_name, r.last_name, r.contact_number, r.address as complainant_address, 
                 u.full_name as handled_by_name 
          FROM blotters b 
          JOIN residents r ON b.complainant_id = r.id 
          JOIN users u ON b.handled_by = u.id 
          WHERE b.id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $blotter_id);
$stmt->execute();
$blotter = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$blotter) {
    header("Location: ../secretary/blotter.php");
    exit();
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/topbar.php'; ?>

<div class="main-container">
    <?php include '../includes/sidebar.php'; ?>
    
    <main class="content">
        <div class="page-header">
            <div class="page-title">
                <h1>Blotter Case Details</h1>
                <p>Case ID: <?php echo htmlspecialchars($blotter['case_id']); ?></p>
            </div>
            <div class="page-actions">
                <a href="../secretary/blotter.php" class="btn btn-outline">Back to List</a>
            </div>
        </div>

        <div class="case-details">
            <!-- Case Status -->
            <div class="detail-section">
                <h3>Case Status</h3>
                <div class="status-display">
                    <span class="status status-<?php echo strtolower($blotter['status']); ?> large">
                        <?php echo $blotter['status']; ?>
                    </span>
                    <?php if ($blotter['resolved_date']): ?>
                        <p><strong>Resolved on:</strong> <?php echo formatDateTime($blotter['resolved_date']); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Complainant Information -->
            <div class="detail-section">
                <h3>Complainant Information</h3>
                <div class="detail-grid">
                    <div class="detail-item">
                        <label>Name:</label>
                        <span><?php echo htmlspecialchars($blotter['first_name'] . ' ' . $blotter['last_name']); ?></span>
                    </div>
                    <div class="detail-item">
                        <label>Contact:</label>
                        <span><?php echo htmlspecialchars($blotter['contact_number']); ?></span>
                    </div>
                    <div class="detail-item">
                        <label>Address:</label>
                        <span><?php echo htmlspecialchars($blotter['complainant_address']); ?></span>
                    </div>
                </div>
            </div>

            <!-- Respondent Information -->
            <div class="detail-section">
                <h3>Respondent Information</h3>
                <div class="detail-grid">
                    <div class="detail-item">
                        <label>Name:</label>
                        <span><?php echo htmlspecialchars($blotter['respondent_name']); ?></span>
                    </div>
                    <div class="detail-item">
                        <label>Address:</label>
                        <span><?php echo htmlspecialchars($blotter['respondent_address']); ?></span>
                    </div>
                </div>
            </div>

            <!-- Incident Details -->
            <div class="detail-section">
                <h3>Incident Details</h3>
                <div class="detail-grid">
                    <div class="detail-item">
                        <label>Incident Type:</label>
                        <span><?php echo htmlspecialchars($blotter['incident_type']); ?></span>
                    </div>
                    <div class="detail-item">
                        <label>Date & Time:</label>
                        <span><?php echo formatDateTime($blotter['incident_date']); ?></span>
                    </div>
                    <div class="detail-item">
                        <label>Location:</label>
                        <span><?php echo htmlspecialchars($blotter['incident_location']); ?></span>
                    </div>
                </div>
                <div class="detail-item full-width">
                    <label>Description:</label>
                    <div class="description-box"><?php echo nl2br(htmlspecialchars($blotter['description'])); ?></div>
                </div>
            </div>

            <!-- Case Resolution -->
            <?php if ($blotter['resolution']): ?>
            <div class="detail-section">
                <h3>Case Resolution</h3>
                <div class="detail-item full-width">
                    <label>Resolution Details:</label>
                    <div class="resolution-box"><?php echo nl2br(htmlspecialchars($blotter['resolution'])); ?></div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Case Metadata -->
            <div class="detail-section">
                <h3>Case Information</h3>
                <div class="detail-grid">
                    <div class="detail-item">
                        <label>Handled By:</label>
                        <span><?php echo htmlspecialchars($blotter['handled_by_name']); ?></span>
                    </div>
                    <div class="detail-item">
                        <label>Date Filed:</label>
                        <span><?php echo formatDateTime($blotter['created_at']); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<style>
.case-details {
    background: white;
    border-radius: 10px;
    padding: 2rem;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}

.detail-section {
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid #e2e8f0;
}

.detail-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.detail-section h3 {
    color: var(--dark);
    margin-bottom: 1rem;
    font-size: 1.2rem;
}

.detail-grid {
    display: grid;
    gap: 1rem;
}

.detail-item {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.detail-item.full-width {
    grid-column: 1 / -1;
}

.detail-item label {
    font-weight: 600;
    color: var(--secondary);
    font-size: 0.9rem;
}

.description-box, .resolution-box {
    background: #f8fafc;
    padding: 1rem;
    border-radius: 5px;
    border-left: 4px solid var(--primary);
}

.status.large {
    padding: 0.5rem 1rem;
    font-size: 1rem;
}
</style>

<?php include '../includes/footer.php'; ?>