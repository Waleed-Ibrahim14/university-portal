<?php 
/*--------------------------------------------------------------------------
| Password Reset Process ::
|--------------------------------------------------------------------------*/

/*--------------------------------------------------------------------------
| Start session FIRST (before any $_SESSION usage).
|--------------------------------------------------------------------------*/
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*--------------------------------------------------------------------------
| Use __DIR__ for reliable path resolution.
|--------------------------------------------------------------------------*/
include_once(__DIR__ . "/../Models/DataBaseConnection.php");

$msg = '';

if (isset($_POST['reset-password'])) {

    /*--------------------------------------------------------------------------
    | Read and sanitize inputs
    |--------------------------------------------------------------------------*/
    $email       = trim($_POST['email'] ?? '');
    $oldPassword = $_POST['oldPassword'] ?? '';
    $newPassword = $_POST['password'] ?? '';

    /*--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------*/
    if (empty($email)) {
        $msg = '<div class="alert alert-danger" role="alert">Please enter your email</div>';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = '<div class="alert alert-danger" role="alert">Please enter a valid email</div>';
    } elseif (empty($oldPassword)) {
        $msg = '<div class="alert alert-danger" role="alert">Please enter your old password</div>';
    } elseif (empty($newPassword)) {
        $msg = '<div class="alert alert-danger" role="alert">Please enter your new password</div>';
    } elseif (strlen($newPassword) < 8) {
        $msg = '<div class="alert alert-danger" role="alert">New password must be at least 8 characters</div>';
    } elseif ($newPassword === $oldPassword) {
        $msg = '<div class="alert alert-danger" role="alert">New password must be different from the old one</div>';
    } else {

        /*--------------------------------------------------------------------------
        | Fetch user (with role_name via JOIN)
        |--------------------------------------------------------------------------*/
        $stmt = $connection->prepare(
            "SELECT u.*, r.role_name AS role_name
             FROM users u
             LEFT JOIN roles r ON u.role_id = r.id
             WHERE u.email = ?
             LIMIT 1"
        );

        if ($stmt === false) {
            $msg = '<div class="alert alert-danger" role="alert">Database error: '
                 . htmlspecialchars($connection->error) . '</div>';
        } else {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();

            if (!$user) {
                $msg = '<div class="alert alert-danger" role="alert">Email not found</div>';
            } elseif (!password_verify($oldPassword, $user['password'])) {
                $msg = '<div class="alert alert-danger" role="alert">Your old password is incorrect</div>';
            } else {

                /*--------------------------------------------------------------------------
                | Update password
                |--------------------------------------------------------------------------*/
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                $update = $connection->prepare(
                    "UPDATE users SET password = ?, updated_at = NOW() WHERE id = ? LIMIT 1"
                );

                if ($update === false) {
                    $msg = '<div class="alert alert-danger" role="alert">Database error: '
                         . htmlspecialchars($connection->error) . '</div>';
                } else {
                    $update->bind_param("si", $hashedPassword, $user['id']);

                    if ($update->execute() && $update->affected_rows > 0) {

                        /*--------------------------------------------------------------------------
                        | Re-set session (auto-login after password change)
                        | FIXED: use role_name + role_id (was $_SESSION['role'] = $user['role'])
                        |--------------------------------------------------------------------------*/
                        $_SESSION['id']          = $user['id'];
                        $_SESSION['fullname']    = $user['fullname'];
                        $_SESSION['country']     = $user['country'];
                        $_SESSION['gender']      = $user['gender'];
                        $_SESSION['username']    = $user['username'];
                        $_SESSION['email']       = $user['email'];
                        $_SESSION['profile']     = $user['profile'];
                        $_SESSION['role_id']     = $user['role_id'];
                        $_SESSION['role_name']   = $user['role_name'];
                        $_SESSION['role']        = $user['role_name'];  // backward compat
                        $_SESSION['user_status'] = $user['user_status'] ?? null;
                        $_SESSION['login']       = true;

                        // FIXED meta refresh syntax
                        $msg = '<div class="alert alert-success" role="alert">'
                             . 'Password changed successfully. Redirecting to dashboard...</div>'
                             . '<meta http-equiv="refresh" content="3; url=dashboard.php" />';
                    } else {
                        $msg = '<div class="alert alert-danger" role="alert">Error updating password</div>';
                    }
                    $update->close();
                }
            }
        }
    }
}
?>
