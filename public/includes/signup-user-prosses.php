<?php
/*--------------------------------------------------------------------------
| Create New User Process (Public Signup) ::
|--------------------------------------------------------------------------*/
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once(__DIR__ . "/../../admin/Models/DataBaseConnection.php");
require_once __DIR__ . "/../../vendor/autoload.php";

use League\ISO3166\ISO3166;
$iso3166 = new ISO3166();
$countries = $iso3166->all();

$msg = '';

if (isset($_POST['submit'])) {

    /*--------------------------------------------------------------------------
    | Read and sanitize inputs
    |--------------------------------------------------------------------------*/
    $fullname     = trim($_POST['fullname'] ?? '');
    $country      = trim($_POST['country'] ?? '');
    $gender       = trim($_POST['gender'] ?? '');
    $username     = trim(strip_tags($_POST['username'] ?? ''));
    $email        = trim($_POST['email'] ?? '');
    $password_raw = $_POST['password_1'] ?? '';

    // File
    $profile       = $_FILES['profile']['name'] ?? '';
    $target        = "../assets/images/users/" . basename($profile);
    $imageFileType = strtolower(pathinfo($target, PATHINFO_EXTENSION));

    /*--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------*/
    if (empty($fullname)) {
        $msg = '<div class="alert alert-danger" role="alert">Please enter your full name</div>';
    } elseif (empty($country)) {
        $msg = '<div class="alert alert-danger" role="alert">Please enter your country</div>';
    } elseif (empty($gender)) {
        $msg = '<div class="alert alert-danger" role="alert">Please select your gender</div>';
    } elseif (empty($username)) {
        $msg = '<div class="alert alert-danger" role="alert">Please enter your username</div>';
    } elseif (empty($email)) {
        $msg = '<div class="alert alert-danger" role="alert">Please enter your email</div>';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = '<div class="alert alert-danger" role="alert">Please enter a valid email</div>';
    } elseif (empty($password_raw)) {
        $msg = '<div class="alert alert-danger" role="alert">Please enter your password</div>';
    } elseif (strlen($password_raw) < 8) {
        $msg = '<div class="alert alert-danger" role="alert">Password must be at least 8 characters</div>';
    } elseif (empty($_POST['password_2'])) {
        $msg = '<div class="alert alert-danger" role="alert">Please confirm your password</div>';
    } elseif ($_POST['password_1'] !== $_POST['password_2']) {
        $msg = '<div class="alert alert-danger" role="alert">Passwords do not match</div>';
    } elseif (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'], true)) {
        $msg = '<div class="alert alert-danger" role="alert">Choose a correct image (*.jpg, *.jpeg, *.png, *.gif)</div>';
    } elseif (($_FILES["profile"]["size"] ?? 0) > 500000) {
        $msg = '<div class="alert alert-danger" role="alert">Image size is too big (max 500 KB)</div>';
    } elseif (file_exists($target)) {
        $msg = '<div class="alert alert-danger" role="alert">Sorry, this file was uploaded before</div>';

    } else {
        /*--------------------------------------------------------------------------
        | Check duplicate username/email (prepared statements)
        |--------------------------------------------------------------------------*/
        $chk = $connection->prepare("SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1");
        $chk->bind_param("ss", $username, $email);
        $chk->execute();
        $dup = $chk->get_result()->num_rows > 0;
        $chk->close();

        if ($dup) {
            $msg = '<div class="alert alert-danger" role="alert">Username or email is already in use</div>';
        } else {

            /*--------------------------------------------------------------------------
            | Get the 'user' role id (FK) — was: role = 'user' (removed column)
            |--------------------------------------------------------------------------*/
            $roleStmt = $connection->prepare("SELECT id FROM roles WHERE role_name = 'user' LIMIT 1");
            $roleStmt->execute();
            $roleRow = $roleStmt->get_result()->fetch_assoc();
            $roleStmt->close();

            if (!$roleRow) {
                $msg = '<div class="alert alert-danger" role="alert">Default user role not found in DB</div>';
            } else {
                $role_id = (int) $roleRow['id'];

                $password = password_hash($password_raw, PASSWORD_DEFAULT);

                /*--------------------------------------------------------------------------
                | INSERT with FK columns (was: role, group_name, scholarship_name)
                |--------------------------------------------------------------------------*/
                $stmt = $connection->prepare(
                    "INSERT INTO users 
                        (fullname, country, gender, username, email, password, profile,
                         role_id, group_id, scholarship_id, user_status, created_at, updated_at)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, NULL, NULL, 'blocked', NOW(), NOW())"
                );

                if ($stmt === false) {
                    $msg = '<div class="alert alert-danger" role="alert">Database error: ' . htmlspecialchars($connection->error) . '</div>';
                } else {
                    $stmt->bind_param(
                        "sssssssi",
                        $fullname, $country, $gender, $username, $email, $password, $profile, $role_id
                    );

                    if ($stmt->execute()) {
                        if (move_uploaded_file($_FILES['profile']['tmp_name'], $target)) {
                            /*--------------------------------------------------------------------------
                            | Auto-login the new user (with modern session keys)
                            |--------------------------------------------------------------------------*/
                            $newId = $stmt->insert_id;
                            $userQuery = $connection->prepare(
                                "SELECT u.*, r.role_name AS role_name
                                 FROM users u
                                 LEFT JOIN roles r ON u.role_id = r.id
                                 WHERE u.id = ? LIMIT 1"
                            );
                            $userQuery->bind_param("i", $newId);
                            $userQuery->execute();
                            $user = $userQuery->get_result()->fetch_assoc();
                            $userQuery->close();

                            $_SESSION['id']          = $user['id'];
                            $_SESSION['fullname']    = $user['fullname'];
                            $_SESSION['country']     = $user['country'];
                            $_SESSION['gender']      = $user['gender'];
                            $_SESSION['username']    = $user['username'];
                            $_SESSION['email']       = $user['email'];
                            $_SESSION['profile']     = $user['profile'];
                            $_SESSION['role_id']     = $user['role_id'];
                            $_SESSION['role_name']   = $user['role_name'];
                            $_SESSION['role']        = $user['role_name'];
                            $_SESSION['user_status'] = $user['user_status'];

                            $msg = '<div class="alert alert-success" role="alert">You have been registered successfully.</div>'
                                 . '<meta http-equiv="refresh" content="2; url=user-activation-form.php" />';
                        } else {
                            $msg = '<div class="alert alert-danger" role="alert">User saved but image upload failed</div>';
                        }
                    } else {
                        $msg = '<div class="alert alert-danger" role="alert">Error: ' . htmlspecialchars($stmt->error) . '</div>';
                    }
                    $stmt->close();
                }
            }
        }
    }
}
?>
