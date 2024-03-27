<?php 	
/*--------------------------------------------------------------------------
| Create group Prosses::
|--------------------------------------------------------------------------*/
include_once("../Models/DataBaseConnection.php");
    $message = '';

if (isset($_POST['submit'])) { 
    $group_name = $_POST['group_name'];	
    if(empty($group_name)){
        echo $message = '<div class="alert alert-danger" role="alert">Enter the group name</div>';
    }else{
        $stmt =  "INSERT INTO `groups` (`group_name`,`created_at`,`updated_at`)
                    VALUES ('$group_name',NOW(),NOW())";
            mysqli_query($connection,$stmt)or die(mysqli_error($connection)) ;
        if($stmt) {
            echo $message = '<div class="alert alert-success" role="alert">group  Saved successfully</div>';
        } else {
            echo $message = '<div class="alert alert-danger" role="alert">group  error save data</div>';
        }
    }	
} 
?>