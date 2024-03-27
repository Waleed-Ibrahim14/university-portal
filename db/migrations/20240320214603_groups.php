<?php
declare(strict_types=1);
use Phinx\Migration\AbstractMigration;

final class Groups extends AbstractMigration{
/*--------------------------------------------------------------------------
| Create groups Table ::
|--------------------------------------------------------------------------*/
    public function up(){
        $this->execute("CREATE TABLE groups (
                id INT AUTO_INCREMENT PRIMARY KEY,
                group_name VARCHAR(50) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP);
        "); 

/*--------------------------------------------------------------------------
| Insert Some Initial Data ::
|--------------------------------------------------------------------------*/
        $this->execute("INSERT INTO groups (group_name) VALUES  ('group 1'),('group 2'),('group 3')");
    
    }
    public function down(){ }
}