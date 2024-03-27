<?php
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

class MigrationTest extends TestCase{
    
    protected $pdo;
    public function setUp(): void{
        // Initialize PDO to connect with database
        $this->pdo = new PDO('mysql:host=localhost;dbname=university_portal', 'root', '');

        // exiqute migrations
        $phinx = new \Phinx\Console\PhinxApplication();
        $phinx->run(new StringInput('migrate'), new NullOutput());
    }

    public function testUsersTableExists()
    {
        $stmt = $this->pdo->query("SELECT scholarship_name FROM scholarships");
        $this->assertNotFalse($stmt, 'Failed to execute migration');
        $this->assertEquals(1, $stmt->rowCount());
    }
    
}
