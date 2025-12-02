<?php
$page_title = "Barangay Information";
require_once '../config/auth.php';
require_once '../config/connection.php';

Auth::checkAuth();
Auth::checkRole(['admin']);

$database = new Database();
$db = $database->getConnection();

// Get barangay info
$query = "SELECT * FROM barangay_info LIMIT 1";
$stmt = $db->prepare($query);
$stmt->execute();
$barangay_info = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$barangay_info) {
    // Insert default if not exists
    $insert_query = "INSERT INTO barangay_info (barangay_name, barangay_captain, barangay_secretary, address, contact_number, email) 
                    VALUES ('Libertad', '', '', 'Libertad, Isabel, Leyte', '', '')";
    $db->exec($insert_query);
    $barangay_info = ['barangay_name' => 'Libertad', 'barangay_captain' => '', 'barangay_secretary' => '', 'address' => 'Libertad, Isabel, Leyte', 'contact_number' => '', 'email' => ''];
}

// Update if form submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $barangay_name = $_POST['barangay_name'];
    $barangay_captain = $_POST['barangay_captain'];
    $barangay_secretary = $_POST['barangay_secretary'];
    $address = $_POST['address'];
    $contact_number = $_POST['contact_number'];
    $email = $_POST['email'];
    
    $update_query = "UPDATE barangay_info SET 
                    barangay_name = :barangay_name,
                    barangay_captain = :barangay_captain,
                    barangay_secretary = :barangay_secretary,
                    address = :address,
                    contact_number = :contact_number,
                    email = :email";
    
    $stmt = $db->prepare($update_query);
    $stmt->bindParam(':barangay_name', $barangay_name);
    $stmt->bindParam(':barangay_captain', $barangay_captain);
    $stmt->bindParam(':barangay_secretary', $barangay_secretary);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':contact_number', $contact_number);
    $stmt->bindParam(':email', $email);
    
    if ($stmt->execute()) {
        $success = "Barangay information updated successfully!";
        $barangay_info = compact('barangay_name', 'barangay_captain', 'barangay_secretary', 'address', 'contact_number', 'email');
    } else {
        $error = "Failed to update barangay information.";
    }
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/topbar.php'; ?>

<div class="main-container">
    <?php include '../includes/sidebar.php'; ?>
    
    <main class="content">
        <div class="page-header">
            <h1>Barangay Information</h1>
            <p>Update barangay details and officials</p>
        </div>

        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="form-container">
            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label for="barangay_name">Barangay Name *</label>
                        <input type="text" id="barangay_name" name="barangay_name" value="<?php echo htmlspecialchars($barangay_info['barangay_name']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="barangay_captain">Barangay Captain</label>
                        <input type="text" id="barangay_captain" name="barangay_captain" value="<?php echo htmlspecialchars($barangay_info['barangay_captain']); ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="barangay_secretary">Barangay Secretary</label>
                        <input type="text" id="barangay_secretary" name="barangay_secretary" value="<?php echo htmlspecialchars($barangay_info['barangay_secretary']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="contact_number">Contact Number</label>
                        <input type="text" id="contact_number" name="contact_number" value="<?php echo htmlspecialchars($barangay_info['contact_number']); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="address">Complete Address *</label>
                    <textarea id="address" name="address" rows="3" required><?php echo htmlspecialchars($barangay_info['address']); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($barangay_info['email']); ?>">
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Update Information</button>
                    <button type="reset" class="btn btn-outline">Reset</button>
                </div>
            </form>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>