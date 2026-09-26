<?php
/*--------------------------------------------------------------------------
| Create New User Prosses ::
|--------------------------------------------------------------------------*/
include_once("../Models/DataBaseConnection.php");
require_once '../../vendor/autoload.php';

/*--------------------------------------------------------------------------------------------------------
| ISO3166 This library was used, which contains a list of countries according to the ISO-3166-1 standard .
|-------------------------------------------------------------------------------------------------------*/
use League\ISO3166\ISO3166;
$iso3166 = new ISO3166();
$countries = $iso3166->all();

$msg = ''; // Initialize the message variable

if (isset($_POST['create_user'])) {

    // ============ 1. Receiving inputs ============
    $fullname     = $_POST['fullname']     ?? '';
    $country      = $_POST['country']      ?? '';
    $gender       = $_POST['gender']       ?? '';
    $username     = strip_tags($_POST['username'] ?? '');
    $email        = $_POST['email']        ?? '';
    $user_status  = $_POST['user_status']  ?? '';

    // ⚠️ Important change: The form now sends role_id (number) and not role (text)
    $role_id      = isset($_POST['role_id']) && $_POST['role_id'] !== ''
                    ? (int) $_POST['role_id']
                    : null;

    // These fields do not exist in the current form, so we set them to null.
    $scholarship_id = null;
    $group_id       = null;

    // Password: We first verify the origin and then encrypt it.
    $password_raw = $_POST['password_1'] ?? '';
    $password     = password_hash($password_raw, PASSWORD_DEFAULT);

    // Image
    $profile       = $_FILES['profile']['name'] ?? '';
    $target        = "../../assets/images/users/" . basename($profile);
    $imageFileType = strtolower(pathinfo($target, PATHINFO_EXTENSION));

    // ============ 2.Input verification (Validation) ============
    if (empty($fullname)) {
        $msg = '<div class="alert alert-danger">Please enter your full name</div>';
    } elseif (empty($country)) {
        $msg = '<div class="alert alert-danger">Please enter your country</div>';
    } elseif (empty($gender)) {
        $msg = '<div class="alert alert-danger">Please choose your Gender</div>';
    } elseif (empty($username)) {
        $msg = '<div class="alert alert-danger">Please enter your username</div>';
    } elseif (empty($email)) {
        $msg = '<div class="alert alert-danger">Please enter your email</div>';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = '<div class="alert alert-danger">Please enter valid email</div>';
    } elseif (empty($password_raw)) {
        $msg = '<div class="alert alert-danger">Please enter your password</div>';
    } elseif (strlen($password_raw) < 8) {
        $msg = '<div class="alert alert-danger">Password must be minimum of 8 characters</div>';
    } elseif (empty($_POST['password_2'])) {
        $msg = '<div class="alert alert-danger">Please confirm your password</div>';
    } elseif ($_POST['password_1'] !== $_POST['password_2']) {
        $msg = '<div class="alert alert-danger">Password does not match</div>';
    } elseif (empty($role_id)) {
        $msg = '<div class="alert alert-danger">Please select role for user</div>';
    } elseif (empty($user_status)) {
        $msg = '<div class="alert alert-danger">Please select user status</div>';
    } elseif (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'], true)) {
        $msg = '<div class="alert alert-danger">Choose correct image: *.jpg .jpeg .png .gif</div>';
    } elseif ($_FILES["profile"]["size"] > 500000) {
        $msg = '<div class="alert alert-danger">Image size very big</div>';
    } elseif (file_exists($target)) {
        $msg = '<div class="alert alert-danger">Sorry, this file was uploaded before</div>';
    } else {

        // ============ 3. Checking for duplication username/email (Prepared Statements) ============
        $check_stmt = $connection->prepare(
            "SELECT username FROM users WHERE username = ? OR email = ? LIMIT 1"
        );
        $check_stmt->bind_param("ss", $username, $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $existing = $check_result->fetch_assoc();
            if ($existing['username'] === $username) {
                $msg = '<div class="alert alert-danger">Sorry, but the username is already in use</div>';
            } else {
                $msg = '<div class="alert alert-danger">Sorry, but the email is already in use</div>';
            }
        } else {

            // ============ 4. User input (Prepared Statement) ============
            $insert_sql = "INSERT INTO users
                (fullname, country, gender, username, email, password, profile,
                 role_id, user_status, scholarship_id, group_id, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

            $insert_stmt = $connection->prepare($insert_sql);

            if ($insert_stmt === false) {
                die("Prepare failed: " . $connection->error);
            }

            // Types of data:
            // s = string, i = integer
            // role_id, scholarship_id, group_id => integers
            // Note: MySQLi automatically handles null values ​​when passed as null
            $insert_stmt->bind_param(
                "sssssssisii",
                $fullname,
                $country,
                $gender,
                $username,
                $email,
                $password,
                $profile,
                $role_id,
                $user_status,
                $scholarship_id,
                $group_id
            );

            if ($insert_stmt->execute()) {

                // ============ 5. Upload image ============
                if (move_uploaded_file($_FILES['profile']['tmp_name'], $target)) {
                    $msg = '<div class="alert alert-success">User created successfully</div>
                            <meta http-equiv="refresh" content="3; \'show-users.php\'" />';
                } else {
                    $msg = '<div class="alert alert-danger">Sorry, an unexpected error occurred while uploading the image</div>';
                }

            } else {
                $msg = '<div class="alert alert-danger">Database error: ' . htmlspecialchars($insert_stmt->error) . '</div>';
            }

            $insert_stmt->close();
        }
        $check_stmt->close();
    }
}
?>
