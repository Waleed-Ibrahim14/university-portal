<?php 
/*--------------------------------------------------------------------------
| Log-in Process for Public Users (Students/Teachers) ::
|--------------------------------------------------------------------------*/
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*--------------------------------------------------------------------------
| Use __DIR__ for reliable path resolution.
| From public/includes/ → ../../admin/Models/
|--------------------------------------------------------------------------*/
include_once(__DIR__ . "/../../admin/Models/DataBaseConnection.php");

$msg = '';

if (isset($_POST['login'])) {

    /*--------------------------------------------------------------------------
    | Sanitize inputs
    |--------------------------------------------------------------------------*/
    $email          = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password_input = $_POST['password'] ?? '';

    /*--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------*/
    if (empty($email)) {
        $msg = '<div class="alert alert-danger" role="alert">Please enter your email</div>';
    } elseif (empty($password_input)) {
        $msg = '<div class="alert alert-danger" role="alert">Please enter your password</div>';
    } else {

        /*--------------------------------------------------------------------------
        | Prepared statement + JOIN with roles
        |--------------------------------------------------------------------------*/
        $sql = "SELECT u.id, u.fullname, u.country, u.gender, u.username, u.email,
                       u.password, u.profile, u.role_id, u.user_status,
                       r.role_name AS role_name
                FROM users u
                LEFT JOIN roles r ON u.role_id = r.id
                WHERE u.email = ?
                LIMIT 1";

        $stmt = $connection->prepare($sql);

        if ($stmt === false) {
            die("Prepare failed: " . $connection->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if (password_verify($password_input, $user['password'])) {

                $user_status = $user['user_status'];

                if ($user_status === 'active') {

                    /*--------------------------------------------------------------------------
                    | Set session — role_name + role_id (was role, now removed)
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
                    $_SESSION['role']        = $user['role_name']; // backward compat
                    $_SESSION['user_status'] = $user_status;
                    $_SESSION['login']       = true;

                    // FIXED meta refresh syntax
                    $msg = '<div class="alert alert-success" role="alert">'
                         . htmlspecialchars($_SESSION['username'])
                         . ' logged in successfully</div>'
                         . '<meta http-equiv="refresh" content="3; url=index.php" />';

                } else {
                    $msg = '<div class="alert alert-danger" role="alert">Your account is blocked. Please contact support.</div>';
                }
            } else {
                $msg = '<div class="alert alert-danger" role="alert">Your password is wrong</div>';
            }
        } else {
            $msg = '<div class="alert alert-danger" role="alert">User not found</div>';
        }

        $stmt->close();
    }
}
?>
