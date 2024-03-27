<?php
use PHPUnit\Framework\TestCase;
require_once 'admin/Models/DataBaseConnection.php';

class DatabaseTest extends TestCase
{
    public function testConnection()
    {
        $db = Database::getInstance();
        $connection = $db->getConnection();

        // check connection not null
        $this->assertNotNull($connection);

        // check connection its object of mysqli
        $this->assertInstanceOf(mysqli::class, $connection);
    }
}
?>
