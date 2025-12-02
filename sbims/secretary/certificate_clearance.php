<?php
$page_title = "Barangay Clearance";
require_once '../config/auth.php';
require_once '../config/connection.php';
require_once '../config/functions.php';

Auth::checkAuth();
Auth::checkRole(['secretary']);

$database = new Database();
$db = $database->getConnection();

// Get residents for selection
$residents_query = "SELECT id, resident_id, first_name, last_name, address, purok FROM residents ORDER BY first_name, last_name";
$residents_stmt = $db->prepare($residents_query);
$residents_stmt->execute();
$residents = $residents_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get barangay info
$barangay_query = "SELECT * FROM barangay_info LIMIT 1";
$barangay_stmt = $db->prepare($barangay_query);
$barangay_stmt->execute();
$barangay_info = $barangay_stmt->fetch(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $resident_id = $_POST['resident_id'];
    $purpose = sanitizeInput($_POST['purpose']);
    $or_number = sanitizeInput($_POST['or_number']);
    $amount_paid = $_POST['amount_paid'];
    
    // Generate certificate ID
    $certificate_id = generateCertificateID($db, 'Clearance');
    $issued_by = $_SESSION['user_id'];
    
    $query = "INSERT INTO certificates (certificate_id, resident_id, certificate_type, purpose, or_number, amount_paid, issued_by, status) 
              VALUES (:certificate_id, :resident_id, 'Clearance', :purpose, :or_number, :amount_paid, :issued_by, 'Pending')";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':certificate_id', $certificate_id);
    $stmt->bindParam(':resident_id', $resident_id);
    $stmt->bindParam(':purpose', $purpose);
    $stmt->bindParam(':or_number', $or_number);
    $stmt->bindParam(':amount_paid', $amount_paid);
    $stmt->bindParam(':issued_by', $issued_by);
    
    if ($stmt->execute()) {
        // Log activity
        Auth::logActivity($_SESSION['user_id'], 'Issue Certificate', "Issued Barangay Clearance: $certificate_id");
        
        $_SESSION['success'] = "Barangay Clearance generated successfully!";
        header("Location: certificates.php");
        exit();
    } else {
        $error = "Failed to generate certificate. Please try again.";
    }
}

