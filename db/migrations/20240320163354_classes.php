<?php
declare(strict_types=1);
use Phinx\Migration\AbstractMigration;

final class Classes extends AbstractMigration{
/*--------------------------------------------------------------------------
| Create classes Table ::
|
| CHANGES:
|   - Added `teacher_id INT FK → users.id` (a class is taught by a teacher)
|   - FK uses ON DELETE SET NULL to preserve class records if teacher is removed
|   - Note: students are NOT linked here directly; the many-to-many relation
|     is handled via `student_courses`
|--------------------------------------------------------------------------*/
    public function up(){
        $this->execute("CREATE TABLE classes (
                id INT AUTO_INCREMENT PRIMARY KEY,
                class_name VARCHAR(50) NOT NULL,
                teacher_id INT NULL,
                FOREIGN KEY (teacher_id) REFERENCES users(id)
                    ON UPDATE CASCADE ON DELETE SET NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP);
            ");     
    }

    public function down(){
         /*--------------------------------------------------------------------------
        | Drop the classes table.
        | NOTE: Foreign keys are dropped automatically because they belong to
        |       this table. No need to drop them explicitly.
        |--------------------------------------------------------------------------*/
        $this->execute("DROP TABLE classes");
    }
}
