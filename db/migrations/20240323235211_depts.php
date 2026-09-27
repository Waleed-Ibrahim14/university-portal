<?php
declare(strict_types=1);
use Phinx\Migration\AbstractMigration;

final class Depts extends AbstractMigration{
/*--------------------------------------------------------------------------
| Create debts Table ::
|
| NOTE ON NAMING:
|   - The class is called "Depts" but creates a table named `debts`.
|     In this project, "Depts" is short for "Debts" (student financial
|     debt), NOT "Departments". This was confirmed by the author.
|
| CHANGES:
|   - `student_id` FK changed from ON DELETE CASCADE to ON DELETE SET NULL
|   - Rationale: a debt record is a FINANCIAL/HISTORICAL document.
|     If a student is removed from the system, their debt record should
|     NOT be silently deleted — it must be preserved for financial/legal
|     reasons (debt may be settled, transferred, or audited later).
|
| NOTE (known design limitation, NOT fixed here):
|   - `amount DECIMAL(10,2)` allows NULL; ideally it should be NOT NULL
|     with a default of 0.00. Left as-is to preserve original behavior.
|--------------------------------------------------------------------------*/
    public function up(){
        $this->execute("CREATE TABLE debts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                student_id INT NULL,
                amount DECIMAL(10, 2),
                reason TEXT NOT NULL,
                date DATE NOT NULL,
                FOREIGN KEY (student_id) REFERENCES users(id)
                    ON UPDATE CASCADE ON DELETE SET NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
        "); 
    }

    public function down(){
         /*--------------------------------------------------------------------------
        | Drop the debts table.
        | NOTE: Foreign keys are dropped automatically because they belong to
        |       this table. No need to drop them explicitly.
        |--------------------------------------------------------------------------*/
        $this->execute("DROP TABLE debts");
    }
}
