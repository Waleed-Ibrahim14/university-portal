<?php 
/*--------------------------------------------------------------------------
| User Activation Form::
|--------------------------------------------------------------------------*/
include_once("../Models/DataBaseConnection.php");
include_once("../includes/user-activation-prosseses.php");
if ($_SESSION['role']  == 'user' ) { 
?>
<!DOCTYPE html>
<html lang="en"> 
<head>
    <title>Account Activation</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="University Portal">
    <meta name="author" content="Waleed Ibrahim">    
    <link rel="shortcut icon" href="Views/edu.png"> 
    <script defer src="../assets/plugins/fontawesome/js/all.min.js"></script>
    <link id="theme-style" rel="stylesheet" href="../assets/css/portal.css">
</head>
<body class="app app-reset-password p-0">    	
    <div class="row g-0 app-auth-wrapper">
	    <div class="col-12 col-md-12 col-lg-12 auth-main-col text-center p-5">
		    <div class="d-flex flex-column align-content-end">
			    <div class="app-auth-body mx-auto">	
				    <div class="app-auth-branding mb-4"><a class="app-logo" href="index.html"><img class="logo-icon me-2" src="logo.png" alt="logo"></a></div>
					<?php
					if ($_SESSION['user_status']  == 'active' ) { 
						echo '<h2 class="auth-heading text-center mb-4"> Your Account is Active</h2>';
					}else{
						echo '
						<h2 class="auth-heading text-center mb-4">Activate Your Account</h2>
						<div class="auth-intro mb-4 text-center">
						Welcome to our website <b style="color: green;font-size: x-large; text-decoration: underline;">"'.$_SESSION['username'].'"</b> ! We’re glad you’ve joined us.Your account has been successfully registered, but there’s one final step required to activate your account.Please check the email address you registered with us	
					</div>
					<div class="auth-form-container text-left">';
					 echo $msg;
					 echo '
					
					 ';
					}
						
					?>
					
					
					
					<!-- Validation Form Message -->
<!--------------------------------------------------------------------------
| Activate User Account Form::
|-------------------------------------------------------------------------->
						<form action="../includes/user-activation-prosseses.php" method="post" class="auth-form resetpass-form">                
							<div class="email mb-3">
								<input name="email" type="email" class="form-control login-email" placeholder="Your Email">
							</div><!--//form-group-->
							<div class="text-center">
								<button type="submit" name="activation-account" class="btn app-btn-primary btn-block theme-btn mx-auto">Activate My Account</button>
							</div>
						</form>
					</div><!--//auth-form-container-->
</div><!--//auth-body-->

<?php	
include_once("../includes/footer.php");
}else{
	header("Location: login.php");
	}
?>	    
					
		</div><!--//flex-column-->   
	</div><!--//auth-main-col-->
</div><!--//row-->