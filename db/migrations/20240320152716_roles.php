<?php
declare(strict_types=1);
use Phinx\Migration\AbstractMigration;

final class Roles extends AbstractMigration{
/*--------------------------------------------------------------------------
| Create Roles Table ::
|--------------------------------------------------------------------------*/
    public function up(){
        $this->execute("CREATE TABLE roles (
                id INT AUTO_INCREMENT PRIMARY KEY,
                role_name VARCHAR(50) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP);
            ");
/*--------------------------------------------------------------------------
| Insert Some Initial Data ::
|--------------------------------------------------------------------------*/
        $this->execute("INSERT INTO roles (role_name) VALUES ('admin'),('user'),('teacher')");      
    }
    public function down(){ }
}