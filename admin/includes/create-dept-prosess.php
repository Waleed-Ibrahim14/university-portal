<?php 	
/*--------------------------------------------------------------------------
| Create Certificate Prosses::
|--------------------------------------------------------------------------*/
include_once("../Models/DataBaseConnection.php");
   $msg = '';
if (isset($_POST['submit'])) { 
      $student_id = $_POST['student_id'];	
      $amount = $_POST['amount']; 
      $date= $_POST['date']; 
      $reason= $_POST['reason']; 
         // form validation
         if(empty($student_id)){
         echo $msg = '<div class="alert alert-danger" role="alert">Select student id</div>';
         }else if(empty($amount)){
         echo $msg = '<div class="alert alert-danger" role="alert">Enter amount</div>';
         }else if(empty($date)){
         echo $msg = '<div class="alert alert-danger" role="alert">Enter The date </div>';
         }else if(empty($reason)){
            echo $msg = '<div class="alert alert-danger" role="alert">Enter The reason for depts </div>';
         }else{
            $insert = mysqli_query($connection, "INSERT INTO `debts` (`student_id`,`amount`,`date`,`reason`,`created_at`,`updated_at`)
                           VALUES ('$student_id','$amount','$date','$reason',NOW(),NOW())");
            if (isset($insert)) {
               $msg = '<div class="alert alert-success" role="alert">dept Createed Successfly</div>';
            }else{
               $msg = '<div class="alert alert-success" role="alert">An error occurred</div>';
            }
         }
	} 
?>