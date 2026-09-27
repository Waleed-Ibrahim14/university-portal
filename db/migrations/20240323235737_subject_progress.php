<?php
declare(strict_types=1);
use Phinx\Migration\AbstractMigration;

final class SubjectProgress extends AbstractMigration{
/*--------------------------------------------------------------------------
| Create subject_progress Table ::
|
| CHANGES:
|   1. `subject_id` RENAMED to `performance_id`
|      Reason: the column was misleadingly named. It does NOT point to a
|      `subjects` table (which doesn't exist). It points to
|      `student_performance.id` — i.e., it references a specific GRADE
|      record, not a subject. The new name reflects the truth.
|
|   2. `teacher_id` FK changed from CASCADE to SET NULL
|      Rationale: progress notes about a student are a HISTORICAL record.
|      If the teacher is removed, the notes should remain (with
|      teacher_id = NULL), because they document the student's academic
|      journey regardless of who recorded them.
|
|   3. `performance_id` FK kept as CASCADE
|      Rationale: progress notes are META-COMMENTARY about a specific
|      grade record. Without that grade, the notes have no anchor — they
|      become dangling commentary with no context. This is semantically
|      meaningless, so CASCADE is correct here (the notes disappear with
|      the grade they describe).
|--------------------------------------------------------------------------*/
    public function up(){
        $this->execute("CREATE TABLE subject_progress (
                id INT AUTO_INCREMENT PRIMARY KEY,
                teacher_id INT NULL,
                performance_id INT NULL,
                progress_notes VARCHAR(50) NOT NULL,
                FOREIGN KEY (teacher_id) REFERENCES users(id)
                    ON UPDATE CASCADE ON DELETE SET NULL,
                FOREIGN KEY (performance_id) REFERENCES student_performance(id)
                    ON UPDATE CASCADE ON DELETE CASCADE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP);
        "); 
    }

    public function down(){
         /*--------------------------------------------------------------------------
        | Drop the subject_progress table.
        | NOTE: Foreign keys are dropped automatically because they belong to
        |       this table. No need to drop them explicitly.
        |--------------------------------------------------------------------------*/
        $this->execute("DROP TABLE subject_progress");
    }
}
