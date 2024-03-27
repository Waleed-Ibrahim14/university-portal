<?php
use PHPUnit\Framework\TestCase;

class ReadTest extends TestCase
{
    public function testRead()
    {
        $db = Database::getInstance();
        $mysqli = $db->getConnection(); 

        $sql_query = "SELECT * FROM users WHERE username = ?";
        $stmt = $mysqli->prepare($sql_query);
        $stmt->bind_param("s", 'testuser');
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        $this->assertEquals('testuser', $user['username']);
        $this->assertEquals('testuser@example.com', $user['email']);
    }
}
?>