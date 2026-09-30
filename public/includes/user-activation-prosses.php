<?php 
/*--------------------------------------------------------------------------
| User Activation Process ::
|--------------------------------------------------------------------------*/
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once(__DIR__ . "/../../admin/Models/DataBaseConnection.php");

$msg = '';

/*--------------------------------------------------------------------------
| Load current user if logged in (with role via JOIN)
|--------------------------------------------------------------------------*/
$currentUser = null;
if (!empty($_SESSION['id'])) {
    $stmt = $connection->prepare(
        "SELECT u.*, r.role_name AS role_name
         FROM users u
         LEFT JOIN roles r ON u.role_id = r.id
         WHERE u.id = ? LIMIT 1"
    );
    $stmt->bind_param("i", $_SESSION['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $currentUser = $result->fetch_assoc();
    }
    $stmt->close();
}

/*--------------------------------------------------------------------------
| Handle activation form submission
|--------------------------------------------------------------------------*/
if (isset($_POST['activation-account'])) {

    $user_status = $currentUser['user_status'] ?? '';

    if (empty($_POST['email'])) {
        $msg = '<div class="alert alert-danger" role="alert">Please enter your email</div>';
    } elseif ($user_status === 'active') {
        $msg = '<div class="alert alert-danger" role="alert">Your account is already active</div>';
    } elseif (!isset($_SESSION['email']) || $_SESSION['email'] !== $_POST['email']) {
        $msg = '<div class="alert alert-danger" role="alert">Email does not match the account</div>';
    } else {
        /*--------------------------------------------------------------------------
        | Activate the account (prepared statement)
        |--------------------------------------------------------------------------*/
        $stmt = $connection->prepare(
            "UPDATE users SET user_status = 'active', updated_at = NOW() WHERE id = ? LIMIT 1"
        );
        $stmt->bind_param("i", $_SESSION['id']);

        if ($stmt->execute()) {
            // FIXED meta refresh syntax
            $msg = '<div class="alert alert-success" role="alert">Activated successfully.</div>'
                 . '<meta http-equiv="refresh" content="2; url=../index.php" />';
        }
        $stmt->close();
    }
}
?>
