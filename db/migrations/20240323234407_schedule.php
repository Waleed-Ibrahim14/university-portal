<?php
declare(strict_types=1);
use Phinx\Migration\AbstractMigration;

final class Schedule extends AbstractMigration{
/*--------------------------------------------------------------------------
| Create schedule Table ::
|--------------------------------------------------------------------------*/
    public function up(){
        $this->execute("CREATE TABLE schedule (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id VARCHAR(50) NOT NULL,
                class_id VARCHAR(50) NOT NULL,
                teacher_id INT,
                FOREIGN KEY (teacher_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE,
                time_slot VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP);
        "); 
    }

    public function down(){ }
}