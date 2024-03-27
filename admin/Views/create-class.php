<?php
session_start();
if ($_SESSION['role']  !== 'admin') { 
	include_once("../includes/header.php");
	include_once("../includes/sidepanel.php"); 
		// if ($_SESSION['role']  == 'user' ) {
		$msg = '';
		if (isset($_POST['submit'])) {
			$class_name =  $_POST['class_name'];
			if (empty($_POST['class_name'])) {
				$msg = '<div class="alert alert-danger" role="alert">Please Enter Class Name</div>';
			}else {
				$insert = mysqli_query($connection, "INSERT INTO `classes` (`class_name`,`teacher_id`,`student_id`,`created_at`,`updated_at`) VALUES ('$class_name',NOW(),NOW())");
					if (isset($insert)) {
						$msg = '<div class="alert alert-success" role="alert">Create Classe Successfly</div>';
					}
			}
		}       
?>
<body class="app">   	
	<div class="app-wrapper">
		<div class="app-content pt-3 p-md-3 p-lg-5">
			<div class="container-xl">
				<div class="row g-3 mb-4 align-items-center justify-content-between">
					<div class="col-auto">
					<h1 class="app-page-title mb-0">Create New Course</h1>
					</div>
				</div>
			</div>			   
			<div class="col-12 col-md-12 col-lg-12 auth-main-col text-center">
				<div class="d-flex flex-column align-content-end">
					<div class="app-auth-body mx-auto col-6 col-md-6 col-lg-6">	
						<div class="auth-form-container text-start ">
							<?php echo $msg; ?>
							<form class="auth-form login-form" action="" method="post">         
								<div class="email mb-3">
									<input name="class_name" type="text" class="form-control signin-email" placeholder="Enter Class Name" >
								</div>
								<div class="text-center">
									<button type="submit" name="submit" class="btn app-btn-primary w-100 theme-btn mx-auto">Create</button>
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