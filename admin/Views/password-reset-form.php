<?php 
/*--------------------------------------------------------------------------
| Start session FIRST (before including the process file).
|--------------------------------------------------------------------------*/
session_start();

/*--------------------------------------------------------------------------
| Include password reset process (handles POST + validation).
|--------------------------------------------------------------------------*/
include_once(__DIR__ . "/../includes/password-reset-prosses.php");
?>
<!DOCTYPE html>
<html lang="en"> 
<head>
    <title>Reset Password | University Portal</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="University Portal">
    <meta name="author" content="Waleed Ibrahim">    
    <link rel="shortcut icon" href="../assets/images/logo.png"> 
    <script defer src="../assets/plugins/fontawesome/js/all.min.js"></script>
    <link id="theme-style" rel="stylesheet" href="../assets/css/portal.css">
</head>
<body class="app app-reset-password p-0">    	
    <div class="row g-0 app-auth-wrapper">
	    <div class="col-12 col-md-12 col-lg-12 auth-main-col text-center p-5">
		    <div class="d-flex flex-column align-content-end">
			    <div class="app-auth-body mx-auto">	
				    <div class="app-auth-branding mb-4">
                        <!-- FIXED: was index.html (broken link) -->
                        <a class="app-logo" href="index.php">
                            <img class="logo-icon me-2" src="../assets/images/logo.png" alt="logo">
                        </a>
                    </div>
					<h2 class="auth-heading text-center mb-4">Change Your Password</h2>
					<div class="auth-form-container text-left">
					<?php if (!empty($msg)) { echo $msg; } ?>
<!--------------------------------------------------------------------------
| Password Reset Form ::
|-------------------------------------------------------------------------->
						<form action="" method="post" class="auth-form resetpass-form">
							<div class="email mb-3">
								<input name="email" type="email" class="form-control login-email" placeholder="Your Email" required>
							</div>
							<div class="email mb-3">
								<input name="oldPassword" type="password" class="form-control login-email" placeholder="Your Old Password" required>
							</div>
							<div class="email mb-3">
								<input name="password" type="password" class="form-control login-email" placeholder="New Password" required>
							</div>
							<div class="text-center">
								<button type="submit" name="reset-password" class="btn app-btn-primary btn-block theme-btn mx-auto">Change Password</button>
							</div>
						</form>
					</div><!--//auth-form-container-->
</div><!--//auth-body-->

<?php include_once(__DIR__ . "/../includes/footer.php"); ?>	    
					
		</div><!--//flex-column-->   
	</div><!--//auth-main-col-->
</div><!--//row-->
