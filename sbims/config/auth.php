<?php
session_start();

class Auth {
    public static function checkAuth() {
        if (!isset($_SESSION['user_id'])) {
            // Get the correct base path
            $base_path = self::getBasePath();
            header("Location: " . $base_path . "login.php");
            exit();
        }
    }

    public static function checkRole($allowed_roles) {
        if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], $allowed_roles)) {
            $base_path = self::getBasePath();
            header("Location: " . $base_path . "unauthorized.php");
            exit();
        }
    }

    public static function login($user_id, $username, $role, $full_name) {
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        $_SESSION['user_role'] = $role;
        $_SESSION['full_name'] = $full_name;
        $_SESSION['last_activity'] = time();
    }

    public static function logout() {
        // Log logout activity
        if (isset($_SESSION['user_id'])) {
            self::logActivity($_SESSION['user_id'], 'Logout', 'User logged out of the system');
        }
        
        session_unset();
        session_destroy();
        
        // Get the correct base path for redirect
        $base_path = self::getBasePath();
        header("Location: " . $base_path . "login.php");
        exit();
    }

    public static function checkTimeout($timeout_minutes = 30) {
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout_minutes * 60)) {
            self::logout();
        }
        $_SESSION['last_activity'] = time();
    }

    public static function logActivity($user_id, $action, $description) {
        try {
            require_once 'connection.php';
            $database = new Database();
            $db = $database->getConnection();

            $query = "INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) 
                     VALUES (:user_id, :action, :description, :ip_address, :user_agent)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':action', $action);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':ip_address', $_SERVER['REMOTE_ADDR']);
            $stmt->bindParam(':user_agent', $_SERVER['HTTP_USER_AGENT']);
            $stmt->execute();
        } catch (Exception $e) {
            error_log("Activity log error: " . $e->getMessage());
        }
    }

    // Helper function to get correct base path
    private static function getBasePath() {
        // If we're in a subfolder (like admin/, secretary/, etc.), go up one level
        $current_dir = dirname($_SERVER['PHP_SELF']);
        $path_parts = explode('/', $current_dir);
        
        // If we're in a subdirectory (not root), add '../'
        if (count($path_parts) > 1 && end($path_parts) != 'sbims') {
            return '../';
        }
        return './';
    }
}
?>