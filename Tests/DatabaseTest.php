<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../admin/Models/DataBaseConnection.php';

class DatabaseTest extends TestCase
{
    public function testConnection(): void
    {
        $db = Database::getInstance();
        $connection = $db->getConnection();

        // Check connection is not null
        $this->assertNotNull($connection);

        // Check connection is an instance of mysqli
        $this->assertInstanceOf(mysqli::class, $connection);
    }

    /*--------------------------------------------------------------------------
    | Additional test: verify the connection actually works (not just exists).
    |--------------------------------------------------------------------------*/
    public function testConnectionIsAlive(): void
    {
        $db = Database::getInstance();
        $connection = $db->getConnection();

        $result = $connection->query("SELECT 1 AS ping");
        $this->assertNotFalse($result, 'The connection is not responding.');

        $row = $result->fetch_assoc();
        $this->assertEquals(1, (int)$row['ping']);
    }
}
?>
