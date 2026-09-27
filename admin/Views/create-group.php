<?php
session_start();

/*--------------------------------------------------------------------------
| Backward-compatible role check.
|--------------------------------------------------------------------------*/
$current_role = $_SESSION['role_name'] ?? $_SESSION['role'] ?? '';

if ($current_role === 'admin') {
    /*--------------------------------------------------------------------------
    | Include DB connection BEFORE the form (in case the process file needs it).
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
<h1 class="app-page-title mb-0">Create New Group</h1>
</div>
</div>
</div>			   
<div class="col-12 col-md-12 col-lg-12 auth-main-col text-center">
<div class="d-flex flex-column align-content-end">
<div class="app-auth-body mx-auto col-10 col-md-10 col-lg-10">	
<div class="auth-form-container text-start ">
    
	<?php 
    /*--------------------------------------------------------------------------
    | Include process file (handles POST validation + INSERT).
    |--------------------------------------------------------------------------*/
    include_once("../includes/create-group-prosses.php");
    ?>

	<?php if (!empty($message)) echo $message; ?>

	<form action="" method="post" class="auth-form login-form">         
        <div class="row">
		<div class="mb-3 col-5 col-md-5 col-lg-5">
			<input name="group_name" type="text" class="form-control" placeholder="Enter group Name" required>
            </div>
        </div>
        <div class="text-center col-3">
			<button type="submit" name="create_group" class="btn app-btn-primary w-100 theme-btn mx-auto">Create Group</button>
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
