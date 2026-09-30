<?php
/*--------------------------------------------------------------------------
| Signup Form for Public Users ::
|--------------------------------------------------------------------------*/
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once(__DIR__ . "/../admin/Models/DataBaseConnection.php");
require_once __DIR__ . "/../vendor/autoload.php";
include_once(__DIR__ . "/includes/signup-user-prosses.php");
?>
<!DOCTYPE html>
<html lang="en"> 
<head>
    <title>Sign Up | University Portal</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="University Portal">
    <meta name="author" content="Waleed Ibrahim">    
    <link rel="shortcut icon" href="../assets/images/logo.png"> 
    <script defer src="../assets/plugins/fontawesome/js/all.min.js"></script>
    <link id="theme-style" rel="stylesheet" href="../assets/css/portal.css">
</head>
<body class="app app-signup p-0">    	
<div class="row g-0 app-auth-wrapper">
<div class="col-12 col-md-12 col-lg-12 auth-main-col text-center p-4">
<div class="d-flex flex-column align-content-end">
<div class="app-auth-body mx-auto">	
<div class="app-auth-branding mb-4">
    <a class="app-logo" href="index.php">
        <img class="logo-icon me-2" src="../assets/images/logo.png" alt="University Portal">
    </a>
</div>
<h2 class="auth-heading text-center mb-4">Sign up to University Portal</h2>	

<?php if (!empty($msg)) echo $msg; ?>

<!--------------------------------------------------------------------------
| Register User Form ::
|-------------------------------------------------------------------------->						
	<div class="auth-form-container text-start mx-auto">
		<form class="auth-form auth-signup-form" action="" method="post" enctype="multipart/form-data">         
			<div class="email mb-3">
				<input name="fullname" type="text" class="form-control signup-fullname" placeholder="Full name" required>
			</div>
			<div class="email mb-3">
			<select name="country" class="form-control" required>
                    <option value="">Choose Your Country</option>
                    <?php 
                        foreach ($countries as $country) {
                            echo '<option value="' . htmlspecialchars($country['alpha2']) . '">' 
                               . htmlspecialchars($country['name']) . '</option>';
                        }
                    ?>
				</select>
			</div>
            <div class="email mb-3">
				<select name="gender" class="form-control" required>
					<option value="">Choose Gender</option>
					<option value="male">Male</option>
					<option value="female">Female</option>
				</select>
			</div>
			<div class="email mb-3">
				<input name="username" type="text" class="form-control" placeholder="Username" required>
			</div>
			<div class="email mb-3">
				<input name="email" type="email" class="form-control" placeholder="Email" required>
			</div>
			<div class="password mb-3">
				<!-- FIXED: removed maxlength="8" (was limiting password length) -->
				<input name="password_1" type="password" class="form-control" placeholder="Password" required>
			</div>
			<div class="password mb-3">
				<input name="password_2" type="password" class="form-control" placeholder="Confirm password" required>
			</div>
			<div class="password mb-3">
				<input name="profile" type="file" class="form-control" accept=".jpg,.jpeg,.png,.gif" required>
			</div>
			<div class="text-center">
				<button type="submit" name="submit" class="btn app-btn-primary w-100 theme-btn mx-auto">Sign Up</button>
			</div>
		</form><!--//auth-form-->
<div class="auth-option text-center pt-2">Already have an account? <a class="text-link" href="login.php">Log in</a></div>
</div><!--//auth-form-container-->	
</div><!--//auth-body-->		    
<?php include_once(__DIR__ . "/includes/footer.php"); ?>
</body>
</html>
