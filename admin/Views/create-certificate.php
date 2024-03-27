<?php
session_start();
if ($_SESSION['role']  !== 'admin') { 
	include_once("../includes/header.php");
    include_once("../includes/create-certificate-prosses.php");
?>
<body class="app"> 
	<?php include_once("../includes/sidepanel.php"); ?>  	
<div class="app-wrapper">
<div class="app-content pt-3 p-md-3 p-lg-5">
<div class="container-xl">
<div class="row g-3 mb-4 align-items-center justify-content-between">
<div class="col-auto">
<h1 class="app-page-title mb-0">Create Certificate</h1>
</div>
</div>
</div>			   
<div class="col-10 col-md-10 col-lg-10 auth-main-col text-center ">
<div class="d-flex flex-column align-content-end">
<div class="app-auth-body mx-auto ">	
<div class="auth-form-container text-start ">
<?php 	echo $msg;  ?>
        <form action="" method="post" enctype="multipart/form-data">         		
            <div class="row">
            <div class="mb-3 col-6">
            <select name="student_id"  class="form-control signup-password">
                <option value="">select user</option>
                <?php 
                    $stmt = mysqli_query($connection, "SELECT * FROM `users` WHERE `role` = 'user'");
                    while($studentId = mysqli_fetch_assoc($stmt)){
                    echo '<option value="'.$studentId['id'].'">'.$studentId['username'].'</option>';
                    }
                ?>
            </select>
            </div>
            <div class="mb-3 col-6">
				<input  name="certificate_name" type="text" class="form-control " placeholder="certificate name">
			</div>
			</div>
            <div class="row">
			<div class="mb-3 col-6">
				<input name="issue_date" type="date" class="form-control ">
			</div>
            <div class="mb-3 col-6">
            <select name="course_name"  class="form-control">
                <option value="">select course name</option>
                <?php 
                    $stmt = mysqli_query($connection, "SELECT `course_name` FROM `courses`");
                        while($teacherId = mysqli_fetch_assoc($stmt)){
                        echo '<option value="'.$teacherId['course_name'].'">'.$teacherId['course_name'].'</option>';
                    }
                ?>
            </select>
            </div>
            </div>
            <div class="row">
            <div class="mb-3">
            <select name="teacher_name"  class="form-control">
                <option value="">select teacher</option>
                <?php 
                    $stmt = mysqli_query($connection, "SELECT * FROM `users` WHERE `role` = 'teacher'");
                        while($teacherId = mysqli_fetch_assoc($stmt)){
                        echo '<option value="'.$teacherId['id'].'">'.$teacherId['username'].'</option>';
                    }
                ?>
            </select>
            </div>
			<div class="mb-3">
				<input name="certificate_file" type="file" class="form-control">
			</div>
			</div>
			<div class="mb-3">
				<button type="submit" name="create_certificate" class="btn app-btn-primary w-100 theme-btn mx-auto">Sign Up</button>
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