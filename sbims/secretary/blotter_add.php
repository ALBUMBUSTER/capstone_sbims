<?php
$page_title = "Add Blotter Case";
require_once '../config/auth.php';
require_once '../config/connection.php';
require_once '../config/functions.php';

Auth::checkAuth();
Auth::checkRole(['secretary']);

$database = new Database();
$db = $database->getConnection();

// Generate case ID
$case_id = generateCaseID($db);

// Get residents for complainant selection
$residents_query = "SELECT id, resident_id, first_name, last_name FROM residents ORDER BY first_name, last_name";
$residents_stmt = $db->prepare($residents_query);
$residents_stmt->execute();
$residents = $residents_stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $complainant_id = $_POST['complainant_id'];
    $respondent_name = sanitizeInput($_POST['respondent_name']);
    $respondent_address = sanitizeInput($_POST['respondent_address']);
    $incident_type = sanitizeInput($_POST['incident_type']);
    $incident_date = $_POST['incident_date'];
    $incident_time = $_POST['incident_time'];
    $incident_location = sanitizeInput($_POST['incident_location']);
    $description = sanitizeInput($_POST['description']);
    
    // Combine date and time
    $incident_datetime = $incident_date . ' ' . $incident_time . ':00';
    $handled_by = $_SESSION['user_id'];

    $query = "INSERT INTO blotters (case_id, complainant_id, respondent_name, respondent_address, incident_type, incident_date, incident_location, description, handled_by) 
              VALUES (:case_id, :complainant_id, :respondent_name, :respondent_address, :incident_type, :incident_date, :incident_location, :description, :handled_by)";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':case_id', $case_id);
    $stmt->bindParam(':complainant_id', $complainant_id);
    $stmt->bindParam(':respondent_name', $respondent_name);
    $stmt->bindParam(':respondent_address', $respondent_address);
    $stmt->bindParam(':incident_type', $incident_type);
    $stmt->bindParam(':incident_date', $incident_datetime);
    $stmt->bindParam(':incident_location', $incident_location);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':handled_by', $handled_by);

    if ($stmt->execute()) {
        // Log activity
        Auth::logActivity($_SESSION['user_id'], 'Add Blotter', "Added new blotter case: $case_id");
        
        $_SESSION['success'] = "Blotter case added successfully!";
        header("Location: blotter.php");
        exit();
    } else {
        $error = "Failed to add blotter case. Please try again.";
    }
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/topbar.php'; ?>

<div class="main-container">
    <?php include '../includes/sidebar.php'; ?>
    
    <main class="content">
        <div class="page-header">
            <h1>Add Blotter Case</h1>
            <p>Record new barangay dispute or incident</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="form-container">
            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label for="case_id">Case ID</label>
                        <input type="text" id="case_id" value="<?php echo $case_id; ?>" readonly style="background-color: #f3f4f6;">
                        <small>Automatically generated</small>
                    </div>
                </div>

                <h3>Complainant Information</h3>
                <div class="form-group">
                    <label for="complainant_id">Select Complainant *</label>
                    <select id="complainant_id" name="complainant_id" required>
                        <option value="">Select Complainant</option>
                        <?php foreach ($residents as $resident): ?>
                            <option value="<?php echo $resident['id']; ?>">
                                <?php echo htmlspecialchars($resident['first_name'] . ' ' . $resident['last_name'] . ' (' . $resident['resident_id'] . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <h3>Respondent Information</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="respondent_name">Respondent Name *</label>
                        <input type="text" id="respondent_name" name="respondent_name" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="respondent_address">Respondent Address</label>
                    <textarea id="respondent_address" name="respondent_address" rows="2" placeholder="Complete address of the respondent"></textarea>
                </div>

                <h3>Incident Details</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="incident_type">Incident Type *</label>
                        <select id="incident_type" name="incident_type" required>
                            <option value="">Select Type</option>
                            <option value="Boundary Dispute">Boundary Dispute</option>
                            <option value="Noise Complaint">Noise Complaint</option>
                            <option value="Property Damage">Property Damage</option>
                            <option value="Physical Altercation">Physical Altercation</option>
                            <option value="Theft">Theft</option>
                            <option value="Trespassing">Trespassing</option>
                            <option value="Verbal Argument">Verbal Argument</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="incident_date">Incident Date *</label>
                        <input type="date" id="incident_date" name="incident_date" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="incident_time">Incident Time *</label>
                        <input type="time" id="incident_time" name="incident_time" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="incident_location">Incident Location *</label>
                    <textarea id="incident_location" name="incident_location" rows="2" placeholder="Exact location where the incident occurred" required></textarea>
                </div>

                <div class="form-group">
                    <label for="description">Case Description *</label>
                    <textarea id="description" name="description" rows="5" placeholder="Detailed description of the incident, including what happened, who was involved, and any witnesses..." required></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Add Blotter Case</button>
                    <a href="blotter.php" class="btn btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </main>
</div>

<script>
// Set default date and time to current
document.addEventListener('DOMContentLoaded', function() {
    const now = new Date();
    const date = now.toISOString().split('T')[0];
    const time = now.toTimeString().split(':').slice(0, 2).join(':');
    
    document.getElementById('incident_date').value = date;
    document.getElementById('incident_time').value = time;
});
</script>

<?php include '../includes/footer.php'; ?>