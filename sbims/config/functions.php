<?php
function generateCaseID($db) {
    $year = date('Y');
    $query = "SELECT COUNT(*) as count FROM blotters WHERE YEAR(created_at) = :year";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':year', $year);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $sequence = $result['count'] + 1;
    return "BL-$year-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
}

function generateCertificateID($db, $type) {
    $year = date('Y');
    $prefix = '';
    switch ($type) {
        case 'Clearance': $prefix = 'CLC'; break;
        case 'Indigency': $prefix = 'IND'; break;
        case 'Residency': $prefix = 'RES'; break;
    }
    
    $query = "SELECT COUNT(*) as count FROM certificates WHERE certificate_type = :type AND YEAR(created_at) = :year";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':type', $type);
    $stmt->bindParam(':year', $year);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $sequence = $result['count'] + 1;
    return "$prefix-$year-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
}

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function formatDate($date) {
    return date('M j, Y', strtotime($date));
}

function formatDateTime($datetime) {
    return date('M j, Y g:i A', strtotime($datetime));
}
function calculateAge($birthdate) {
    $birthDate = new DateTime($birthdate);
    $today = new DateTime();
    $age = $today->diff($birthDate);
    return $age->y;
}
?>