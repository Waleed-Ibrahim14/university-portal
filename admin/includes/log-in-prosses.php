<?php 
/*--------------------------------------------------------------------------
| Log-in Process ::
|--------------------------------------------------------------------------*/
include_once("../Models/DataBaseConnection.php");

// Start session if not already started (safe guard against duplicate session_start calls)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize message variable
$msg = "";

if (isset($_POST['login'])) {

    // Sanitize email input (FILTER_SANITIZE_EMAIL is enough; no need for mysqli_real_escape_string since we use prepared statements)
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password_input = $_POST['password'] ?? '';

    // Basic validation
    if (empty($email)) {
        $msg = '<div class="alert alert-danger" role="alert">Please Enter Your Email</div>';
    } elseif (empty($password_input)) {
        $msg = '<div class="alert alert-danger" role="alert">Please Enter Your Password</div>';
    } else {

        // ------------------------------------------------------------------
        // Retrieve user data based on the provided email, with JOIN to get role_name
        // Use prepared statement to prevent SQL Injection
        // ------------------------------------------------------------------
        $sql_select = "SELECT
                           u.id,
                           u.fullname,
                           u.country,
                           u.gender,
                           u.username,
                           u.email,
                           u.password,
                           u.profile,
                           u.role_id,
                           u.user_status,
                           r.role_name AS role_name
                       FROM users u
                       LEFT JOIN roles r ON u.role_id = r.id
                       WHERE u.email = ?
                       LIMIT 1";

        $stmt = $connection->prepare($sql_select);

        if ($stmt === false) {
            die("Prepare failed: " . $connection->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {

            $user = $result->fetch_assoc();
            $hashed_password = $user['password'];

            // Verify the password
            if (password_verify($password_input, $hashed_password)) {

                // Check if the user is not blocked
                $user_status = $user['user_status'];

                if ($user_status === 'active') {

                    // ------------------------------------------------------------------
                    // Set Session Variables
                    // NOTE: We now store BOTH role_id (numeric) and role_name (string)
                    //       to keep backward compatibility and enable FK-based logic.
                    // ------------------------------------------------------------------
                    $_SESSION['id']          = $user['id'];
                    $_SESSION['fullname']    = $user['fullname'];
                    $_SESSION['country']     = $user['country'];
                    $_SESSION['gender']      = $user['gender'];
                    $_SESSION['username']    = $user['username'];
                    $_SESSION['email']       = $user['email'];
                    $_SESSION['profile']     = $user['profile'];
                    $_SESSION['role_id']     = $user['role_id'];      // numeric FK
                    $_SESSION['role_name']   = $user['role_name'];    // human-readable name
                    $_SESSION['role']        = $user['role_name'];    // backward-compat alias (older files still read 'role')
                    $_SESSION['user_status'] = $user_status;
                    $_SESSION['login']       = true;

                    // Redirect To Dashboard After Successful Login
                    $msg = '<div class="alert alert-success" role="alert">'
                         . htmlspecialchars($_SESSION['username'])
                         . ' Logged in Successfully</div>'
                         . '<meta http-equiv="refresh" content="3; \'dashboard.php\'" />';

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
