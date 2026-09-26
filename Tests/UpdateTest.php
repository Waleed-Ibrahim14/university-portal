<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../admin/Models/DataBaseConnection.php';

class UpdateTest extends TestCase
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

    public function testUpdate(): void
    {
        $sql_query = "UPDATE users SET email = ? WHERE username = ?";
        $stmt = $this->mysqli->prepare($sql_query);
        $newEmail = 'newemail@example.com';
        $testUser = 'testuser';
        $stmt->bind_param("ss", $newEmail, $testUser);
        $stmt->execute();

        $this->assertEquals(1, $stmt->affected_rows, 'Expected exactly one row to be updated.');
        $stmt->close();
    }
}
?>
