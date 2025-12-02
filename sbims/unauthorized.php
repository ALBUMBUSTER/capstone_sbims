<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied - SBIMS-PRO</title>
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="logo" style="background-color: #ef4444;">!</div>
            <h1>Access Denied</h1>
            <p>You don't have permission to access this page</p>
        </div>
        
        <div class="unauthorized-actions">
            <a href="javascript:history.back()" class="btn btn-outline">Go Back</a>
            <a href="index.php" class="btn btn-primary">Go to Dashboard</a>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
        
        <div class="login-footer">
            <p>SBIMS-PRO - Barangay Libertad</p>
        </div>
    </div>
</body>
</html>