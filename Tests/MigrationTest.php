<?php
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;

class MigrationTest extends TestCase
{
    protected $pdo;

    protected function setUp(): void
    {
        try {
            $this->pdo = new PDO('mysql:host=localhost;dbname=portal', 'root', '');
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            $this->markTestSkipped('Database connection failed: ' . $e->getMessage());
        }

        // Run migrations (idempotent)
        $phinx = new \Phinx\Console\PhinxApplication();
        $phinx->setAutoExit(false);
        $phinx->run(new StringInput('migrate'), new NullOutput());
    }

    /*--------------------------------------------------------------------------
    | RENAMED from `testUsersTableExists` to `testScholarshipsTableHasNameColumn`
    | because the original name did not match what the test actually checked.
    |--------------------------------------------------------------------------*/
    public function testScholarshipsTableHasNameColumn(): void
    {
        $stmt = $this->pdo->query("SELECT scholarship_name FROM scholarships LIMIT 1");
        $this->assertNotFalse($stmt, 'The `scholarship_name` column is missing from `scholarships`.');
    }

    /*--------------------------------------------------------------------------
    | Verify the new FK-based columns in the users table exist after migration.
    | This directly tests the schema refactor.
    |--------------------------------------------------------------------------*/
    public function testUsersTableHasForeignKeyColumns(): void
    {
        $requiredColumns = ['role_id', 'scholarship_id', 'group_id'];

        foreach ($requiredColumns as $column) {
            $stmt = $this->pdo->query("SHOW COLUMNS FROM users LIKE '$column'");
            $this->assertEquals(
                1,
                $stmt->rowCount(),
                "Required column `$column` is missing from `users`."
            );
        }
    }

    /*--------------------------------------------------------------------------
    | Verify that the removed text columns no longer exist.
    |--------------------------------------------------------------------------*/
    public function testUsersTableDoesNotHaveLegacyColumns(): void
    {
        $removedColumns = ['role', 'scholarship_name', 'group_name'];

        foreach ($removedColumns as $column) {
            $stmt = $this->pdo->query("SHOW COLUMNS FROM users LIKE '$column'");
            $this->assertEquals(
                0,
                $stmt->rowCount(),
                "Legacy column `$column` should have been removed from `users`."
            );
        }
    }
}
?>
