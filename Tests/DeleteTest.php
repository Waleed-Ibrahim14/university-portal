<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../admin/Models/DataBaseConnection.php';

class DeleteTest extends TestCase
{
    private $mysqli;

    /*--------------------------------------------------------------------------
    | Set up: ensure the test user exists before running the delete test.
    | NOTE: The original test assumed 'testuser' already existed in the DB.
    |       This made it fail on a fresh database. We now create it in setUp().
    |--------------------------------------------------------------------------*/
    protected function setUp(): void
    {
        $db = Database::getInstance();
        $this->mysqli = $db->getConnection();

        // Clean up any leftover test user
        $cleanup = $this->mysqli->prepare("DELETE FROM users WHERE username = ?");
        $testUser = 'testuser';
        $cleanup->bind_param("s", $testUser);
        $cleanup->execute();
        $cleanup->close();

        // Insert a fresh test user
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

    /*--------------------------------------------------------------------------
    | Tear down: clean up after tests.
    |--------------------------------------------------------------------------*/
    protected function tearDown(): void
    {
        $cleanup = $this->mysqli->prepare("DELETE FROM users WHERE username = ?");
        $testUser = 'testuser';
        $cleanup->bind_param("s", $testUser);
        $cleanup->execute();
        $cleanup->close();
    }

    public function testDelete(): void
    {
        $sql_query = "DELETE FROM users WHERE username = ?";
        $stmt = $this->mysqli->prepare($sql_query);
        $testUser = 'testuser';
        $stmt->bind_param("s", $testUser);
        $stmt->execute();

        $this->assertEquals(1, $stmt->affected_rows, 'Expected exactly one user to be deleted.');
        $stmt->close();
    }
}
?>
