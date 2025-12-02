<?php
$page_title = "Add New Resident";
require_once '../config/auth.php';
require_once '../config/connection.php';
require_once '../config/functions.php';

Auth::checkAuth();
Auth::checkRole(['secretary']);

$database = new Database();
$db = $database->getConnection();

// Generate resident ID
$year = date('Y');
$query = "SELECT COUNT(*) as count FROM residents WHERE YEAR(created_at) = :year";
$stmt = $db->prepare($query);
$stmt->bindParam(':year', $year);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$sequence = $result['count'] + 1;
$resident_id = "RES-$year-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = sanitizeInput($_POST['first_name']);
    $middle_name = sanitizeInput($_POST['middle_name']);
    $last_name = sanitizeInput($_POST['last_name']);
    $birthdate = $_POST['birthdate'];
    $gender = $_POST['gender'];
    $civil_status = $_POST['civil_status'];
    $contact_number = sanitizeInput($_POST['contact_number']);
    $email = sanitizeInput($_POST['email']);
    $address = sanitizeInput($_POST['address']);
    $purok = $_POST['purok'];
    $household_number = sanitizeInput($_POST['household_number']);
    $is_voter = isset($_POST['is_voter']) ? 1 : 0;
    $is_4ps = isset($_POST['is_4ps']) ? 1 : 0;
    $is_senior = isset($_POST['is_senior']) ? 1 : 0;
    $is_pwd = isset($_POST['is_pwd']) ? 1 : 0;

    $query = "INSERT INTO residents (resident_id, first_name, middle_name, last_name, birthdate, gender, civil_status, contact_number, email, address, purok, household_number, is_voter, is_4ps, is_senior, is_pwd) 
              VALUES (:resident_id, :first_name, :middle_name, :last_name, :birthdate, :gender, :civil_status, :contact_number, :email, :address, :purok, :household_number, :is_voter, :is_4ps, :is_senior, :is_pwd)";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':resident_id', $resident_id);
    $stmt->bindParam(':first_name', $first_name);
    $stmt->bindParam(':middle_name', $middle_name);
    $stmt->bindParam(':last_name', $last_name);
    $stmt->bindParam(':birthdate', $birthdate);
    $stmt->bindParam(':gender', $gender);
    $stmt->bindParam(':civil_status', $civil_status);
    $stmt->bindParam(':contact_number', $contact_number);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':purok', $purok);
    $stmt->bindParam(':household_number', $household_number);
    $stmt->bindParam(':is_voter', $is_voter);
    $stmt->bindParam(':is_4ps', $is_4ps);
    $stmt->bindParam(':is_senior', $is_senior);
    $stmt->bindParam(':is_pwd', $is_pwd);

    if ($stmt->execute()) {
        // Log activity
        Auth::logActivity($_SESSION['user_id'], 'Add Resident', "Added new resident: $first_name $last_name");
        
        $_SESSION['success'] = "Resident added successfully!";
        header("Location: residents.php");
        exit();
    } else {
        $error = "Failed to add resident. Please try again.";
    }
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/topbar.php'; ?>

<div class="main-container">
    <?php include '../includes/sidebar.php'; ?>
    
    <main class="content">
        <div class="page-header">
            <h1>Add New Resident</h1>
            <p>Register a new barangay resident</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="form-container">
            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label for="resident_id">Resident ID</label>
                        <input type="text" id="resident_id" value="<?php echo $resident_id; ?>" readonly style="background-color: #f3f4f6;">
                        <small>Automatically generated</small>
                    </div>
                </div>

                <h3>Personal Information</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name *</label>
                        <input type="text" id="first_name" name="first_name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="middle_name">Middle Name</label>
                        <input type="text" id="middle_name" name="middle_name">
                    </div>
                    
                    <div class="form-group">
                        <label for="last_name">Last Name *</label>
                        <input type="text" id="last_name" name="last_name" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="birthdate">Birthdate *</label>
                        <input type="date" id="birthdate" name="birthdate" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="gender">Gender *</label>
                        <select id="gender" name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="civil_status">Civil Status *</label>
                        <select id="civil_status" name="civil_status" required>
                            <option value="">Select Status</option>
                            <option value="Single">Single</option>
                            <option value="Married">Married</option>
                            <option value="Widowed">Widowed</option>
                            <option value="Divorced">Divorced</option>
                        </select>
                    </div>
                </div>

                <h3>Contact Information</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="contact_number">Contact Number</label>
                        <input type="text" id="contact_number" name="contact_number" placeholder="09123456789">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email">
                    </div>
                </div>

                <div class="form-group">
                    <label for="address">Complete Address *</label>
                    <textarea id="address" name="address" rows="3" placeholder="Purok 1, Libertad, Isabel, Leyte" required></textarea>
                </div>

                <h3>Barangay Information</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="purok">Purok *</label>
                        <select id="purok" name="purok" required>
                            <option value="">Select Purok</option>
                            <option value="Purok 1">Purok 1</option>
                            <option value="Purok 2">Purok 2</option>
                            <option value="Purok 3">Purok 3</option>
                            <option value="Purok 4">Purok 4</option>
                            <option value="Purok 5">Purok 5</option>
                            <option value="Purok 6">Purok 6</option>
                            <option value="Purok 7">Purok 7</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="household_number">Household Number</label>
                        <input type="text" id="household_number" name="household_number" placeholder="HH-001">
                    </div>
                </div>

                <h3>Additional Information</h3>
                <div class="form-row">
                    <div class="form-group" style="display: flex; align-items: center; gap: 0.5rem;">
                        <input type="checkbox" id="is_voter" name="is_voter" value="1">
                        <label for="is_voter" style="margin: 0;">Registered Voter</label>
                    </div>
                    
                    <div class="form-group" style="display: flex; align-items: center; gap: 0.5rem;">
                        <input type="checkbox" id="is_4ps" name="is_4ps" value="1">
                        <label for="is_4ps" style="margin: 0;">4PS Beneficiary</label>
                    </div>
                    
                    <div class="form-group" style="display: flex; align-items: center; gap: 0.5rem;">
                        <input type="checkbox" id="is_senior" name="is_senior" value="1">
                        <label for="is_senior" style="margin: 0;">Senior Citizen</label>
                    </div>
                    
                    <div class="form-group" style="display: flex; align-items: center; gap: 0.5rem;">
                        <input type="checkbox" id="is_pwd" name="is_pwd" value="1">
                        <label for="is_pwd" style="margin: 0;">Person with Disability</label>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Add Resident</button>
                    <a href="residents.php" class="btn btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>