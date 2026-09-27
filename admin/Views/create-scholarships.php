<?php
session_start();

/*--------------------------------------------------------------------------
| Backward-compatible role check.
|--------------------------------------------------------------------------*/
$current_role = $_SESSION['role_name'] ?? $_SESSION['role'] ?? '';

if ($current_role !== 'admin') {
    include_once("../includes/header.php");
    /*--------------------------------------------------------------------------
    | FIXED: corrected filename (was "create-sholarship-prosses.php")
    |--------------------------------------------------------------------------*/
    include_once("../includes/create-scholarships-prosses.php");
?>
<body class="app"> 
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
<div class="col-12 col-md-12 col-lg-12 auth-main-col text-center">
<div class="d-flex flex-column align-content-end">
<div class="app-auth-body mx-auto col-10 col-md-10 col-lg-10">	
<div class="auth-form-container text-start ">
<?php echo $msg; ?>
<!--------------------------------------------------------------------------
| Create Scholarship Form::
|-------------------------------------------------------------------------->
	<form action="" method="post" enctype="multipart/form-data" class="auth-form login-form">         
	<div class="row">
            <div class="mb-3 col-6">
			<input name="scholarship_name" type="text" class="form-control" placeholder="Scholarship Name" required>
		</div>
		<div class="mb-3 col-6">
			<input name="image" type="file" class="form-control" accept=".jpg,.jpeg,.png,.gif,.pdf" required>
		</div>
	</div>
	<div class="row">
		<div class="mb-3 col-6">
			<!-- FIXED: unique id, removed wrong pattern (pattern should only be on amount) -->
			<input type="text" name="amount" class="form-control" id="amountInput" 
			       pattern="\d+(\.\d{1,2})?" 
			       placeholder="Enter amount (up to 2 decimals)" required>
		</div>
		<div class="mb-3 col-6">
			<!-- FIXED: removed copy-paste attributes (id="amountInput", pattern) -->
			<input type="date" name="date" class="form-control" required>
		</div>
	</div>
	<div class="row">
		<div class="mb-3 col-6">
		<select name="scholarship_status" class="form-control" required>
			<option value="">-- Select Status --</option>
			<option value="published">published</option>
			<option value="draft">draft</option>
		</select>
		</div>
		<div class="mb-3 col-6">
            <select name="added_by" class="form-control" required>
                <option value="">-- Select Admin --</option>
                <?php 
                    /*--------------------------------------------------------------------------
                    | FIXED: replace `WHERE role = 'admin'` with JOIN
                    | Also: value = username (text) because scholarships.added_by is VARCHAR
                    |--------------------------------------------------------------------------*/
                    $admins = mysqli_query(
                        $connection,
                        "SELECT u.id, u.username
                         FROM users u
                         LEFT JOIN roles r ON u.role_id = r.id
                         WHERE r.role_name = 'admin'
                         ORDER BY u.username ASC"
                    );
                    while ($admin = mysqli_fetch_assoc($admins)) {
                        echo '<option value="'.htmlspecialchars($admin['username']).'">'
                           . htmlspecialchars($admin['username'])
                           . '</option>';
                    }
                ?>
            </select>
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
} else {
    header("Location: login.php");
    exit;
}
?>
