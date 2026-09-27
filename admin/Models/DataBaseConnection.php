<?php
/*--------------------------------------------------------------------------
| Database Connection
|
| This file provides:
|   1. A singleton `Database` class (used in PHPUnit tests).
|   2. A global `$connection` variable (used in admin pages and REST API).
|
| Adjust host/user/pass/dbname if your MySQL configuration differs.
|--------------------------------------------------------------------------*/

class Database
{
    private static $instance = null;
    private $connection;

    /*--------------------------------------------------------------------------
    | Private constructor (singleton pattern)
    |--------------------------------------------------------------------------*/
    private function __construct()
    {
        $host     = 'localhost';
        $user     = 'root';
        $password = '';              // empty string, not null (XAMPP default)
        $dbname   = 'portal';

        $this->connection = new mysqli($host, $user, $password, $dbname);

        if ($this->connection->connect_error) {
            die("Database connection failed: " . $this->connection->connect_error);
        }

        // Use utf8mb4 for full Unicode support (Arabic, emoji, etc.)
        $this->connection->set_charset("utf8mb4");
    }

    /*--------------------------------------------------------------------------
    | Get singleton instance
    |--------------------------------------------------------------------------*/
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /*--------------------------------------------------------------------------
    | Get the underlying mysqli connection
    |--------------------------------------------------------------------------*/
    public function getConnection()
    {
        return $this->connection;
    }
}

/*--------------------------------------------------------------------------
| Global connection for procedural-style usage ($connection)
|--------------------------------------------------------------------------*/
$db         = Database::getInstance();
$connection = $db->getConnection();

/*--------------------------------------------------------------------------
| Optional: close connection on script end (not strictly needed).
|--------------------------------------------------------------------------*/
function close_DB()
{
    global $connection;
    if ($connection instanceof mysqli) {
        mysqli_close($connection);
    }
}
?>
