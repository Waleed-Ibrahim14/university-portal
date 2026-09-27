<?php 
/*--------------------------------------------------------------------------
| User Activation Form ::
|--------------------------------------------------------------------------*/

/*--------------------------------------------------------------------------
| Start session FIRST.
|--------------------------------------------------------------------------*/
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*--------------------------------------------------------------------------
| Include DB connection BEFORE the process file (which needs $connection).
|--------------------------------------------------------------------------*/
include_once(__DIR__ . "/../Models/DataBaseConnection.php");

/*--------------------------------------------------------------------------
| Include process file (handles POST submission).
| NOTE: FIXED spelling (was user-activation-prosseses.php with triple 's').
|--------------------------------------------------------------------------*/
include_once(__DIR__ . "/../includes/user-activation-prosses.php");

/*--------------------------------------------------------------------------
| Only regular 'user' role can access this page.
|--------------------------------------------------------------------------*/
$current_role = $_SESSION['role_name'] ?? $_SESSION['role'] ?? '';

if ($current_role === 'user') {
?>
<!DOCTYPE html>
<html lang="en"> 
<head>
    <title>Account Activation | University Portal</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="University Portal">
    <meta name="author" content="Waleed Ibrahim">    
    <!-- FIXED: paths relative to admin/Views/ → ../assets/... -->
    <link rel="shortcut icon" href="../../assets/images/logo.png"> 
    <script defer src="../assets/plugins/fontawesome/js/all.min.js"></script>
    <link id="theme-style" rel="stylesheet" href="../assets/css/portal.css">
</head>
<body class="app app-reset-password p-0">    	
    <div class="row g-0 app-auth-wrapper">
	    <div class="col-12 col-md-12 col-lg-12 auth-main-col text-center p-5">
		    <div class="d-flex flex-column align-content-end">
			    <div class="app-auth-body mx-auto">	
				    <!-- FIXED: link target (was index.html) and logo path -->
				    <div class="app-auth-branding mb-4">
                        <a class="app-logo" href="../index.php">
                            <img class="logo-icon me-2" src="../../assets/images/logo.png" alt="logo">
                        </a>
                    </div>
					
					<?php
                    $user_status = $_SESSION['user_status'] ?? '';
                    
					if ($user_status === 'active') {
						echo '<h2 class="auth-heading text-center mb-4">Your Account is Active</h2>';
					} else {
                        echo '<h2 class="auth-heading text-center mb-4">Activate Your Account</h2>';
                        echo '<div class="auth-intro mb-4 text-center">'
                           . 'Welcome to our website <b style="color: green; font-size: x-large; text-decoration: underline;">"'
                           . htmlspecialchars($_SESSION['username'] ?? '')
                           . '"</b> ! We\'re glad you\'ve joined us. Your account has been successfully registered, '
                           . 'but there\'s one final step required to activate your account. '
                           . 'Please check the email address you registered with us.'
                           . '</div>';
                        
                        echo '<div class="auth-form-container text-left">';
                        if (!empty($msg)) {
                            echo $msg;
                        }
						
						/*--------------------------------------------------------------------------
						| Activation form — only shown when account is NOT active.
						|--------------------------------------------------------------------------*/
						?>
						<form action="" method="post" class="auth-form resetpass-form">
							<div class="email mb-3">
								<input name="email" type="email" class="form-control login-email" 
								       placeholder="Your Email" required>
							</div>
							<div class="text-center">
								<button type="submit" name="activation-account" 
								        class="btn app-btn-primary btn-block theme-btn mx-auto">
                                    Activate My Account
                                </button>
							</div>
						</form>
						</div><!--//auth-form-container-->
						<?php
					}
					?>
                    
                    <!-- Show messages even when account is already active -->
                    <?php if ($user_status === 'active' && !empty($msg)) echo $msg; ?>

			    </div><!--//auth-body-->

<?php	
    include_once(__DIR__ . "/../includes/footer.php");
} else {
    header("Location: ../login.php");
    exit;
}
?>	    
					
		</div><!--//flex-column-->   
	</div><!--//auth-main-col-->
</div><!--//row-->
</body>
</html>
