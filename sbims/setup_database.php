<?php
// Database setup script - Run this once to create tables and admin user
require_once 'config/connection.php';

$database = new Database();
$db = $database->getConnection();

try {
    // Create users table
    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id INT PRIMARY KEY AUTO_INCREMENT,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(100),
        role ENUM('admin', 'captain', 'secretary', 'resident') NOT NULL,
        full_name VARCHAR(100) NOT NULL,
        is_active BOOLEAN DEFAULT TRUE,
        last_login DATETIME,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Create residents table
    $db->exec("CREATE TABLE IF NOT EXISTS residents (
        id INT PRIMARY KEY AUTO_INCREMENT,
        resident_id VARCHAR(20) UNIQUE NOT NULL,
        first_name VARCHAR(50) NOT NULL,
        middle_name VARCHAR(50),
        last_name VARCHAR(50) NOT NULL,
        birthdate DATE NOT NULL,
        gender ENUM('Male', 'Female') NOT NULL,
        civil_status ENUM('Single', 'Married', 'Widowed', 'Divorced') NOT NULL,
        contact_number VARCHAR(15),
        email VARCHAR(100),
        address TEXT NOT NULL,
        purok VARCHAR(50) NOT NULL,
        household_number VARCHAR(20),
        is_voter BOOLEAN DEFAULT FALSE,
        is_4ps BOOLEAN DEFAULT FALSE,
        is_senior BOOLEAN DEFAULT FALSE,
        is_pwd BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");

    // Create default admin user
    $username = "admin";
    $password = password_hash("admin123", PASSWORD_DEFAULT);
    $full_name = "System Administrator";
    $role = "admin";
    $email = "admin@barangaylibertad.com";

    $query = "INSERT IGNORE INTO users (username, password, email, role, full_name) 
              VALUES (:username, :password, :email, :role, :full_name)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':role', $role);
    $stmt->bindParam(':full_name', $full_name);
    $stmt->execute();

    echo "Database setup completed successfully!<br>";
    echo "Default admin credentials:<br>";
    echo "Username: admin<br>";
    echo "Password: admin123<br>";
    echo "<a href='login.php'>Go to Login</a>";

} catch (PDOException $e) {
    echo "Error setting up database: " . $e->getMessage();
}
?>