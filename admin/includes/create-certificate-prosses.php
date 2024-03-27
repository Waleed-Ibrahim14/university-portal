<?php
/*--------------------------------------------------------------------------
| Create Certificate Prosses::
|--------------------------------------------------------------------------*/
include_once("../Models/DataBaseConnection.php");
$msg = ''; // Initialize the message variable

if (isset($_POST['create_certificate'])) {
    $student_id = $_POST['student_id'];
    $certificate_name = $_POST['certificate_name'];
    $issue_date = $_POST['issue_date'];
    $teacher_name = $_POST['teacher_name'];
    // Get image name
    $certificate_file = $_FILES['certificate_file']['name'];
    // image file directory
    $target = "../../assets/images/certificates/".basename($certificate_file);
    $imageFileType = strtolower(pathinfo($target,PATHINFO_EXTENSION));
    // Form Validation
    if(empty($student_id)){
        $msg ='<div class="alert alert-danger" role="alert">Please select student</div>';
        }elseif(empty($certificate_name)){ 
            $msg = '<div class="alert alert-danger" role="alert">Please enter certificate name</div>';
        }elseif(empty($issue_date)){ 
            $msg ='<div class="alert alert-danger" role="alert">Please select issue date</div>';
        }elseif(empty($teacher_name)){ 
            $msg ='<div class="alert alert-danger" role="alert">Please select teacher</div>';
        // Validate Image type
        }else if($imageFileType != "pdf") {
            $msg = '<div class="alert alert-danger" role="alert">Choose Corrected file, *.pdf</div>';
        // Validate Image Size
        }else if ($_FILES["certificate_file"]["size"] > 50000000000000) {
        $msg = '<div class="alert alert-danger" role="alert">File size very big</div>';
        // Validate if this uploaded before or not
        }else if (file_exists($target)) {
        $msg = '<div class="alert alert-danger" role="alert">Sorry this certificate file was uploaded before</div>';  
        
        }else{
        $stmt =  "INSERT INTO `certificates` (`student_id`,`certificate_name`,`issue_date`,`teacher_name`,`certificate_file`,`created_at`,`updated_at`)
                 VALUES ('$student_id','$certificate_name','$issue_date','$teacher_name','$certificate_file',NOW(),NOW())";
        mysqli_query($connection,$stmt)or die(mysqli_error($connection)) ;
        if (move_uploaded_file($_FILES['certificate_file']['tmp_name'], $target)) {
            $msg = '<div class="alert alert-success" role="alert">certificate created successfuly</div><meta http-equiv="refresh"content="2; \'show-certificates.php\' "/>';
        }else{
            $msg = '<div class="alert alert-danger" role="alert">Sorry, an unexpected error occurred</div>';
        }
    }
}
?>