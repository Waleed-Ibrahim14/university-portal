<?php
session_start();

/*--------------------------------------------------------------------------
| Backward-compatible role check.
|--------------------------------------------------------------------------*/
$current_role = $_SESSION['role_name'] ?? $_SESSION['role'] ?? '';

if ($current_role === 'admin') {
    /*--------------------------------------------------------------------------
    | Include DB connection BEFORE the form, so $connection is available below.
    | Also include the process file (handles POST submission).
    |--------------------------------------------------------------------------*/
    include_once(__DIR__ . "/../Models/DataBaseConnection.php");
    include_once("../includes/header.php");
?>
<body class="app"> 
	<?php include_once("../includes/sidepanel.php"); ?>  	
<div class="app-wrapper">
<div class="app-content pt-3 p-md-3 p-lg-5">
<div class="container-xl">
<div class="row g-3 mb-4 align-items-center justify-content-between">
<div class="col-auto">
<h1 class="app-page-title mb-0">Create New Course</h1>
</div>
</div>
</div>			   
<div class="col-12 col-md-12 col-lg-12 auth-main-col text-center">
<div class="d-flex flex-column align-content-end">
<div class="app-auth-body mx-auto col-10 col-md-10 col-lg-10">	
<div class="auth-form-container text-start ">
    
	<?php 
    /*--------------------------------------------------------------------------
    | Include process file (handles validation + INSERT)
    | NOTE: fixed filename capitalization (was create-Course-prosses.php)
    |--------------------------------------------------------------------------*/
    include_once("../includes/create-course-prosses.php");
    ?>
<?php if (!empty($message)) echo $message; ?>
	<form action="" method="post" class="auth-form login-form">         
        <div class="row">
		<div class="mb-3 col-5 col-md-5 col-lg-5">
			<input name="course_name" type="text" class="form-control" placeholder="Enter Course Name" required>
            </div>
            <div class="mb-3 col-5 col-md-5 col-lg-5">
            <select name="teacher_id" class="form-control" required>
                <option value="">-- Select Teacher --</option>
                <?php 
                    /*--------------------------------------------------------------------------
                    | FIXED: replace `WHERE role = 'teacher'` (removed column) with JOIN
                    |--------------------------------------------------------------------------*/
                    $teachers = mysqli_query(
                        $connection,
                        "SELECT u.id, u.username
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
        </div>
        <div class="text-center col-5 col-md-5 col-lg-5">
			<button type="submit" name="create_course" class="btn app-btn-primary w-100 theme-btn mx-auto">Create Course</button>
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
