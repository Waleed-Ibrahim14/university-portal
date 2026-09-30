<?php
/*--------------------------------------------------------------------------
| Add Scholarship Form (Public side) ::
|--------------------------------------------------------------------------*/
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*--------------------------------------------------------------------------
| FIXED: session check — original was inverted.
| If user is NOT logged in, redirect to login.
|--------------------------------------------------------------------------*/
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

include_once(__DIR__ . "/../admin/Models/DataBaseConnection.php");
?>
<body class="app"> 
	<?php include_once("../includes/header.php"); ?>
	<?php include_once("../includes/sidepanel.php"); ?>  	
<div class="app-wrapper">
<div class="app-content pt-3 p-md-3 p-lg-5">
<div class="container-xl">
<div class="row g-3 mb-4 align-items-center justify-content-between">
<div class="col-auto">
	<h1 class="app-page-title mb-0">Create New Scholarship</h1>
</div>
</div>
</div>			   
<div class="col-12 col-md-12 col-lg-12 auth-main-col text-center p-5">
<div class="d-flex flex-column align-content-end">
<div class="app-auth-body mx-auto col-10 col-md-10 col-lg-10">	
<div class="auth-form-container text-start ">
<?php echo $msg ?? ''; ?>
<!--------------------------------------------------------------------------
| Create Scholarship Form ::
|-------------------------------------------------------------------------->
	<form action="" method="post" enctype="multipart/form-data" class="auth-form login-form">
		<div class="email mb-3 col-5 col-md-5 col-lg-5">
			<input name="scholarship_name" type="text" class="form-control" placeholder="Scholarship Name" required>
		</div>
		<div class="email mb-3 col-5 col-md-5 col-lg-5">
			<input name="image" type="file" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.gif" required>
		</div>
		<div class="email mb-3 col-6 col-md-6 col-lg-6">
			<input type="text" name="amount" class="form-control" id="amountInput" 
			       pattern="\d+(\.\d{1,2})?" placeholder="Enter amount (up to 2 decimals)" required>
		</div>
		<div class="email mb-3">
			<textarea name="scholarship_description" class="form-control tinymce" id="scholarship_description" rows="8"></textarea>
		</div>
		<div class="text-center col-5 col-md-5 col-lg-5">
			<button type="submit" name="add_scholarship" class="btn app-btn-primary w-100 theme-btn mx-auto">Create</button>
		</div>
	</form>
	</div>
</div>
<script type="text/javascript">
	const amountInput = document.getElementById('amountInput');
	amountInput.addEventListener('input', function () {
		const value = this.value;
		if (!/^(\d+(\.\d{1,2})?)?$/.test(value)) {
			this.setCustomValidity('Enter a valid decimal number (up to 2 decimal places)');
		} else {
			this.setCustomValidity('');
		}
	});
</script>
<!-- tinymce Text Editor -->
<script src="../assets/plugins/tinymce/tinymce.min.js"></script>
<script src="../assets/plugins/tinymce/init-tinymce.js"></script>
<?php
	include_once("../includes/footer.php");
?>
