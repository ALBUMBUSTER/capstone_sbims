<?php
$page_title = "Community Statistics";
require_once '../config/auth.php';
require_once '../config/connection.php';
require_once '../config/functions.php';

Auth::checkAuth();
Auth::checkRole(['resident']);

$database = new Database();
$db = $database->getConnection();

// Get population statistics
$total_residents_query = "SELECT COUNT(*) as total FROM residents";
$total_residents_stmt = $db->prepare($total_residents_query);
$total_residents_stmt->execute();
$total_residents = $total_residents_stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Gender statistics
$gender_query = "SELECT gender, COUNT(*) as count FROM residents GROUP BY gender";
$gender_stmt = $db->prepare($gender_query);
$gender_stmt->execute();
$gender_stats = $gender_stmt->fetchAll(PDO::FETCH_ASSOC);

// Purok statistics
$purok_query = "SELECT purok, COUNT(*) as count FROM residents GROUP BY purok ORDER BY purok";
$purok_stmt = $db->prepare($purok_query);
$purok_stmt->execute();
$purok_stats = $purok_stmt->fetchAll(PDO::FETCH_ASSOC);

// Special groups
$voters_query = "SELECT COUNT(*) as count FROM residents WHERE is_voter = 1";
$voters_stmt = $db->prepare($voters_query);
$voters_stmt->execute();
$total_voters = $voters_stmt->fetch(PDO::FETCH_ASSOC)['count'];

$seniors_query = "SELECT COUNT(*) as count FROM residents WHERE is_senior = 1";
$seniors_stmt = $db->prepare($seniors_query);
$seniors_stmt->execute();
$total_seniors = $seniors_stmt->fetch(PDO::FETCH_ASSOC)['count'];

$pwd_query = "SELECT COUNT(*) as count FROM residents WHERE is_pwd = 1";
$pwd_stmt = $db->prepare($pwd_query);
$pwd_stmt->execute();
$total_pwd = $pwd_stmt->fetch(PDO::FETCH_ASSOC)['count'];

$fourps_query = "SELECT COUNT(*) as count FROM residents WHERE is_4ps = 1";
$fourps_stmt = $db->prepare($fourps_query);
$fourps_stmt->execute();
$total_4ps = $fourps_stmt->fetch(PDO::FETCH_ASSOC)['count'];
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/topbar.php'; ?>

<div class="main-container">
    <?php include '../includes/sidebar.php'; ?>
    
    <main class="content">
        <div class="page-header">
            <div class="page-title">
                <h1>Community Statistics</h1>
                <p>Barangay Libertad Population and Demographic Data</p>
            </div>
        </div>

        <!-- Summary Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Population</h3>
                <div class="stat-value"><?php echo $total_residents; ?></div>
                <div class="stat-trend">Registered Residents</div>
            </div>
            
            <div class="stat-card">
                <h3>Registered Voters</h3>
                <div class="stat-value"><?php echo $total_voters; ?></div>
                <div class="stat-trend"><?php echo round(($total_voters/$total_residents)*100, 1); ?>% of population</div>
            </div>
            
            <div class="stat-card">
                <h3>Senior Citizens</h3>
                <div class="stat-value"><?php echo $total_seniors; ?></div>
                <div class="stat-trend"><?php echo round(($total_seniors/$total_residents)*100, 1); ?>% of population</div>
            </div>
            
            <div class="stat-card">
                <h3>PWD Residents</h3>
                <div class="stat-value"><?php echo $total_pwd; ?></div>
                <div class="stat-trend">Persons with Disabilities</div>
            </div>
        </div>

        <div class="dashboard-grid">
            <!-- Gender Distribution -->
            <div class="data-table">
                <div class="table-header">
                    <h3>Gender Distribution</h3>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Gender</th>
                            <th>Count</th>
                            <th>Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($gender_stats as $gender): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($gender['gender']); ?></td>
                            <td><?php echo $gender['count']; ?></td>
                            <td><?php echo round(($gender['count']/$total_residents)*100, 1); ?>%</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Purok Distribution -->
            <div class="data-table">
                <div class="table-header">
                    <h3>Population by Purok</h3>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Purok</th>
                            <th>Population</th>
                            <th>Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($purok_stats as $purok): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($purok['purok']); ?></td>
                            <td><?php echo $purok['count']; ?></td>
                            <td><?php echo round(($purok['count']/$total_residents)*100, 1); ?>%</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Special Programs -->
        <div class="data-table">
            <div class="table-header">
                <h3>Social Programs</h3>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Program</th>
                        <th>Beneficiaries</th>
                        <th>Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>4PS (Pantawid Pamilyang Pilipino Program)</td>
                        <td><?php echo $total_4ps; ?></td>
                        <td><?php echo round(($total_4ps/$total_residents)*100, 1); ?>%</td>
                    </tr>
                    <tr>
                        <td>Senior Citizens</td>
                        <td><?php echo $total_seniors; ?></td>
                        <td><?php echo round(($total_seniors/$total_residents)*100, 1); ?>%</td>
                    </tr>
                    <tr>
                        <td>Persons with Disabilities (PWD)</td>
                        <td><?php echo $total_pwd; ?></td>
                        <td><?php echo round(($total_pwd/$total_residents)*100, 1); ?>%</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Note -->
        <div class="info-box">
            <h4>📊 About These Statistics</h4>
            <p>These statistics are based on the official barangay registry and are updated regularly. The data helps in planning community programs and services.</p>
            <p><small>Last updated: <?php echo date('F j, Y'); ?></small></p>
        </div>
    </main>
</div>

<style>
.dashboard-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.info-box {
    background: #e0f2fe;
    border: 1px solid #7dd3fc;
    border-radius: 8px;
    padding: 1.5rem;
    margin-top: 2rem;
}

.info-box h4 {
    color: #0369a1;
    margin: 0 0 1rem 0;
}

.info-box p {
    color: #0c4a6e;
    margin-bottom: 0.5rem;
}

.info-box small {
    color: #64748b;
}

@media (max-width: 768px) {
    .dashboard-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include '../includes/footer.php'; ?>