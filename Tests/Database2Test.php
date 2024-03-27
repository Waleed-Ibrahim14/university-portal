<?php
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
require_once 'admin/Models/DataBaseConnection.php';

class Database2Test extends TestCase{

    protected $pdo;
    public function testConnection(){

        // initialize pdo to connect to database
    $this->pdo = new PDO('mysql:host=localhost;dbname=portal', 'root', '');

      //execute migrations
      $phinx = new \Phinx\Console\PhinxApplication();
      $phinx->run(new StringInput('migrate'), new NullOutput());

  }
    public function testUsersTableExists(){
        $stmt = $this->pdo->query("SHOW TABLES LIKE 'users'");
        $this->assertNotFalse($stmt, 'Failed to execute migration');
        $this->assertEquals(1, $stmt->rowCount());
        return true;
    }
}
?>
