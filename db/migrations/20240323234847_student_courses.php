<?php
declare(strict_types=1);
use Phinx\Migration\AbstractMigration;

final class StudentCourses extends AbstractMigration{
/*--------------------------------------------------------------------------
| Create student_courses Table ::
|
| CHANGES:
|   - FKs restored to ON DELETE CASCADE (was SET NULL in previous revision)
|   - Rationale: this is a JUNCTION table (many-to-many between students
|     and courses). A row here is a RELATIONSHIP, not a historical record.
|     A row with student_id = NULL or course_id = NULL is semantically
|     meaningless (an "enrollment with no student" or "enrollment with no
|     course") — it is CORRUPT DATA, not preserved data.
|   - When a student or a course is removed, the enrollment relation
|     should disappear with it.
|   - Column made explicitly NULL only for consistency (CASCADE does not
|     require NULL, but explicit is better than implicit).
|--------------------------------------------------------------------------*/
    public function up(){
        $this->execute("CREATE TABLE student_courses (
                id INT AUTO_INCREMENT PRIMARY KEY,
                student_id INT NULL,
                course_id INT NULL,
                FOREIGN KEY (student_id) REFERENCES users(id)
                    ON UPDATE CASCADE ON DELETE CASCADE,
                FOREIGN KEY (course_id) REFERENCES courses(id)
                    ON UPDATE CASCADE ON DELETE CASCADE,
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
