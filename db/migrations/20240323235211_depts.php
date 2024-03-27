<?php
declare(strict_types=1);
use Phinx\Migration\AbstractMigration;

final class Depts extends AbstractMigration{
/*--------------------------------------------------------------------------
| Create Depts Table ::
|--------------------------------------------------------------------------*/
    public function up(){
        $this->execute("CREATE TABLE debts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                student_id INT,
                amount DECIMAL(10, 2),
                reason TEXT NOT NULL,
                date DATE NOT NULL,
                FOREIGN KEY (student_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
        "); 
    }
    public function down(){ }
}