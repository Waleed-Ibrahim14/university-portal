<?php
declare(strict_types=1);
use Phinx\Migration\AbstractMigration;

final class Schedule extends AbstractMigration{
/*--------------------------------------------------------------------------
| Create schedule Table ::
|
| CHANGES:
|   - `teacher_id` FK changed from ON DELETE CASCADE to ON DELETE SET NULL
|   - Rationale: a schedule entry is a planning/administrative record.
|     If a teacher is removed, the schedule entry should NOT silently
|     disappear — it should remain with teacher_id = NULL so we keep
|     the historical timetable record (which can later be reassigned).
|
| NOTE (known design limitation, NOT fixed here):
|   - `user_id VARCHAR(50)` and `class_id VARCHAR(50)` are text columns
|     with no FK constraints. Ideally they should be:
|       user_id  → INT FK → users.id   (the student)
|       class_id → INT FK → classes.id
|     This is left as-is to avoid scope creep (would require code changes
|     in any file that reads/writes schedule), and documented as a finding.
|--------------------------------------------------------------------------*/
    public function up(){
        $this->execute("CREATE TABLE schedule (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id VARCHAR(50) NOT NULL,
                class_id VARCHAR(50) NOT NULL,
                teacher_id INT NULL,
                FOREIGN KEY (teacher_id) REFERENCES users(id)
                    ON UPDATE CASCADE ON DELETE SET NULL,
                time_slot VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP);
        "); 
    }

    public function down(){
         /*--------------------------------------------------------------------------
        | Drop the schedule table.
        | NOTE: Foreign keys are dropped automatically because they belong to
        |       this table. No need to drop them explicitly.
        |--------------------------------------------------------------------------*/
        $this->execute("DROP TABLE schedule");
    }
}
