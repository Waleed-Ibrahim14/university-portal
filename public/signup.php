<?php
include_once("../admin/Models/DataBaseConnection.php");
require_once '../vendor/autoload.php';
include_once("includes/signup-user-prosses.php");
/*--------------------------------------------------------------------------------------------------------
| ISO3166 This library was used, which contains a list of countries according to the ISO-3166-1 standard .
|-------------------------------------------------------------------------------------------------------*/
?>
<!DOCTYPE html>
<html lang="en"> 
<head>
    <title>University Portal</title>
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
<div class="app-auth-branding mb-4"><a class="app-logo" href="index.php"><img class="logo-icon me-2" src="../assets/images/logo.png" alt="University Portal"></a></div>
<h2 class="auth-heading text-center mb-4">Sign up to University Portal</h2>	

<?php 	echo $msg;  ?> <!-- Validation Form Message :: -->

<!--------------------------------------------------------------------------
| Register User Form ::
|-------------------------------------------------------------------------->						
	<div class="auth-form-container text-start mx-auto">
		<form class="auth-form auth-signup-form" action="" method="post" enctype="multipart/form-data">         
			<div class="email mb-3">
				<input name="fullname" type="text" class="form-control signup-fullname" placeholder="Full name">
			</div>
			<div class="email mb-3">
			<select name = "country" class="form-control">
                    <option value="">choose Your Country</option>
                    <?php 
                        foreach ($countries as $country) {
                            echo '
                            <option value="' . $country['alpha2'] . '">' . $country['name'] . '</option>';
                        }
                    ?>
				</select>
			</div>
            <div class="email mb-3">
				<select name = "gender" class="form-control signup-name">
					<option value="" selected="">Choose Gender</option>
					<option value="male">Male</option>
					<option value="female">Female</option>
				</select>
			</div>
			<div class="email mb-3">
				<input name="username" type="text" class="form-control signup-name" placeholder="username">
			</div>
			<div class="email mb-3">
				<input name="email" type="email" class="form-control signup-email" placeholder="Email">
			</div>
			<div class="password mb-3">
				<input name="password_1" type="password" maxlength="8" class="form-control signup-password" placeholder="password">
			</div>
			<div class="password mb-3">
				<input name="password_2" type="password" maxlength="8" class="form-control signup-password" placeholder="confirm password" >
			</div>
			<div class="password mb-3">
				<input name="profile" type="file" class="form-control signup-password" placeholder="prfile" >
			</div>
			<div class="text-center">
				<button type="submit" name="submit" class="btn app-btn-primary w-100 theme-btn mx-auto">Sign Up</button>
			</div>
		</form><!--//auth-form-->
<div class="auth-option text-center pt-2">Already have an account? <a class="text-link" href="login.php" >Log in</a></div>
</div><!--//auth-form-container-->	
</div><!--//auth-body-->		    
<?php   include_once("includes/footer.php"); ?>