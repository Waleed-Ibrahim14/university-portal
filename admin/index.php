<?php
/*--------------------------------------------------------------------------
| Start session FIRST (before any include that uses $_SESSION).
|--------------------------------------------------------------------------*/
session_start();

/*--------------------------------------------------------------------------
| Include login process (handles POST validation + session setting).
| NOTE: log-in-prosses.php also has a session_status() guard,
|       so calling session_start() twice is safe.
|--------------------------------------------------------------------------*/
include_once(__DIR__ . "/includes/log-in-prosses.php");
?>
<!DOCTYPE html>
<html lang="en"> 
<head>
    <title>Portal | Login</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="University Portal">
    <meta name="author" content="Waleed Ibrahim">    
    <link rel="shortcut icon" href="assets/images/logo.png">
    <script defer src="../assets/plugins/fontawesome/js/all.min.js"></script>
    <link id="theme-style" rel="stylesheet" href="../assets/css/portal.css">
</head>
<body class="app app-login p-0">    	
  	<div class="row g-0 app-auth-wrapper">
	<div class="col-12 col-md-12 col-lg-12 auth-main-col text-center p-5">
	<div class="d-flex flex-column align-content-end">
	<div class="app-auth-body mx-auto">	
	<div class="app-auth-branding mb-4">
        <a class="app-logo" href="index.php">
            <img class="logo-icon me-2" src="assets/images/logo.png" alt="University Portal">
        </a>
    </div>
	<h2 class="auth-heading text-center mb-5" style="color: green;">Log in to University Portal</h2>
  
<div class="auth-form-container text-start">
<!-- Validation Form Message -->
<?php if (!empty($msg)) { echo $msg; } ?>

<!--------------------------------------------------------------------------
| Login Form ::
|-------------------------------------------------------------------------->
	<form action="" method="post" class="auth-form login-form">         
		<div class="email mb-3">
			<input name="email" type="email" class="form-control signin-email" placeholder="Email address" required>
		</div>
		<div class="password mb-3">
			<input name="password" type="password" class="form-control signin-password" placeholder="Password" required>
			<div class="extra mt-3 row justify-content-between">
				<div class="col-6"></div>
				<div class="col-6">
					<div class="forgot-password text-end">
						<a href="password-reset-form.php">Forgot password?</a>
					</div>
				</div>
			</div>
		</div>
		<div class="text-center">
			<button type="submit" name="login" class="btn app-btn-primary w-100 theme-btn mx-auto">Log In</button>
		</div>
	</form>

	<!-- NOTE: signup.php may not exist in this project. If not, remove this line. -->
	<div class="auth-option text-center pt-5">
        No Account? <a class="text-link" href="signup.php">Sign up here</a>.
    </div>

</div><!--//auth-form-container-->	
</div><!--//auth-body-->
<?php include_once(__DIR__ . "/includes/footer.php"); ?>
