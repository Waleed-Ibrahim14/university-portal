<?php 
/*--------------------------------------------------------------------------
| Password Reset Prosses::
|--------------------------------------------------------------------------*/
include_once("../Models/DataBaseConnection.php");

$msg = ""; // Initialize the message variable

if (isset($_POST['reset-password'])) {
    $email = trim($_POST['email']);
    $oldPassword = $_POST['oldPassword'];
    $newPassword = $_POST['password'];

    // Validate input data
    if (empty($email)) {
        $msg = '<div class="alert alert-danger" role="alert">Please enter your email</div>';
    } elseif (empty($oldPassword)) {
        $msg = '<div class="alert alert-danger" role="alert">Please enter your old password</div>';
    } elseif (empty($newPassword)) {
        $msg = '<div class="alert alert-danger" role="alert">Please enter your new password</div>';
    } else {
        // Check if the email exists in the database
        $stmt = mysqli_prepare($connection, "SELECT * FROM `users` WHERE `email` = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if (!$user) {
            $msg = '<div class="alert alert-danger" role="alert">Email not found</div>';
        } elseif (!password_verify($oldPassword, $user['password'])) {
            $msg = '<div class="alert alert-danger" role="alert">Your old password is incorrect</div>';
        } else {
            // Update the password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $updateStmt = mysqli_prepare($connection, "UPDATE `users` SET `password` = ?, `updated_at` = NOW() WHERE `email` = ?");
            mysqli_stmt_bind_param($updateStmt, "ss", $hashedPassword, $email);
            mysqli_stmt_execute($updateStmt);

            if (mysqli_affected_rows($connection) > 0) {
                $_SESSION['id'] = $user['id'];
                $_SESSION['fullname'] = $user['fullname'];
                $_SESSION['country'] = $user['country'];
                $_SESSION['gender'] = $user['gender'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['profile'] = $user['profile'];
                $_SESSION['role'] = $user['role'];
                $msg = '<div class="alert alert-success" role="alert">Password changed successfully Redirection to Dashoard</div><meta http-equiv="refresh"content="3; \'dashboard.php\' "/>';
            } else {
                $msg = '<div class="alert alert-danger" role="alert">Error updating password</div>';
            }
        }
    }
}
?>