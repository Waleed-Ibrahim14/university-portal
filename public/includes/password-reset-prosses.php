<?php 
/*--------------------------------------------------------------------------
| Password Reset Process (Public) ::
|--------------------------------------------------------------------------*/
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once(__DIR__ . "/../../admin/Models/DataBaseConnection.php");

$msg = '';

if (isset($_POST['reset-password'])) {

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
        | Fetch user (prepared statement — matches project OOP style)
        |--------------------------------------------------------------------------*/
        $stmt = $connection->prepare(
            "SELECT id, email, password FROM users WHERE email = ? LIMIT 1"
        );

        if ($stmt === false) {
            $msg = '<div class="alert alert-danger" role="alert">Database error</div>';
        } else {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
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

                $upd = $connection->prepare(
                    "UPDATE users SET password = ?, updated_at = NOW() WHERE id = ? LIMIT 1"
                );
                $upd->bind_param("si", $hashedPassword, $user['id']);

                if ($upd->execute() && $upd->affected_rows > 0) {
                    // FIXED meta refresh syntax
                    $msg = '<div class="alert alert-success" role="alert">Password changed successfully</div>'
                         . '<meta http-equiv="refresh" content="3; url=index.php" />';
                } else {
                    $msg = '<div class="alert alert-danger" role="alert">Error updating password</div>';
                }
                $upd->close();
            }
        }
    }
}
?>
