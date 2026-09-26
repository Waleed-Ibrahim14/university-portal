<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../admin/Models/DataBaseConnection.php';

class ReadTest extends TestCase
{
    private $mysqli;

    protected function setUp(): void
    {
        $db = Database::getInstance();
        $this->mysqli = $db->getConnection();

        // Ensure test user exists
        $cleanup = $this->mysqli->prepare("DELETE FROM users WHERE username = ?");
        $testUser = 'testuser';
        $cleanup->bind_param("s", $testUser);
        $cleanup->execute();
        $cleanup->close();

        $passwordHash = password_hash('testpassword123', PASSWORD_DEFAULT);
        $fullname = 'Test User';
        $country = 'SD';
        $gender = 'male';
        $email = 'testuser@example.com';
        $profile = 'default.png';
        $userStatus = 'active';

        $insert = $this->mysqli->prepare(
            "INSERT INTO users (fullname, country, gender, username, email, password, profile, user_status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $insert->bind_param(
            "ssssssss",
            $fullname, $country, $gender, $testUser, $email, $passwordHash, $profile, $userStatus
        );
        $insert->execute();
        $insert->close();
    }

    protected function tearDown(): void
    {
        $cleanup = $this->mysqli->prepare("DELETE FROM users WHERE username = ?");
        $testUser = 'testuser';
        $cleanup->bind_param("s", $testUser);
        $cleanup->execute();
        $cleanup->close();
    }

    public function testRead(): void
    {
        $sql_query = "SELECT * FROM users WHERE username = ?";
        $stmt = $this->mysqli->prepare($sql_query);
        $testUser = 'testuser';
        $stmt->bind_param("s", $testUser);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        $this->assertNotNull($user, 'Test user was not found.');
        $this->assertEquals('testuser', $user['username']);
        $this->assertEquals('testuser@example.com', $user['email']);
    }
}
?>
