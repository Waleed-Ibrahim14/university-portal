<?php
session_start();
if (!$_SESSION['role']  == 'admin') { 
	include_once("../includes/header.php");
    include_once("../Models/DataBaseConnection.php");
/*--------------------------------------------------------------------------
| UPDATE Certification Prosses::
|--------------------------------------------------------------------------*/
$msg = '';
    if (isset($_GET['certificateId'])) {
        $sql = mysqli_query($connection, "SELECT * FROM `certificates`  WHERE `id` = '$_GET[certificateId]'");
        $certifiupdate = mysqli_fetch_assoc($sql);
    }
    if (isset($_POST['update_certificate'])) {
        $certificate_file = $_FILES['certificate_file']['name'];
        // image file directory
        $target = "../../assets/images/certificates/".basename($certificate_file);
        $imageFileType = strtolower(pathinfo($target,PATHINFO_EXTENSION));
        // Form Validation
        if(empty($certificate_name)){ 
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
            
            }else {
                $stmt =  "UPDATE `certificates` SET `certificate_name` = '$_POST[certificate_name]', `issue_date` = '$_POST[issue_date]',
                `teacher_name` = '$_POST[teacher_name]', `certificate_file` = '$_POST[certificate_file]',`updated_at` = NOW() WHERE `id` = '$_GET[certificateId]')";

            mysqli_query($connection,$stmt)or die(mysqli_error($connection)) ;
            if (move_uploaded_file($_FILES['certificate_file']['tmp_name'], $target)) {
                $msg = '<div class="alert alert-success" role="alert">certificate created successfuly</div><meta http-equiv="refresh"content="2; \'show-certificates.php\' "/>';
            }else{
                $msg = '<div class="alert alert-danger" role="alert">Sorry, an unexpected error occurred</div>';
            }
      
    }
    } 
?>
<body class="app"> 
	<?php include_once("../includes/sidepanel.php"); ?>  	
<div class="app-wrapper">
<div class="app-content pt-3 p-md-3 p-lg-5">
<div class="container-xl">
<div class="row g-3 mb-4 align-items-center justify-content-between">
<div class="col-auto">
<h1 class="app-page-title mb-0">Create Certificate</h1>
</div>
</div>
</div>			   
<div class="col-10 col-md-10 col-lg-10 auth-main-col text-center p-6">
<div class="d-flex flex-column align-content-end">
<div class="app-auth-body mx-auto ">	
<div class="auth-form-container text-start ">
<?php 	echo $msg;  ?>
        <form action="" method="post" enctype="multipart/form-data">         		
            <div class="row">
            <div class="mb-3 col-6">
            <input  name="student_id" value="<?php echo $certifiupdate['student_id']; ?>" type="text" class="form-control " disabled>
            </div>
            <div class="mb-3 col-6">
				<input  name="certificate_name" value="<?php echo $certifiupdate['certificate_name']; ?>" type="text" class="form-control " placeholder="certificate name">
			</div>
			</div>
            <div class="row">
			<div class="mb-3 col-12">
				<input name="issue_date" value="<?php echo $certifiupdate['issue_date']; ?>" type="date" class="form-control ">
			</div>
            </div>
            <div class="row">
            <div class="mb-3">
            <select name="teacher_name" value="<?php echo $certifiupdate['teacher_name']; ?>"  class="form-control">
                <option value=""><?php echo $certifiupdate['teacher_name']; ?></option>
                <?php 
                    $stmt = mysqli_query($connection, "SELECT * FROM `users` WHERE `role` = 'teacher'");
                        while($teacherId = mysqli_fetch_assoc($stmt)){
                        echo '<option value="'.$teacherId['id'].'">'.$teacherId['username'].'</option>';
                    }
                ?>
            </select>
            </div>
			<div class="mb-3">
				<input name="certificate_file"  type="file" class="form-control">
			</div>
			</div>
			<div class="mb-3">
				<button type="submit" name="update_certificate" class="btn app-btn-primary w-100 theme-btn mx-auto">Sign Up</button>
			</div>
		</form>
</div>
</div>
<?php
	include_once("../includes/footer.php");	
}else{
	header("Location:login.php");
}
?>