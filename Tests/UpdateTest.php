<?php
use PHPUnit\Framework\TestCase;

class UpdateTest extends TestCase
{
    public function testUpdate()
    {
        $db = Database::getInstance();
        $mysqli = $db->getConnection(); 

        $sql_query = "UPDATE users SET email = ? WHERE username = ?";
        $stmt = $mysqli->prepare($sql_query);
        $stmt->bind_param("ss", 'newemail@example.com', 'testuser');
        $stmt->execute();

        $this->assertEquals(1, $stmt->affected_rows);
    }
}
?>