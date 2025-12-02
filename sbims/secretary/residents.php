<?php
$page_title = "Resident Records";
require_once '../config/auth.php';
require_once '../config/connection.php';
require_once '../config/functions.php';

Auth::checkAuth();
Auth::checkRole(['secretary']);

$database = new Database();
$db = $database->getConnection();

// Search and filter
$search = $_GET['search'] ?? '';
$purok = $_GET['purok'] ?? '';

$query = "SELECT * FROM residents WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND (first_name LIKE :search OR last_name LIKE :search OR resident_id LIKE :search)";
    $params[':search'] = "%$search%";
}

if (!empty($purok)) {
    $query .= " AND purok = :purok";
    $params[':purok'] = $purok;
}

$query .= " ORDER BY created_at DESC";

$stmt = $db->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$residents = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get unique puroks for filter
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
                <h1>Resident Records</h1>
                <p>Manage barangay resident information</p>
            </div>
            <div class="page-actions">
                <a href="residents_add.php" class="btn btn-primary">Add New Resident</a>
            </div>
        </div>

        <!-- Search and Filter -->
        <div class="data-table">
            <div class="table-header">
                <h3>Resident List</h3>
                <div class="table-actions">
                    <form method="GET" class="search-form">
                        <select name="purok" onchange="this.form.submit()">
                            <option value="">All Purok</option>
                            <?php foreach ($puroks as $p): ?>
                                <option value="<?php echo $p; ?>" <?php echo $purok == $p ? 'selected' : ''; ?>>
                                    <?php echo $p; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="text" name="search" placeholder="Search residents..." value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit" class="btn btn-outline">Search</button>
                        <a href="residents.php" class="btn btn-outline">Reset</a>
                    </form>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Resident ID</th>
                        <th>Full Name</th>
                        <th>Contact</th>
                        <th>Purok</th>
                        <th>Household No.</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($residents) > 0): ?>
                        <?php foreach ($residents as $resident): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($resident['resident_id']); ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($resident['first_name'] . ' ' . $resident['last_name']); ?></strong><br>
                                <small><?php echo $resident['gender'] . ', ' . calculateAge($resident['birthdate']) . ' years old'; ?></small>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($resident['contact_number']); ?><br>
                                <small><?php echo htmlspecialchars($resident['email']); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($resident['purok']); ?></td>
                            <td><?php echo htmlspecialchars($resident['household_number']); ?></td>
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
                            <td>
                                <a href="residents_edit.php?id=<?php echo $resident['id']; ?>" class="btn btn-sm btn-outline">Edit</a>
                                <a href="residents_delete.php?id=<?php echo $resident['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; color: #64748b;">
                                No residents found. <a href="residents_add.php">Add the first resident</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>