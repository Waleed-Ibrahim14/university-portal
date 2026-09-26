<?php
/*--------------------------------------------------------------------------
| Logout Process ::
|--------------------------------------------------------------------------*/

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Clear all session variables in memory
$_SESSION = [];

// 2. Delete the session cookie from the browser
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// 3. Destroy the session on the server
session_destroy();

// 4. Redirect to login page and stop further execution
header("Location: login.php");
exit;
?>
