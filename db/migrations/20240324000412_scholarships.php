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
                scholarship_status VARCHAR(10) NOT NULL,
                added_by VARCHAR(10) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP);
        "); 
    }
    public function down(){ }
}