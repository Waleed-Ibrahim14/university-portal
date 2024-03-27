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
                student_id INT,
                course_id INT,
                FOREIGN KEY (student_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE,
                FOREIGN KEY (course_id) REFERENCES courses(id) ON UPDATE CASCADE ON DELETE CASCADE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP);
        "); 
    }

    public function down(){ }
}