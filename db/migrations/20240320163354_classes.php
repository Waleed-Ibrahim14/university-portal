<?php
declare(strict_types=1);
use Phinx\Migration\AbstractMigration;

final class Classes extends AbstractMigration{
/*--------------------------------------------------------------------------
| Create classes Table ::
|--------------------------------------------------------------------------*/
    public function up(){
        $this->execute("CREATE TABLE classes (
                id INT AUTO_INCREMENT PRIMARY KEY,
                class_name VARCHAR(50) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP);
            ");     
    }
    public function down(){
         /*--------------------------------------------------------------------------
        | Drop the users table.
        | NOTE: Foreign keys are dropped automatically because they belong to
        |       this table. No need to drop them explicitly.
        |--------------------------------------------------------------------------*/
        $this->execute("DROP TABLE classes");
    }
}
