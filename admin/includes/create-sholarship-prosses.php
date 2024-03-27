<?php   
/*--------------------------------------------------------------------------
| Add Scholarship Prosses::
|--------------------------------------------------------------------------*/
   include_once("../Models/DataBaseConnection.php");
   if (isset($_POST['add_scholarship'])) {
      $msg = ''; //feeding messages
      $scholarship_name =  strip_tags($_POST['scholarship_name']);
      $scholarship_description  =  $_POST['scholarship_description']; 
      $amount  =  $_POST['amount'];
      $date  =  $_POST['date'];
      $scholarship_status  =  $_POST['scholarship_status'];
      $added_by = $_POST['added_by'];
      // Get image name
      $image = $_FILES['image']['name'];
      // image file directory
      $target = "../assets/images/scholarship/".basename($image);
      $imageFileType = strtolower(pathinfo($target,PATHINFO_EXTENSION));
      // Form Validation
      if (empty($scholarship_name)) {   $msg = '<div class="alert alert-danger" role="alert">Please Enter Scholarship name</div>';
         }else if (empty($image)) {   $msg = '<div class="alert alert-danger" role="alert">Please select Scholarship image</div>';
         }else if(empty($amount)) {   $msg = '<div class="alert alert-danger" role="alert">Please Enter Scholarship amount</div>';
         }else if(empty($date)) {   $msg = '<div class="alert alert-danger" role="alert">Please Enter Scholarship date</div>';
         }else if(empty($scholarship_status)) {   $msg = '<div class="alert alert-danger" role="alert">Please select Scholarship status</div>';
         }else if(empty($added_by)) {   $msg = '<div class="alert alert-danger" role="alert">Please select admin name status</div>';
         }elseif(empty($scholarship_description)) {   $msg = '<div class="alert alert-danger" role="alert">Please enter Scholarship Description</div>';
         // Check the image type
         }else if($imageFileType != "pdf" && $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
            $msg = '<div class="alert alert-danger" role="alert">Choose correct pictures</div>';
         // Check image size
         }else if ($_FILES["image"]["size"] > 50000000000000000000000) {
            $msg = '<div class="alert alert-danger" role="alert">The image size is too large</div>';
         // Verify whether this file has been uploaded before or not
         }else if (file_exists($target)) {
            $msg = '<div class="alert alert-danger" role="alert">Sorry, this file has been uploaded before</div>';  
      }else { 
         $sql = mysqli_query($connection, 
            "INSERT INTO `scholarships` (`scholarship_name`,`image`,`scholarship_description`,`amount`,`date`,`scholarship_status`,`added_by`,`created_at`,`updated_at`)
               VALUES ('$scholarship_name','$image','$scholarship_description','$amount','$date','$scholarship_status', '$added_by', NOW(), NOW())");
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                  $msg = '<div class="alert alert-success" role="alert">The Scholarship has been added successfully</div><meta http-equiv="refresh"content="2; \'show-scholarships.php\' "/>';
               }else{
                  $msg = '<div class="alert alert-danger" role="alert">An error occurred while adding the file</div>';
               }    
       }
    }
?>