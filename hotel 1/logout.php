<?php
session_start();

// Store username for message
$username = $_SESSION['username'] ?? 'User';

// Destroy all session data
$_SESSION = array();

// If a session cookie is used, destroy it
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Redirect with message
echo "<script>
    localStorage.setItem('logoutMessage', 'Goodbye, $username! You have been logged out successfully.');
    window.location.href = 'index.php';
</script>";
exit();
?>