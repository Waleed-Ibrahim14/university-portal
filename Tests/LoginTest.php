<?php
use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase
{
    private $connection;

    protected function setUp(): void
    {
        $this->connection = new mysqli("localhost", "root", "", "portal");
    }

    public function testValidLogin()
    {
        // Test valid login
        $email = "waleed.it13@email.com";
        $password = "hd@3mng42$@53$#@%";

        // Mock the POST data
        $_POST['email'] = $email;
        $_POST['password'] = $password;

       include "../public/login.php";

        // Assert that the session variables are set correctly
        $this->assertEquals($_SESSION['email'], $email);
        $this->assertEquals($_SESSION['user_status'], "active");
        // Add more assertions as needed
    }

    public function testInvalidLogin()
    {
        // Test invalid login
        $email = "invalid@email.com";
        $password = "wrongpassword";

        // Mock the POST data
        $_POST['email'] = $email;
        $_POST['password'] = $password;

        // Include your login code here
        // Example: include "login.php";

        // Assert that the session variables are not set
        $this->assertArrayNotHasKey('email', $_SESSION);
        $this->assertArrayNotHasKey('user_status', $_SESSION);
        // Add more assertions as needed
    }

    // Add more test methods for edge cases, blocked accounts, etc.
}
