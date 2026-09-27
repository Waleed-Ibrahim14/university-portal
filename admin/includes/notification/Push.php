<?php
/*--------------------------------------------------------------------------
| Push Notification Class
|
| FIXES:
|   - Database name corrected: "university_portal" → "portal"
|   - Prepared statements to prevent SQL Injection
|   - updateNotification() syntax error fixed (extra parenthesis)
|   - Password comparison uses password_verify() (not plain equality)
|   - mysqli_error() called with connection parameter
|   - Removed hard-coded username filter (too fragile)
|--------------------------------------------------------------------------*/
class Push {

    private $host       = 'localhost';
    private $user       = 'root';
    private $password   = '';
    private $database   = 'portal';          // FIXED: was "university_portal"
    private $notifTable = 'announcements';
    private $userTable  = 'users';
    private $dbConnect  = false;

    public function __construct() {
        if (!$this->dbConnect) {
            $conn = new mysqli($this->host, $this->user, $this->password, $this->database);
            if ($conn->connect_error) {
                die("Error failed to connect to MySQL: " . $conn->connect_error);
            }
            $conn->set_charset("utf8mb4");
            $this->dbConnect = $conn;
        }
    }

    /*--------------------------------------------------------------------------
    | Internal helper — kept for backwards compatibility with existing callers.
    | NOTE: Only use with hardcoded queries (no user input).
    |--------------------------------------------------------------------------*/
    private function getData($sqlQuery) {
        $result = mysqli_query($this->dbConnect, $sqlQuery);
        if (!$result) {
            die('Error in query: ' . mysqli_error($this->dbConnect));
        }
        $data = [];
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $data[] = $row;
        }
        return $data;
    }

    public function listNotification() {
        $sqlQuery = 'SELECT * FROM ' . $this->notifTable . ' ORDER BY id DESC';
        return $this->getData($sqlQuery);
    }

    /*--------------------------------------------------------------------------
    | Get pending notifications for a specific user.
    | Uses prepared statement.
    |--------------------------------------------------------------------------*/
    public function listNotificationUser($user) {
        $stmt = $this->dbConnect->prepare(
            "SELECT * FROM {$this->notifTable}
             WHERE username = ? AND notif_loop > 0 AND notif_time <= CURRENT_TIMESTAMP()"
        );
        $stmt->bind_param("s", $user);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $stmt->close();
        return $data;
    }

    /*--------------------------------------------------------------------------
    | List all users except admins.
    | Uses a JOIN with roles (more robust than string comparison).
    |--------------------------------------------------------------------------*/
    public function listUsers() {
        $sqlQuery = "SELECT u.id, u.username, u.fullname
                     FROM {$this->userTable} u
                     LEFT JOIN roles r ON u.role_id = r.id
                     WHERE r.role_name IS NULL OR r.role_name != 'admin'
                     ORDER BY u.username ASC";
        return $this->getData($sqlQuery);
    }

    /*--------------------------------------------------------------------------
    | Verify login.
    | NOTE: Uses password_verify() because passwords are now hashed.
    |       NEVER compare plaintext passwords.
    |--------------------------------------------------------------------------*/
    public function loginUsers($username, $password) {
        $stmt = $this->dbConnect->prepare(
            "SELECT id AS userid, username, password FROM {$this->userTable} WHERE username = ? LIMIT 1"
        );
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $stmt->close();
            return [];
        }

        $user = $result->fetch_assoc();
        $stmt->close();

        // Verify password against the stored hash
        if (password_verify($password, $user['password'])) {
            unset($user['password']); // never return the hash
            return [$user];
        }
        return [];
    }

    /*--------------------------------------------------------------------------
    | Save a new notification.
    | Uses prepared statement.
    |--------------------------------------------------------------------------*/
    public function saveNotification($title, $msg, $time, $loop, $loop_every, $user) {
        $stmt = $this->dbConnect->prepare(
            "INSERT INTO {$this->notifTable}
                (title, notif_msg, notif_time, notif_repeat, notif_loop, username)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        if ($stmt === false) {
            return 'Error preparing statement: ' . $this->dbConnect->error;
        }

        $stmt->bind_param("sssiss", $title, $msg, $time, $loop_every, $loop, $user);
        $result = $stmt->execute();
        $stmt->close();

        if (!$result) {
            return 'Error in query: ' . $this->dbConnect->error;
        }
        return $result;
    }

    /*--------------------------------------------------------------------------
    | Update a notification's next schedule time.
    | FIXED: removed extra closing parenthesis in the SQL string.
    |--------------------------------------------------------------------------*/
    public function updateNotification($id, $nextTime) {
        $id = (int) $id;
        $stmt = $this->dbConnect->prepare(
            "UPDATE {$this->notifTable}
             SET notif_time = ?, publish_date = CURRENT_TIMESTAMP(), notif_loop = notif_loop - 1
             WHERE id = ?"
        );
        if ($stmt === false) {
            return false;
        }
        $stmt->bind_param("si", $nextTime, $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
}
?>
