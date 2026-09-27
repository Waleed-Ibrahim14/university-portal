<?php
session_start();

/*--------------------------------------------------------------------------
| Backward-compatible role check.
| NOTE: Original used `!$_SESSION['role'] == 'admin'` which is always false
|       due to operator precedence (! applies first, then ==).
|       Corrected to a proper admin check.
|--------------------------------------------------------------------------*/
$current_role = $_SESSION['role_name'] ?? $_SESSION['role'] ?? '';

if ($current_role === 'admin') {
    include_once(__DIR__ . "/../includes/header.php");
    include_once(__DIR__ . "/../Models/DataBaseConnection.php");
    /*--------------------------------------------------------------------------
    | FIXED: corrected filename (was create-dept-prosses.php — missing 's')
    |--------------------------------------------------------------------------*/
    include_once("../includes/create-depts-prosses.php");
?>
<body class="app"> 
	<?php include_once(__DIR__ . "/../includes/sidepanel.php"); ?>  	
	<div class="app-wrapper">
		<div class="app-content pt-3 p-md-3 p-lg-5">
			<div class="container-xl">
				<div class="row g-3 mb-4 align-items-center justify-content-between">
					<div class="col-auto">
						<h1 class="app-page-title mb-0">Create Student Debt</h1>
					</div>
				</div>
			</div>			   
			<div class="col-12 col-md-12 col-lg-12 auth-main-col text-center">
				<div class="d-flex flex-column align-content-end">
					<div class="app-auth-body mx-auto col-10 col-md-10 col-lg-10">	
						<div class="auth-form-container text-start ">
							
							<?php if (!empty($msg)) echo $msg; ?>

							<form action="" method="post" class="auth-form login-form">         
								<div class="row">
									<div class="mb-3 col-4">
										<label>Student</label>
										<select name="student_id" class="form-control" required>
											<option value="">-- Select Student --</option>
											<?php 
											/*--------------------------------------------------------------------------
											| FIXED: replace `WHERE role = 'user'` with JOIN
											|--------------------------------------------------------------------------*/
											$students = mysqli_query(
												$connection,
												"SELECT u.id, u.username
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
									<div class="mb-3 col-4">
										<label>Amount</label>
										<input name="amount" type="number" step="0.01" min="0" class="form-control" placeholder="Enter amount" required>
									</div>
									<div class="mb-3 col-4">
										<label>Date</label>
										<input name="date" type="date" class="form-control" required>
									</div>
								</div>
								<div class="mb-3">
									<label>Reason for debt</label>
									<textarea name="reason" class="form-control tinymce" rows="8" required></textarea>
								</div>
								<div class="text-center col-5 col-md-5 col-lg-5">
									<button type="submit" name="create_debt" class="btn app-btn-primary w-100 theme-btn mx-auto">Create Student Debt</button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- tinymce Text Editor -->
	<script src="../assets/plugins/tinymce/tinymce.min.js"></script>
	<script src="../assets/plugins/tinymce/init-tinymce.js"></script>
<?php
	include_once("../includes/footer.php");	
} else {
    header("Location: login.php");
    exit;
}
?>
