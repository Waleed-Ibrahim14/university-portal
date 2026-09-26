<?php
declare(strict_types=1);
use Phinx\Migration\AbstractMigration;

final class Scholarships extends AbstractMigration{
/*--------------------------------------------------------------------------
| Create Scholarships Table ::
|--------------------------------------------------------------------------*/
    public function up(){
        $this->execute("CREATE TABLE scholarships (
                id INT AUTO_INCREMENT PRIMARY KEY,
                scholarship_name VARCHAR(255) NOT NULL,
                image TEXT NOT NULL,
                scholarship_description TEXT NOT NULL,
                amount DECIMAL(10, 2)  NOT NULL,
                date DATE  NOT NULL,
                scholarship_status VARCHAR(20) NOT NULL,
                added_by VARCHAR(50) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP);
        ");
        $this->execute("INSERT INTO scholarships 
    (scholarship_name, image, scholarship_description, amount, date, scholarship_status, added_by) 
VALUES 
    ('Scholarship A', '', 'Description A', 1000.00, '2024-01-01', 'active', 'admin'),
    ('Scholarship B', '', 'Description B', 2000.00, '2024-01-01', 'active', 'admin')
");
    }
    public function down(){
        $this->execute("DROP TABLE scholarships");
    }
}
