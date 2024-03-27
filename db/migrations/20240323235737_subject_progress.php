<?php
declare(strict_types=1);
use Phinx\Migration\AbstractMigration;

final class SubjectProgress extends AbstractMigration{
/*--------------------------------------------------------------------------
| Create subject_progress Table ::
|--------------------------------------------------------------------------*/
    public function up(){
        $this->execute("CREATE TABLE subject_progress (
                id INT AUTO_INCREMENT PRIMARY KEY,
                teacher_id INT,
                subject_id INT,
                progress_notes VARCHAR(50) NOT NULL,
                FOREIGN KEY (teacher_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE,
                FOREIGN KEY (subject_id) REFERENCES student_performance(id) ON UPDATE CASCADE ON DELETE CASCADE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP);
        "); 
    }
    public function down(){ }
}