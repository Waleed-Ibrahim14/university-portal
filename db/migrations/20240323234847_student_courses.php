<?php
declare(strict_types=1);
use Phinx\Migration\AbstractMigration;

final class StudentCourses extends AbstractMigration{
/*--------------------------------------------------------------------------
| Create student_courses Table ::
|--------------------------------------------------------------------------*/
    public function up(){
        $this->execute("CREATE TABLE student_courses (
                id INT AUTO_INCREMENT PRIMARY KEY,
                student_id INT NULL,
                course_id INT NULL,
                FOREIGN KEY (student_id) REFERENCES users(id) 
                    ON UPDATE CASCADE 
                    ON DELETE SET NULL,
                FOREIGN KEY (course_id) REFERENCES courses(id) 
                    ON UPDATE CASCADE 
                    ON DELETE SET NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP);
        "); 
    }

    public function down(){
         /*--------------------------------------------------------------------------
        | Drop the student_courses table.
        | NOTE: Foreign keys are dropped automatically because they belong to
        |       this table. No need to drop them explicitly.
        |--------------------------------------------------------------------------*/
        $this->execute("DROP TABLE student_courses");
    }
}
