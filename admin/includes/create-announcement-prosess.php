<?php 	
/*--------------------------------------------------------------------------
| Create Announcement Process ::
|--------------------------------------------------------------------------*/

/*--------------------------------------------------------------------------
| Use __DIR__ for reliable path resolution.
|--------------------------------------------------------------------------*/
include_once(__DIR__ . "/../Models/DataBaseConnection.php");
include_once(__DIR__ . "/notification/Push.php");

$push = new Push();
$message = '';

if (isset($_POST['submit'])) {

    /*--------------------------------------------------------------------------
    | Read and sanitize inputs
    |--------------------------------------------------------------------------*/
    $title      = trim($_POST['title'] ?? '');
    $msg_body   = trim($_POST['msg'] ?? '');
    $loop       = isset($_POST['loops']) && $_POST['loops'] !== '' ? (int) $_POST['loops'] : null;
    $loop_every = isset($_POST['loop_every']) && $_POST['loop_every'] !== '' ? (int) $_POST['loop_every'] : null;
    $user       = trim($_POST['user'] ?? '');

    // Broadcast time is always "now" — ignore the form value
    $time = date('Y-m-d H:i:s');

    /*--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------*/
    if (empty($title)) {
        $message = '<div class="alert alert-danger" role="alert">Enter the notification title</div>';
    } elseif (empty($msg_body)) {
        $message = '<div class="alert alert-danger" role="alert">Enter the notification body</div>';
    } elseif (empty($loop)) {
        $message = '<div class="alert alert-danger" role="alert">Enter loop (times) number</div>';
    } elseif (empty($loop_every)) {
        $message = '<div class="alert alert-danger" role="alert">Enter loop every (minutes)</div>';
    } elseif (empty($user)) {
        $message = '<div class="alert alert-danger" role="alert">Select a user</div>';
    } else {

        /*--------------------------------------------------------------------------
        | Verify the target user exists (integrity check)
        |--------------------------------------------------------------------------*/
        $check = $connection->prepare("SELECT id FROM users WHERE username = ? LIMIT 1");
        $check->bind_param("s", $user);
        $check->execute();
        $userExists = $check->get_result()->num_rows > 0;
        $check->close();

        if (!$userExists) {
            $message = '<div class="alert alert-danger" role="alert">Selected user does not exist</div>';
        } else {

            /*--------------------------------------------------------------------------
            | Save via Push class (which handles DB insertion internally)
            |--------------------------------------------------------------------------*/
            $isSaved = $push->saveNotification($title, $msg_body, $time, $loop, $loop_every, $user);

            if ($isSaved) {
                // FIXED meta refresh syntax
                $message = '<div class="alert alert-success" role="alert">Notification saved successfully</div>'
                         . '<meta http-equiv="refresh" content="2; url=show-announcements.php" />';
            } else {
                $message = '<div class="alert alert-danger" role="alert">Error saving notification</div>';
            }
        }
    }
}
?>
