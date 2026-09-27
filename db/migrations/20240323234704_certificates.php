<?php
declare(strict_types=1);
use Phinx\Migration\AbstractMigration;

final class Certificates extends AbstractMigration{
/*--------------------------------------------------------------------------
| Create certificates Table ::
|
| CHANGES:
|   - `teacher_name VARCHAR(50)` replaced by `teacher_id INT FK → users.id`
|   - Added `course_id INT FK → courses.id`
|   - All FKs use ON DELETE SET NULL to preserve historical records:
|       * student_id: a certificate is a historical document (graduation proof)
|       * teacher_id: the certificate was issued by this teacher
|       * course_id:  the certificate is for this course
|     Deleting a user or course should NEVER silently erase a certificate.
|--------------------------------------------------------------------------*/
    public function up(){
        $this->execute("CREATE TABLE certificates (
                id INT AUTO_INCREMENT PRIMARY KEY,
                student_id INT NULL,
                certificate_name VARCHAR(255) NOT NULL,
                issue_date DATE NOT NULL,
                teacher_id INT NULL,
                course_id INT NULL,
                certificate_file TEXT NOT NULL,
                FOREIGN KEY (student_id) REFERENCES users(id)
                    ON UPDATE CASCADE ON DELETE SET NULL,
                FOREIGN KEY (teacher_id) REFERENCES users(id)
                    ON UPDATE CASCADE ON DELETE SET NULL,
                FOREIGN KEY (course_id) REFERENCES courses(id)
                    ON UPDATE CASCADE ON DELETE SET NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP);
        "); 
    }

    public function down(){
         /*--------------------------------------------------------------------------
        | Drop the certificates table.
        | NOTE: Foreign keys are dropped automatically because they belong to
        |       this table. No need to drop them explicitly.
        |--------------------------------------------------------------------------*/
        $this->execute("DROP TABLE certificates");
    }
}
