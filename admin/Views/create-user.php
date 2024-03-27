<?php
session_start();
if ($_SESSION['role']  !== 'admin') { 
	include_once("../includes/header.php");
    include_once("../includes/notification/Push.php");  
    include_once("../includes/create-user-prosses.php");
?>
<body class="app"> 
	<?php include_once("../includes/sidepanel.php"); ?>  	
<div class="app-wrapper">
<div class="app-content pt-3 p-md-3 p-lg-5">
<div class="container-xl">
<div class="row g-3 mb-4 align-items-center justify-content-between">
<div class="col-auto">
<h1 class="app-page-title mb-0">Create New User</h1>
</div>
</div>
</div>			   
<div class="col-10 col-md-10 col-lg-10 auth-main-col text-center">
<div class="d-flex flex-column align-content-end">
<div class="app-auth-body mx-auto ">	
<div class="auth-form-container text-start ">
	<!-- errors and success message -->
<?php 	echo $msg;  ?>
<form class="auth-form auth-signup-form" action="" method="post" enctype="multipart/form-data">         
		<div class="row">
			<div class="mb-3 col-4">
				<input name="fullname" type="text" class="form-control signup-fullname" placeholder="Full name">
			</div>
			<div class="mb-3 col-4">
				<select name = "country" class="form-control signup-name">
					<option value="">choose Your Country</option>
						<?php 
						foreach ($countries as $country) {
							echo '<option value="' . $country['alpha2'] . '">' . $country['name'] . '</option>';
						}
					?>
				</select>
			</div>
			
		</div>
		<div class="row">
			<div class="mb-3 col-4">
				<select name = "gender" class="form-control signup-name">
					<option value="" selected="">Choose Gender</option>
					<option value="male">Male</option>
					<option value="female">Female</option>
				</select>
			</div>
			<div class="mb-3 col-4">
				<input name="username" type="text" class="form-control" placeholder="username">
			</div>
			
			<div class="row">
				<div class="mb-3 col-4">
					<input name="email" type="email" class="form-control" placeholder="Email">
				</div>
				<div class="mb-3 col-4">
						<select name="role"  class="form-control" id="sections" >
							<option value="">select user role</option>
							<?php 
								$stmt = mysqli_query($connection, "SELECT * FROM `roles`");
								while($role = mysqli_fetch_assoc($stmt)){
								echo '<option value="'.$role['role_name'].'">'.$role['role_name'].'</option>';
								}
							?>
						</select>
			</div>
		
		
		</div>
		<div class="mb-3 col-4">
			<input name="profile" type="file" class="form-control" placeholder="prfile" >
		</div>
		
		<div class="mb-3 col-4">
		<select name="user_status"  class="form-control" id="sections" >
			<option value="">select user status</option>
			<option value="active">active</option>
			<option value="blocked">blocked</option>
		</select>
		</div>
		<div class="row">
			<div class="mb-3 col-4">
				<input name="password_1" type="password" maxlength="8" class="form-control" placeholder="password">
			</div>
			<div class="mb-3 col-4">
				<input name="password_2" type="password" maxlength="8" class="form-control" placeholder="confirm password" >
			</div>
		</div>
		<div class="text-center mb-3 col-3">
			<button type="submit" name="create_user" class="btn app-btn-primary w-100 theme-btn mx-auto">Sign Up</button>
		</div>
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