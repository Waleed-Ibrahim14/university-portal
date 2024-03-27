<?php
declare(strict_types=1);
use Phinx\Migration\AbstractMigration;

final class Referrals extends AbstractMigration{
/*--------------------------------------------------------------------------
| Create referrals Table ::
|--------------------------------------------------------------------------*/
    public function up(){
        $this->execute("CREATE TABLE referrals (
                id INT AUTO_INCREMENT PRIMARY KEY,
                student_id INT,
                referral_reason TEXT NOT NULL,
                date DATE NOT NULL,
                FOREIGN KEY (student_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP);
        "); 
    }
    public function down(){ }
}