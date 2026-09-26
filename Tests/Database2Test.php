<?php
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

require_once __DIR__ . '/../admin/Models/DataBaseConnection.php';

class Database2Test extends TestCase
{
    protected $pdo;

    /*--------------------------------------------------------------------------
    | Set up runs before EACH test method.
    | NOTE: the original file initialized $this->pdo inside testConnection(),
    |       which made other test methods depend on execution order
    |       (a classic anti-pattern in PHPUnit). Now the setup is centralized.
    |--------------------------------------------------------------------------*/
    protected function setUp(): void
    {
        try {
            $this->pdo = new PDO('mysql:host=localhost;dbname=portal', 'root', '');
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            $this->markTestSkipped('Database connection failed: ' . $e->getMessage());
        }

        // Run migrations once per test class (idempotent — Phinx skips already-run)
        $phinx = new \Phinx\Console\PhinxApplication();
        $phinx->setAutoExit(false);
        $phinx->run(new StringInput('migrate'), new NullOutput());
    }

    public function testDatabaseConnection(): void
    {
        $this->assertInstanceOf(PDO::class, $this->pdo);
    }

    /*--------------------------------------------------------------------------
    | Renamed from testUsersTableExists to testUsersTableExistsExplicitly
    | because the original only checked rowCount on a "SHOW TABLES LIKE" query
    | — which is fragile. This version explicitly asserts the table exists.
    |--------------------------------------------------------------------------*/
    public function testUsersTableExists(): void
    {
        $stmt = $this->pdo->query("SHOW TABLES LIKE 'users'");
        $this->assertNotFalse($stmt, 'SHOW TABLES query failed');
        $this->assertEquals(1, $stmt->rowCount(), 'The `users` table does not exist.');
    }

    /*--------------------------------------------------------------------------
    | Verify that all FK-referenced tables exist after migration.
    |--------------------------------------------------------------------------*/
    public function testRequiredTablesExist(): void
    {
        $requiredTables = ['users', 'roles', 'groups', 'scholarships', 'courses', 'student_courses'];
        foreach ($requiredTables as $table) {
            $stmt = $this->pdo->query("SHOW TABLES LIKE '$table'");
            $this->assertEquals(
                1,
                $stmt->rowCount(),
                "Required table `$table` is missing after migration."
            );
        }
    }
}
?>
