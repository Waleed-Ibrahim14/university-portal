بالطبع، يمكنك إنشاء اختبارات وحدة لكل من عمليات CRUD. في الأدنى، يمكن أن تكون الاختبارات بسيطة مثل محاولة تنفيذ كل عملية والتحقق من أن النتائج هي كما هو متوقع.

إليك كيف يمكنك القيام بذلك باستخدام PHPUnit:

CreateTest.php:
PHP

<?php
use PHPUnit\Framework\TestCase;

class CreateTest extends TestCase
{
    public function testCreate()
    {
        $db = Database::getInstance();
        $mysqli = $db->getConnection(); 

        $sql_query = "INSERT INTO users (username, email) VALUES (?, ?)";
        $stmt = $mysqli->prepare($sql_query);
        $stmt->bind_param("ss", 'testuser', 'testuser@example.com');
        $stmt->execute();

        $this->assertEquals(1, $stmt->affected_rows);
    }
}
AI-generated code. Review and use carefully. More info on FAQ.
ReadTest.php:
PHP

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
AI-generated code. Review and use carefully. More info on FAQ.
UpdateTest.php:
PHP

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
AI-generated code. Review and use carefully. More info on FAQ.
DeleteTest.php:
PHP

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