<?php
session_start();
require_once 'db_config.php';
header('Content-Type: application/json');

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

if (!$username || !$password) {
    echo json_encode(['status' => 'error', 'message' => 'Username and password required']);
    exit;
}

$mysqli = getDBConnection();

// Check if admins table exists
$tableCheck = $mysqli->query("SHOW TABLES LIKE 'admins'");

if ($tableCheck && $tableCheck->num_rows > 0) {
    // Use database authentication
    $stmt = $mysqli->prepare("SELECT id, username, full_name FROM admins WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        
        // Login successful
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['admin_name'] = $admin['full_name'];
        
        // Update last login time
        $updateStmt = $mysqli->prepare("UPDATE admins SET last_login = NOW() WHERE id = ?");
        $updateStmt->bind_param("i", $admin['id']);
        $updateStmt->execute();
        $updateStmt->close();
        
        // Log successful login
        $logCheck = $mysqli->query("SHOW TABLES LIKE 'login_logs'");
        if ($logCheck && $logCheck->num_rows > 0) {
            $logStmt = $mysqli->prepare("INSERT INTO login_logs (username, ip_address, status, message) VALUES (?, ?, 'success', 'Login successful')");
            $logStmt->bind_param("ss", $username, $ip_address);
            $logStmt->execute();
            $logStmt->close();
        }
        
        echo json_encode(['status' => 'success', 'message' => 'Login successful']);
    } else {
        // Log failed login attempt
        $logCheck = $mysqli->query("SHOW TABLES LIKE 'login_logs'");
        if ($logCheck && $logCheck->num_rows > 0) {
            $logStmt = $mysqli->prepare("INSERT INTO login_logs (username, ip_address, status, message) VALUES (?, ?, 'failed', 'Invalid credentials')");
            $logStmt->bind_param("ss", $username, $ip_address);
            $logStmt->execute();
            $logStmt->close();
        }
        
        echo json_encode(['status' => 'error', 'message' => 'Invalid username or password']);
    }
    $stmt->close();
} else {
    // Fallback to hardcoded credentials if table doesn't exist
    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        $_SESSION['admin_name'] = 'Administrator';
        echo json_encode(['status' => 'success', 'message' => 'Login successful']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid username or password']);
    }
}

$mysqli->close();
?>
