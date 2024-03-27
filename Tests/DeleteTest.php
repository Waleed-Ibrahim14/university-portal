<?php
use PHPUnit\Framework\TestCase;

class DeleteTest extends TestCase
{
    public function testDelete()
    {
        $db = Database::getInstance();
        $mysqli = $db->getConnection(); 

        $sql_query = "DELETE FROM users WHERE username = ?";
        $stmt = $mysqli->prepare($sql_query);
        $stmt->bind_param("s", 'testuser');
        $stmt->execute();

        $this->assertEquals(1, $stmt->affected_rows);
    }
}
?>