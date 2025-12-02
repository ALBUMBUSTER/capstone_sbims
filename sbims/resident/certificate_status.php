<?php
$page_title = "My Certificates";
require_once '../config/auth.php';
require_once '../config/connection.php';
require_once '../config/functions.php';

Auth::checkAuth();
Auth::checkRole(['resident']);

$database = new Database();
$db = $database->getConnection();

// Get resident ID from session (you might need to store resident_id in session during login)
// For now, we'll use a placeholder - in real implementation, link user to resident record
$resident_user_id = $_SESSION['user_id'];

// Get resident details
$resident_query = "SELECT r.* FROM residents r WHERE r.id = :resident_id";
$resident_stmt = $db->prepare($resident_query);
$resident_stmt->bindParam(':resident_id', $resident_user_id); // This needs proper linking
$resident_stmt->execute();
$resident = $resident_stmt->fetch(PDO::FETCH_ASSOC);

// If no direct link, get by name (temporary solution)
if (!$resident) {
    // Try to find resident by name (this is a temporary workaround)
    $name_parts = explode(' ', $_SESSION['full_name']);
    $first_name = $name_parts[0];
    $last_name = end($name_parts);
    
    $resident_query = "SELECT r.* FROM residents r WHERE r.first_name LIKE :first_name AND r.last_name LIKE :last_name LIMIT 1";
    $resident_stmt = $db->prepare($resident_query);
    $resident_stmt->bindValue(':first_name', $first_name . '%');
    $resident_stmt->bindValue(':last_name', $last_name . '%');
    $resident_stmt->execute();
    $resident = $resident_stmt->fetch(PDO::FETCH_ASSOC);
}

if ($resident) {
    $resident_id = $resident['id'];
    
    // Get certificates for this resident
    $certificates_query = "SELECT c.*, u1.full_name as issued_by_name, u2.full_name as approved_by_name
                          FROM certificates c 
                          LEFT JOIN users u1 ON c.issued_by = u1.id 
                          LEFT JOIN users u2 ON c.approved_by = u2.id 
                          WHERE c.resident_id = :resident_id 
                          ORDER BY c.created_at DESC";
    $certificates_stmt = $db->prepare($certificates_query);
    $certificates_stmt->bindParam(':resident_id', $resident_id);
    $certificates_stmt->execute();
    $certificates = $certificates_stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $certificates = [];
    $error = "Resident record not found. Please contact barangay secretary to link your account.";
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/topbar.php'; ?>

<div class="main-container">
    <?php include '../includes/sidebar.php'; ?>
    
    <main class="content">
        <div class="page-header">
            <div class="page-title">
                <h1>My Certificate Requests</h1>
                <p>Track the status of your barangay certificate requests</p>
            </div>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($resident): ?>
        <!-- Resident Information -->
        <div class="info-card">
            <h3>My Information</h3>
            <div class="info-grid">
                <div class="info-item">
                    <label>Name:</label>
                    <span><?php echo htmlspecialchars($resident['first_name'] . ' ' . $resident['last_name']); ?></span>
                </div>
                <div class="info-item">
                    <label>Resident ID:</label>
                    <span><?php echo htmlspecialchars($resident['resident_id']); ?></span>
                </div>
                <div class="info-item">
                    <label>Address:</label>
                    <span><?php echo htmlspecialchars($resident['address']); ?></span>
                </div>
                <div class="info-item">
                    <label>Purok:</label>
                    <span><?php echo htmlspecialchars($resident['purok']); ?></span>
                </div>
            </div>
        </div>

        <!-- Certificate Requests -->
        <div class="data-table">
            <div class="table-header">
                <h3>Certificate Request History</h3>
                <div class="table-actions">
                    <span class="badge"><?php echo count($certificates); ?> requests</span>
                </div>
            </div>

            <?php if (count($certificates) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Certificate ID</th>
                            <th>Type</th>
                            <th>Purpose</th>
                            <th>Date Requested</th>
                            <th>Status</th>
                            <th>Approved By</th>
                            <th>Date Issued</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($certificates as $cert): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($cert['certificate_id']); ?></td>
                            <td>
                                <span class="certificate-type">
                                    <?php echo htmlspecialchars($cert['certificate_type']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($cert['purpose']); ?></td>
                            <td><?php echo formatDate($cert['created_at']); ?></td>
                            <td>
                                <span class="status status-<?php echo strtolower($cert['status']); ?>">
                                    <?php echo $cert['status']; ?>
                                </span>
                                <?php if ($cert['status'] == 'Pending'): ?>
                                    <br><small>Awaiting approval</small>
                                <?php elseif ($cert['status'] == 'Approved'): ?>
                                    <br><small>Ready for release</small>
                                <?php elseif ($cert['status'] == 'Released'): ?>
                                    <br><small>Certificate released</small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo $cert['approved_by_name'] ? htmlspecialchars($cert['approved_by_name']) : 'Pending'; ?>
                            </td>
                            <td>
                                <?php echo $cert['issued_date'] ? formatDate($cert['issued_date']) : '-'; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-data">
                    <div class="no-data-icon">📄</div>
                    <h3>No Certificate Requests</h3>
                    <p>You haven't requested any barangay certificates yet.</p>
                    <p>Visit the barangay hall to request certificates for clearance, indigency, or residency.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Status Legend -->
        <div class="info-box">
            <h4>📋 Certificate Status Guide</h4>
            <div class="status-legend">
                <div class="legend-item">
                    <span class="status status-pending">Pending</span>
                    <span> - Under review by barangay officials</span>
                </div>
                <div class="legend-item">
                    <span class="status status-approved">Approved</span>
                    <span> - Approved and ready for release</span>
                </div>
                <div class="legend-item">
                    <span class="status status-released">Released</span>
                    <span> - Certificate has been issued to you</span>
                </div>
            </div>
            <p><small>For questions about your certificate requests, please visit the barangay hall or contact the secretary.</small></p>
        </div>

        <?php else: ?>
        <!-- No Resident Record Found -->
        <div class="no-data">
            <div class="no-data-icon">👤</div>
            <h3>Account Not Linked</h3>
            <p>Your user account is not yet linked to a resident record in the system.</p>
            <p>Please visit the barangay hall to have your account properly set up.</p>
            <div class="contact-info">
                <p><strong>Barangay Libertad Office</strong></p>
                <p>📍 Libertad, Isabel, Leyte</p>
                <p>📞 Contact the barangay secretary for assistance</p>
            </div>
        </div>
        <?php endif; ?>
    </main>
</div>

<style>
.info-card {
    background: white;
    border-radius: 10px;
    padding: 1.5rem;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    margin-bottom: 2rem;
}

.info-card h3 {
    color: var(--dark);
    margin-bottom: 1rem;
    font-size: 1.2rem;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
}

.info-item label {
    font-weight: 600;
    color: var(--secondary);
    font-size: 0.9rem;
}

.info-item span {
    color: var(--dark);
}

.certificate-type {
    background: #e0f2fe;
    color: #0369a1;
    padding: 0.2rem 0.5rem;
    border-radius: 4px;
    font-size: 0.8rem;
    font-weight: 500;
}

.status-legend {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin: 1rem 0;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.contact-info {
    background: #f0f9ff;
    border: 1px solid #7dd3fc;
    border-radius: 8px;
    padding: 1rem;
    margin-top: 1rem;
}

.contact-info p {
    margin: 0.3rem 0;
}

.status-released {
    background: #dcfce7;
    color: #166534;
}
</style>

<?php include '../includes/footer.php'; ?>