<?php
declare(strict_types=1);
use Phinx\Migration\AbstractMigration;

final class Courses extends AbstractMigration{
/*--------------------------------------------------------------------------
| Create courses Table ::
|--------------------------------------------------------------------------*/
    public function up(){
        $this->execute("CREATE TABLE courses (
                id INT AUTO_INCREMENT PRIMARY KEY,
                course_name VARCHAR(255) NOT NULL,
                teacher_id INT,
                FOREIGN KEY (teacher_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP);
        "); 
/*--------------------------------------------------------------------------
| Insert Some Initial Data ::
|--------------------------------------------------------------------------*/       
       $this->execute("INSERT INTO courses (course_name,teacher_id)
        VALUES  ('Artificial intelligence','3'),('Software engineering','3'),('Smart systems', '3')");
    
    }

    public function down(){ }
}