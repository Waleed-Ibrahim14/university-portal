<?php
declare(strict_types=1);
use Phinx\Migration\AbstractMigration;

final class Users extends AbstractMigration{
/*--------------------------------------------------------------------------
| Create users Table ::
|--------------------------------------------------------------------------*/
    public function up(){
        $this->execute("CREATE TABLE users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                fullname VARCHAR(50) NOT NULL,
                country VARCHAR(50) NOT NULL,
                gender VARCHAR(50) NOT NULL,
                username VARCHAR(50) NOT NULL,
                email VARCHAR(255) NOT NULL,
                password VARCHAR(255) NOT NULL,
                profile text NOT NULL,
                role_id INT NULL,
                user_status VARCHAR(10) NOT NULL,
                scholarship_id INT NULL,
                group_id INT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (role_id)        REFERENCES roles(id)         ON DELETE SET NULL,
                FOREIGN KEY (scholarship_id) REFERENCES scholarships(id)  ON DELETE SET NULL,
                FOREIGN KEY (group_id)       REFERENCES groups(id)        ON DELETE SET NULL
            )");

/*--------------------------------------------------------------------------
    | Insert Some Initial Data ::
    | NOTE: Role IDs are resolved in PHP first (not inside the SQL VALUES clause),
    |       because MySQL does not reliably allow subqueries inside VALUES.
    |--------------------------------------------------------------------------*/
    $adminpassword    = password_hash('admin',   PASSWORD_DEFAULT);
    $userpassword     = password_hash('user',    PASSWORD_DEFAULT);
    $teacherpassword  = password_hash('teacher', PASSWORD_DEFAULT);

    // Resolve role IDs safely in PHP
    $adminRole   = $this->fetchRow("SELECT id FROM roles WHERE role_name = 'admin'   LIMIT 1");
    $userRole    = $this->fetchRow("SELECT id FROM roles WHERE role_name = 'user'    LIMIT 1");
    $teacherRole = $this->fetchRow("SELECT id FROM roles WHERE role_name = 'teacher' LIMIT 1");

    $adminRoleId   = $adminRole['id']   ?? 'NULL';
    $userRoleId    = $userRole['id']    ?? 'NULL';
    $teacherRoleId = $teacherRole['id'] ?? 'NULL';

    $this->execute("INSERT INTO users 
        (fullname, country, gender, username, email, password, profile, 
         role_id, user_status, scholarship_id, group_id) 
    VALUES  
        ('Waleed Ibrahim', 'sudan', 'male',   'waleed',  'waleed.it13@gmail.com', '$adminpassword',   '../../assets/images/users/edu.png', 
         $adminRoleId,   'active',  NULL, NULL),
        ('First User',     'sudan', 'female', 'user',    'user@gmail.com',        '$userpassword',    '../../assets/images/users/edu.png', 
         $userRoleId,    'blocked', NULL, NULL),
        ('First Teacher',  'sudan', 'male',   'teacher', 'teacher@gmail.com',     '$teacherpassword', '../../assets/images/users/edu.png', 
         $teacherRoleId, 'blocked', NULL, NULL)
    ");
}

    public function down(){
        /*--------------------------------------------------------------------------
        | Drop the users table.
        | NOTE: Foreign keys are dropped automatically because they belong to
        |       this table. No need to drop them explicitly.
        |--------------------------------------------------------------------------*/
        $this->execute("DROP TABLE users");
    }
}
