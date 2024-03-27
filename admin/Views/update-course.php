<?php
session_start();
if ($_SESSION['role']  !== 'admin') { 
	include_once("../includes/header.php");
	include_once("../includes/sidepanel.php"); ?>

<body class="app">   	
    <div class="app-wrapper">
	    <div class="app-content pt-3 p-md-3 p-lg-5">
		    <div class="container-xl">
			    <div class="row g-3 mb-4 align-items-center justify-content-between">
				    <div class="col-auto">
			            <h1 class="app-page-title mb-0">Update ..... Course</h1>
				    </div>
				</div><!--//col-auto-->
			</div><!--//row-->
			   
	<div class="col-12 col-md-12 col-lg-12 auth-main-col text-center p-5">
	<div class="d-flex flex-column align-content-end">
	<div class="app-auth-body mx-auto col-6 col-md-6 col-lg-6">	
	<div class="auth-form-container text-start ">
	<form class="auth-form login-form">         
		<div class="email mb-3">
			<label class="sr-only" for="signin-email">Email</label>
			<input id="signin-email" name="signin-email" type="email" class="form-control signin-email" placeholder="Email address" required="required">
		</div><!--//form-group-->
		<div class="password mb-3">
			<label class="sr-only" for="signin-password">Password</label>
			<input id="signin-password" name="signin-password" type="password" class="form-control signin-password" placeholder="Password" required="required">
			<div class="extra mt-3 row justify-content-between">
				
			</div><!--//extra-->
		</div><!--//form-group-->
		<div class="text-center">
			<button type="submit" class="btn app-btn-primary w-100 theme-btn mx-auto">Log In</button>
		</div>
	</form>
				
				
			    
		    </div><!--//container-fluid-->
	    </div><!--//app-content-->
	    
<?php
	include_once("../includes/footer.php");	
}else{
	header("Location:login.php");
}
?>