// If editing existing certificate
$edit_certificate = null;
if (isset($_GET['id'])) {
    $cert_id = $_GET['id'];
    $edit_query = "SELECT c.*, r.first_name, r.last_name, r.address, r.purok, r.birthdate, r.civil_status 
                   FROM certificates c 
                   JOIN residents r ON c.resident_id = r.id 
                   WHERE c.id = :id";
    $edit_stmt = $db->prepare($edit_query);
    $edit_stmt->bindParam(':id', $cert_id);
    $edit_stmt->execute();
    $edit_certificate = $edit_stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/topbar.php'; ?>

<div class="main-container">
    <?php include '../includes/sidebar.php'; ?>
    
    <main class="content">
        <div class="page-header">
            <h1>Barangay Clearance</h1>
            <p>Issue barangay clearance certificate</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="form-container">
            <form method="POST">
                <h3>Resident Information</h3>
                <div class="form-group">
                    <label for="resident_id">Select Resident *</label>
                    <select id="resident_id" name="resident_id" required <?php echo $edit_certificate ? 'disabled' : ''; ?>>
                        <option value="">Select Resident</option>
                        <?php foreach ($residents as $resident): ?>
                            <option value="<?php echo $resident['id']; ?>" 
                                <?php echo $edit_certificate && $edit_certificate['resident_id'] == $resident['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($resident['first_name'] . ' ' . $resident['last_name'] . ' - ' . $resident['purok']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($edit_certificate): ?>
                        <input type="hidden" name="resident_id" value="<?php echo $edit_certificate['resident_id']; ?>">
                    <?php endif; ?>
                </div>

                <?php if ($edit_certificate): ?>
                <div class="resident-details">
                    <h4>Resident Details:</h4>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <label>Name:</label>
                            <span><?php echo htmlspecialchars($edit_certificate['first_name'] . ' ' . $edit_certificate['last_name']); ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Address:</label>
                            <span><?php echo htmlspecialchars($edit_certificate['address']); ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Purok:</label>
                            <span><?php echo htmlspecialchars($edit_certificate['purok']); ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Civil Status:</label>
                            <span><?php echo htmlspecialchars($edit_certificate['civil_status']); ?></span>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <h3>Certificate Details</h3>
                <div class="form-group">
                    <label for="purpose">Purpose *</label>
                    <textarea id="purpose" name="purpose" rows="3" placeholder="State the purpose for this barangay clearance..." required><?php echo $edit_certificate ? htmlspecialchars($edit_certificate['purpose']) : ''; ?></textarea>
                </div>

                <h3>Payment Information</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="or_number">OR Number</label>
                        <input type="text" id="or_number" name="or_number" value="<?php echo $edit_certificate ? htmlspecialchars($edit_certificate['or_number'] ?? '') : ''; ?>" placeholder="Optional">
                    </div>
                    
                    <div class="form-group">
                        <label for="amount_paid">Amount Paid</label>
                        <input type="number" id="amount_paid" name="amount_paid" step="0.01" value="<?php echo $edit_certificate ? htmlspecialchars($edit_certificate['amount_paid'] ?? '0') : '0'; ?>" placeholder="0.00">
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <?php echo $edit_certificate ? 'Update Certificate' : 'Generate Certificate'; ?>
                    </button>
                    <a href="certificates.php" class="btn btn-outline">Cancel</a>
                    
                    <?php if ($edit_certificate && $edit_certificate['status'] == 'Approved'): ?>
                        <button type="button" class="btn btn-success" onclick="printCertificate()">Print Certificate</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Certificate Preview -->
        <?php if ($edit_certificate): ?>
        <div class="certificate-preview" id="certificatePreview">
            <div class="certificate-header">
                <div class="barangay-logo">BL</div>
                <div class="barangay-info">
                    <h2>BARANGAY CLEARANCE</h2>
                    <p>Republic of the Philippines</p>
                    <p>Province of Leyte</p>
                    <p>Municipality of Isabel</p>
                    <p><strong>BARANGAY LIBERTAD</strong></p>
                </div>
            </div>

            <div class="certificate-body">
                <p class="to-whom">TO WHOM IT MAY CONCERN:</p>
                
                <p class="certificate-text">
                    This is to certify that <strong><?php echo htmlspecialchars($edit_certificate['first_name'] . ' ' . $edit_certificate['last_name']); ?></strong>, 
                    of legal age, <strong><?php echo htmlspecialchars($edit_certificate['civil_status']); ?></strong>, 
                    and a resident of <strong><?php echo htmlspecialchars($edit_certificate['address']); ?></strong>, 
                    Purok <strong><?php echo htmlspecialchars($edit_certificate['purok']); ?></strong>, Barangay Libertad, Isabel, Leyte, 
                    is known to me to be a person of good moral character and a law-abiding citizen.
                </p>
                
                <p class="certificate-text">
                    This certification is issued upon the request of the above-mentioned person for <strong><?php echo htmlspecialchars($edit_certificate['purpose']); ?></strong>.
                </p>
                
                <p class="certificate-text">
                    Issued this <strong><?php echo date('jS'); ?></strong> day of <strong><?php echo date('F Y'); ?></strong> at Barangay Libertad, Isabel, Leyte.
                </p>
            </div>

            <div class="certificate-footer">
                <div class="signature-area">
                    <div class="signature-line"></div>
                    <p><strong><?php echo $barangay_info['barangay_captain'] ? htmlspecialchars($barangay_info['barangay_captain']) : 'BARANGAY CAPTAIN'; ?></strong></p>
                    <p>Punong Barangay</p>
                </div>
                
                <div class="certificate-number">
                    <p>Certificate ID: <strong><?php echo htmlspecialchars($edit_certificate['certificate_id']); ?></strong></p>
                    <?php if ($edit_certificate['or_number']): ?>
                        <p>OR Number: <strong><?php echo htmlspecialchars($edit_certificate['or_number']); ?></strong></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </main>
</div>

<style>
.certificate-preview {
    background: white;
    border: 2px solid #1e40af;
    padding: 2rem;
    margin-top: 2rem;
    font-family: 'Times New Roman', serif;
}

.certificate-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 2rem;
    text-align: center;
    border-bottom: 2px solid #1e40af;
    padding-bottom: 1rem;
}

.barangay-logo {
    width: 80px;
    height: 80px;
    background: #1e40af;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: bold;
}

.barangay-info h2 {
    color: #1e40af;
    margin: 0;
    font-size: 1.5rem;
}

.barangay-info p {
    margin: 0.2rem 0;
    font-size: 0.9rem;
}

.to-whom {
    font-weight: bold;
    margin-bottom: 1.5rem;
}

.certificate-text {
    text-align: justify;
    line-height: 1.6;
    margin-bottom: 1rem;
    text-indent: 2rem;
}

.certificate-footer {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-top: 3rem;
}

.signature-area {
    text-align: center;
}

.signature-line {
    width: 200px;
    border-bottom: 1px solid #000;
    margin-bottom: 0.5rem;
}

.resident-details {
    background: #f8fafc;
    padding: 1rem;
    border-radius: 5px;
    margin-bottom: 1rem;
}

.resident-details h4 {
    margin-bottom: 1rem;
    color: var(--dark);
}
</style>

<script>
function printCertificate() {
    var printContent = document.getElementById('certificatePreview').innerHTML;
    var originalContent = document.body.innerHTML;
    
    document.body.innerHTML = printContent;
    window.print();
    document.body.innerHTML = originalContent;
    window.location.reload();
}
</script>

<?php include '../includes/footer.php'; ?>