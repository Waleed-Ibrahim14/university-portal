<?php
declare(strict_types=1);
use Phinx\Migration\AbstractMigration;

final class Announcements extends AbstractMigration{
/*--------------------------------------------------------------------------
| Create Announcements  Table ::
|--------------------------------------------------------------------------*/
    public function up(){
        $this->execute("CREATE TABLE announcements (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL, 
                notif_msg TEXT NOT NULL, 
                notif_time DATETIME NULL, 
                notif_repeat INT(11) NULL DEFAULT 1,
                notif_loop INT(11) NULL DEFAULT 1, 
                publish_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                username VARCHAR(255) NOT NULL ,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP);
        "); 
    }
    public function down(){ }
}