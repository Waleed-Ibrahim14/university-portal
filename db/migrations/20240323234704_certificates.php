<?php
declare(strict_types=1);
use Phinx\Migration\AbstractMigration;

final class Certificates extends AbstractMigration{
/*--------------------------------------------------------------------------
| Create certificates Table ::
|--------------------------------------------------------------------------*/
    public function up(){
        $this->execute("CREATE TABLE certificates (
                id INT AUTO_INCREMENT PRIMARY KEY,
                student_id INT,
                certificate_name VARCHAR(255) NOT NULL,
                issue_date DATE NOT NULL,
                teacher_name VARCHAR(50) NOT NULL,
                certificate_file TEXT NOT NULL,
                FOREIGN KEY (student_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP);
        "); 
    }
    public function down(){ }
}