<?php
$page_title = "Announcements";
require_once '../config/auth.php';
require_once '../config/connection.php';
require_once '../config/functions.php';

Auth::checkAuth();
Auth::checkRole(['resident']);

$database = new Database();
$db = $database->getConnection();

// Get active announcements
$query = "SELECT a.*, u.full_name as posted_by_name 
          FROM announcements a 
          JOIN users u ON a.posted_by = u.id 
          WHERE a.is_active = 1 
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
                <h1>Barangay Announcements</h1>
                <p>Latest updates and public advisories from Barangay Libertad</p>
            </div>
        </div>

        <?php if (count($announcements) > 0): ?>
            <div class="announcements-list">
                <?php foreach ($announcements as $announcement): ?>
                <div class="announcement-card">
                    <div class="announcement-header">
                        <h3><?php echo htmlspecialchars($announcement['title']); ?></h3>
                        <div class="announcement-meta">
                            <span>Posted by: <?php echo htmlspecialchars($announcement['posted_by_name']); ?></span>
                            <span>•</span>
                            <span><?php echo formatDateTime($announcement['created_at']); ?></span>
                        </div>
                    </div>
                    <div class="announcement-content">
                        <?php echo nl2br(htmlspecialchars($announcement['content'])); ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-data">
                <div class="no-data-icon">📢</div>
                <h3>No Announcements</h3>
                <p>There are no active announcements at the moment.</p>
                <p>Check back later for updates from the barangay officials.</p>
            </div>
        <?php endif; ?>
    </main>
</div>

<style>
.announcements-list {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.announcement-card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 2rem;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    transition: transform 0.2s;
}

.announcement-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.announcement-header h3 {
    color: var(--primary);
    margin: 0 0 0.5rem 0;
    font-size: 1.3rem;
}

.announcement-meta {
    color: var(--secondary);
    font-size: 0.9rem;
    margin-bottom: 1rem;
}

.announcement-content {
    line-height: 1.7;
    color: var(--dark);
    font-size: 1rem;
}

.no-data {
    text-align: center;
    padding: 3rem;
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}

.no-data-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.no-data h3 {
    color: var(--dark);
    margin-bottom: 1rem;
}

.no-data p {
    color: var(--secondary);
    margin-bottom: 0.5rem;
}
</style>

<?php include '../includes/footer.php'; ?>