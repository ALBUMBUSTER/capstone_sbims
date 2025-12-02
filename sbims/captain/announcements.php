<?php
$page_title = "Announcements";
require_once '../config/auth.php';
require_once '../config/connection.php';
require_once '../config/functions.php';

Auth::checkAuth();
Auth::checkRole(['captain']);

$database = new Database();
$db = $database->getConnection();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = sanitizeInput($_POST['title']);
    $content = sanitizeInput($_POST['content']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $posted_by = $_SESSION['user_id'];

    $query = "INSERT INTO announcements (title, content, posted_by, is_active) 
              VALUES (:title, :content, :posted_by, :is_active)";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':content', $content);
    $stmt->bindParam(':posted_by', $posted_by);
    $stmt->bindParam(':is_active', $is_active);

    if ($stmt->execute()) {
        Auth::logActivity($_SESSION['user_id'], 'Create Announcement', "Created announcement: $title");
        $_SESSION['success'] = "Announcement published successfully!";
        header("Location: announcements.php");
        exit();
    } else {
        $error = "Failed to publish announcement. Please try again.";
    }
}

// Get all announcements
$query = "SELECT a.*, u.full_name as posted_by_name 
          FROM announcements a 
          JOIN users u ON a.posted_by = u.id 
          ORDER BY a.created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/topbar.php'; ?>

<div class="main-container">
    <?php include '../includes/sidebar.php'; ?>
    
    <main class="content">
        <div class="page-header">
            <div class="page-title">
                <h1>Announcements</h1>
                <p>Manage barangay announcements and public advisories</p>
            </div>
        </div>

        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Create Announcement Form -->
        <div class="form-container">
            <h3>Create New Announcement</h3>
            <form method="POST">
                <div class="form-group">
                    <label for="title">Announcement Title *</label>
                    <input type="text" id="title" name="title" placeholder="Enter announcement title" required>
                </div>

                <div class="form-group">
                    <label for="content">Announcement Content *</label>
                    <textarea id="content" name="content" rows="6" placeholder="Enter announcement details..." required></textarea>
                </div>

                <div class="form-group" style="display: flex; align-items: center; gap: 0.5rem;">
                    <input type="checkbox" id="is_active" name="is_active" value="1" checked>
                    <label for="is_active" style="margin: 0;">Publish immediately</label>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Publish Announcement</button>
                    <button type="reset" class="btn btn-outline">Clear</button>
                </div>
            </form>
        </div>

        <!-- Announcements List -->
        <div class="data-table">
            <div class="table-header">
                <h3>Recent Announcements</h3>
                <div class="table-actions">
                    <span class="badge"><?php echo count($announcements); ?> total</span>
                </div>
            </div>

            <?php if (count($announcements) > 0): ?>
                <div class="announcements-list">
                    <?php foreach ($announcements as $announcement): ?>
                    <div class="announcement-card <?php echo !$announcement['is_active'] ? 'inactive' : ''; ?>">
                        <div class="announcement-header">
                            <h4><?php echo htmlspecialchars($announcement['title']); ?></h4>
                            <div class="announcement-meta">
                                <span class="status <?php echo $announcement['is_active'] ? 'status-approved' : 'status-pending'; ?>">
                                    <?php echo $announcement['is_active'] ? 'Published' : 'Draft'; ?>
                                </span>
                                <span>•</span>
                                <span>Posted by: <?php echo htmlspecialchars($announcement['posted_by_name']); ?></span>
                                <span>•</span>
                                <span><?php echo formatDateTime($announcement['created_at']); ?></span>
                            </div>
                        </div>
                        <div class="announcement-content">
                            <?php echo nl2br(htmlspecialchars($announcement['content'])); ?>
                        </div>
                        <div class="announcement-actions">
                            <a href="announcements_edit.php?id=<?php echo $announcement['id']; ?>" class="btn btn-sm btn-outline">Edit</a>
                            <?php if ($announcement['is_active']): ?>
                                <a href="announcements.php?deactivate=<?php echo $announcement['id']; ?>" class="btn btn-sm btn-warning">Unpublish</a>
                            <?php else: ?>
                                <a href="announcements.php?activate=<?php echo $announcement['id']; ?>" class="btn btn-sm btn-success">Publish</a>
                            <?php endif; ?>
                            <a href="announcements.php?delete=<?php echo $announcement['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this announcement?')">Delete</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-data">
                    <p>No announcements yet. Create the first announcement above.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<style>
.announcements-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.announcement-card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 1.5rem;
    transition: all 0.3s;
}

.announcement-card.inactive {
    background: #f8fafc;
    opacity: 0.7;
}

.announcement-card:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.announcement-header {
    margin-bottom: 1rem;
}

.announcement-header h4 {
    color: var(--dark);
    margin: 0 0 0.5rem 0;
    font-size: 1.2rem;
}

.announcement-meta {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    color: var(--secondary);
    flex-wrap: wrap;
}

.announcement-content {
    line-height: 1.6;
    color: var(--dark);
    margin-bottom: 1rem;
}

.announcement-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}
</style>

<?php
// Handle activation/deactivation and deletion
if (isset($_GET['activate'])) {
    $announcement_id = $_GET['activate'];
    $update_query = "UPDATE announcements SET is_active = 1 WHERE id = :id";
    $update_stmt = $db->prepare($update_query);
    $update_stmt->bindParam(':id', $announcement_id);
    
    if ($update_stmt->execute()) {
        $_SESSION['success'] = "Announcement published!";
        header("Location: announcements.php");
        exit();
    }
}

if (isset($_GET['deactivate'])) {
    $announcement_id = $_GET['deactivate'];
    $update_query = "UPDATE announcements SET is_active = 0 WHERE id = :id";
    $update_stmt = $db->prepare($update_query);
    $update_stmt->bindParam(':id', $announcement_id);
    
    if ($update_stmt->execute()) {
        $_SESSION['success'] = "Announcement unpublished!";
        header("Location: announcements.php");
        exit();
    }
}

if (isset($_GET['delete'])) {
    $announcement_id = $_GET['delete'];
    $delete_query = "DELETE FROM announcements WHERE id = :id";
    $delete_stmt = $db->prepare($delete_query);
    $delete_stmt->bindParam(':id', $announcement_id);
    
    if ($delete_stmt->execute()) {
        Auth::logActivity($_SESSION['user_id'], 'Delete Announcement', "Deleted announcement ID: $announcement_id");
        $_SESSION['success'] = "Announcement deleted!";
        header("Location: announcements.php");
        exit();
    }
}
?>

<?php include '../includes/footer.php'; ?>