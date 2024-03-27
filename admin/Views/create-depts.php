<?php
session_start();
if (!$_SESSION['role']  == 'admin') { 
	include_once("../includes/header.php");
	?>
<body class="app"> 
	<?php include_once("../includes/sidepanel.php"); ?>  	
	<div class="app-wrapper">
		<div class="app-content pt-3 p-md-3 p-lg-5">
			<div class="container-xl">
				<div class="row g-3 mb-4 align-items-center justify-content-between">
					<div class="col-auto">
						<h1 class="app-page-title mb-0">Create depts</h1>
					</div>
				</div>
			</div>			   
			<div class="col-12 col-md-12 col-lg-12 auth-main-col text-center">
				<div class="d-flex flex-column align-content-end">
					<div class="app-auth-body mx-auto col-10 col-md-10 col-lg-10">	
						<div class="auth-form-container text-start ">
							<?php include_once("../includes/create-dept-prosess.php");?>
							
	
	<form action="" method="post" class="auth-form login-form">         
    <div class="row">
            <div class="mb-3 col-4">
            <select name="student_id"  class="form-control">
                <option value="">select user</option>
                <?php 
                    $stmt = mysqli_query($connection, "SELECT * FROM `users` WHERE `role` = 'user'");
                    while($studentId = mysqli_fetch_assoc($stmt)){
                    echo '<option value="'.$studentId['id'].'">'.$studentId['username'].'</option>';
                    }
                ?>
            </select>
            </div>
            <div class="mb-3 col-4">
                <input name="amount" type="text" class="form-control" placeholder="Enter amount" >
            </div>
            <div class="mb-3 col-4">
                <input name="date" type="date" class="form-control" placeholder="Enter Class Name">
            </div>
		<div class="mb-3">
			<td>Reason for dept</td>
			<textarea name="reason" class="form-control tinymce" rows="8"></textarea>
		</div></tr>
        <div class="text-center col-5 col-md-5 col-lg-5">
			<button type="submit" name="submit" class="btn app-btn-primary w-100 theme-btn mx-auto">Create user dept</button>
		</div>
	</form>
	</div>
</div>
<!-- tinymce Text Editor -->
<script src="../assets/plugins/tinymce/tinymce.min.js"></script>
<script src="../assets/plugins/tinymce/init-tinymce.js"></script>
<?php
	include_once("../includes/footer.php");	
}else{
	header("Location:login.php");
}
?>