<?php
use PHPUnit\Framework\TestCase;
require_once 'admin/Models/DataBaseConnection.php';

class DatabaseTest extends TestCase
{
    public function testConnection()
    {
        $db = Database::getInstance();
        $connection = $db->getConnection();

        // التحقق من أن الاتصال ليس فارغًا
        $this->assertNotNull($connection);

        // التحقق من أن الاتصال هو كائن من فئة mysqli
        $this->assertInstanceOf(mysqli::class, $connection);
    }
}
?>
