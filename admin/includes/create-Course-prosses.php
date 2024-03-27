<?php 	
/*--------------------------------------------------------------------------
| Create Course Prosses::
|--------------------------------------------------------------------------*/
include_once("../Models/DataBaseConnection.php");
    $message = '';

if (isset($_POST['submit'])) { 
    $course_name = $_POST['course_name'];	
    $teacher_id = $_POST['teacher_id'];
    if(empty($course_name)){
        echo $message = '<div class="alert alert-danger" role="alert">Enter the course name</div>';
    }else if(empty($teacher_id)){
        echo $message = '<div class="alert alert-danger" role="alert">select teacher</div>';
    }else{
        $stmt =  "INSERT INTO `courses` (`course_name`,`teacher_id`,`created_at`,`updated_at`)
                    VALUES ('$course_name','$teacher_id',NOW(),NOW())";
            mysqli_query($connection,$stmt)or die(mysqli_error($connection)) ;
        if($stmt) {
            echo $message = '<div class="alert alert-success" role="alert">course  Saved successfully</div>';
        } else {
            echo $message = '<div class="alert alert-danger" role="alert">course  error save data</div>';
        }
    }	
} 
?>