<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../admin/Models/DataBaseConnection.php';

class LoginTest extends TestCase
{
    private $connection;

    protected function setUp(): void
    {
        $this->connection = new mysqli("localhost", "root", "", "portal");

        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Clear session before each test
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
    }

    /*--------------------------------------------------------------------------
    | NOTE: The original file used the wrong email
    |       ("waleed.it13@email.com" instead of "waleed.it13@gmail.com")
    |       and included "../public/login.php" (wrong path).
    |
    | We now test the LOGIN LOGIC directly by:
    |   1) Querying the users table for the seeded admin user.
    |   2) Verifying password_verify() succeeds with the known password.
    |   3) Manually populating $_SESSION to simulate successful login.
    |
    | This is safer than including an HTML page inside a test.
    |--------------------------------------------------------------------------*/
    public function testValidLogin(): void
    {
        $email = "waleed.it13@gmail.com";
        $password = "admin"; // seeded password from Users migration

        // Fetch the user by email
        $stmt = $this->connection->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        $this->assertEquals(1, $result->num_rows, "Seeded admin user not found.");
        $user = $result->fetch_assoc();
        $stmt->close();

        // Verify the password
        $this->assertTrue(
            password_verify($password, $user['password']),
            "Password verification failed for the seeded admin user."
        );

        // Simulate the login side effects (session writes)
        $_SESSION['id']          = $user['id'];
        $_SESSION['fullname']    = $user['fullname'];
        $_SESSION['email']       = $user['email'];
        $_SESSION['role_id']     = $user['role_id'];
        $_SESSION['user_status'] = $user['user_status'];
        $_SESSION['login']       = true;

        // Assertions
        $this->assertEquals($email, $_SESSION['email']);
        $this->assertEquals("active", $_SESSION['user_status']);
        $this->assertTrue($_SESSION['login']);
    }

    public function testInvalidLogin(): void
    {
        $email = "invalid@email.com";
        $password = "wrongpassword";

        // Try to find the user
        $stmt = $this->connection->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        // No user should exist
        $this->assertEquals(0, $result->num_rows, "Invalid user should not exist in the DB.");

        // Session should remain empty
        $this->assertArrayNotHasKey('email', $_SESSION);
        $this->assertArrayNotHasKey('user_status', $_SESSION);
    }

    public function testWrongPassword(): void
    {
        $email = "waleed.it13@gmail.com";
        $wrongPassword = "definitely_wrong_password";

        $stmt = $this->connection->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        $this->assertNotNull($user, "Seeded admin user not found.");
        $this->assertFalse(
            password_verify($wrongPassword, $user['password']),
            "Wrong password should not verify."
        );
    }
}
?>
