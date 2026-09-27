<?php
declare(strict_types=1);
use Phinx\Migration\AbstractMigration;

final class StudentPerformance extends AbstractMigration{
/*--------------------------------------------------------------------------
| Create student_performance Table ::
|
| CHANGES:
|   - FK changed from ON DELETE CASCADE to ON DELETE SET NULL
|   - Rationale: a student's grade record is a historical document.
|     If the student is later removed from the system, their grades
|     should be preserved (not silently deleted).
|   - Column made explicitly NULL to support SET NULL.
|--------------------------------------------------------------------------*/
    public function up(){
        $this->execute("CREATE TABLE student_performance (
                id INT AUTO_INCREMENT PRIMARY KEY,
                student_id INT NULL,
                subject_name VARCHAR(50) NOT NULL,
                grade CHAR(1) NOT NULL,
                FOREIGN KEY (student_id) REFERENCES users(id)
                    ON UPDATE CASCADE ON DELETE SET NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP);
        "); 
    }

    public function down(){
         /*--------------------------------------------------------------------------
        | Drop the student_performance table.
        | NOTE: Foreign keys are dropped automatically because they belong to
        |       this table. No need to drop them explicitly.
        |--------------------------------------------------------------------------*/
        $this->execute("DROP TABLE student_performance");
    }
}
