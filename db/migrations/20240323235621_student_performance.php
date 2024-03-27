<?php
declare(strict_types=1);
use Phinx\Migration\AbstractMigration;

final class StudentPerformance extends AbstractMigration{
/*--------------------------------------------------------------------------
| Create student_performance  Table ::
|--------------------------------------------------------------------------*/
    public function up(){
        $this->execute("CREATE TABLE student_performance  (
                id INT AUTO_INCREMENT PRIMARY KEY,
                student_id INT,
                subject_name VARCHAR(50) NOT NULL,
                grade CHAR(1) NOT NULL,
                FOREIGN KEY (student_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP);
        "); 
    }
    public function down(){ }
}
