<?php
session_start();

/*--------------------------------------------------------------------------
| Backward-compatible role check.
|--------------------------------------------------------------------------*/
$current_role = $_SESSION['role_name'] ?? $_SESSION['role'] ?? '';

if ($current_role == 'admin') {
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
<?php echo $msg; ?>
        <form action="" method="post" enctype="multipart/form-data">         		
            <div class="row">
            <div class="mb-3 col-6">
            <select name="student_id" class="form-control" required>
                <option value="">-- Select Student --</option>
                <?php 
                    $students = mysqli_query(
                        $connection,
                        "SELECT u.id, u.username, u.fullname
                         FROM users u
                         LEFT JOIN roles r ON u.role_id = r.id
                         WHERE r.role_name = 'user'
                         ORDER BY u.username ASC"
                    );
                    while ($student = mysqli_fetch_assoc($students)) {
                        echo '<option value="'.(int)$student['id'].'">'
                           . htmlspecialchars($student['username'])
                           . '</option>';
                    }
                ?>
            </select>
            </div>
            <div class="mb-3 col-6">
				<input name="certificate_name" type="text" class="form-control" placeholder="Certificate name" required>
			</div>
			</div>
            <div class="row">
			<div class="mb-3 col-6">
				<input name="issue_date" type="date" class="form-control" required>
			</div>
            <div class="mb-3 col-6">
            <select name="course_id" class="form-control" required>
                <option value="">-- Select Course --</option>
                <?php 
                    $courses = mysqli_query(
                        $connection,
                        "SELECT id, course_name FROM courses ORDER BY course_name ASC"
                    );
                    while ($course = mysqli_fetch_assoc($courses)) {
                        echo '<option value="'.(int)$course['id'].'">'
                           . htmlspecialchars($course['course_name'])
                           . '</option>';
                    }
                ?>
            </select>
            </div>
            </div>
            <div class="row">
            <div class="mb-3">
            <select name="teacher_id" class="form-control" required>
                <option value="">-- Select Teacher --</option>
                <?php 
                    $teachers = mysqli_query(
                        $connection,
                        "SELECT u.id, u.username, u.fullname
                         FROM users u
                         LEFT JOIN roles r ON u.role_id = r.id
                         WHERE r.role_name = 'teacher'
                         ORDER BY u.username ASC"
                    );
                    while ($teacher = mysqli_fetch_assoc($teachers)) {
                        echo '<option value="'.(int)$teacher['id'].'">'
                           . htmlspecialchars($teacher['username'])
                           . '</option>';
                    }
                ?>
            </select>
            </div>
			<div class="mb-3">
				<input name="certificate_file" type="file" accept=".pdf" class="form-control" required>
			</div>
			</div>
			<div class="mb-3">
				<button type="submit" name="create_certificate" class="btn app-btn-primary w-100 theme-btn mx-auto">Create Certificate</button>
			</div>
		</form>
</div>
</div>
<?php
	include_once("../includes/footer.php");	
} else {
	header("Location: login.php");
	exit;
}
?>
