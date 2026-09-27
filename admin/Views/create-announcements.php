<?php
session_start();

/*--------------------------------------------------------------------------
| Backward-compatible role check.
|--------------------------------------------------------------------------*/
$current_role = $_SESSION['role_name'] ?? $_SESSION['role'] ?? '';

if ($current_role !== 'admin') {
    include_once("../includes/header.php");

    /*--------------------------------------------------------------------------
    | Include Push class FIRST (so $push is available for listUsers() below)
    |--------------------------------------------------------------------------*/
    include_once(__DIR__ . "/../includes/notification/Push.php");
    $push = new Push();

    /*--------------------------------------------------------------------------
    | FIXED: corrected filename (was create-announcement-prosess.php — typo)
    |--------------------------------------------------------------------------*/
    include_once("../includes/create-announcements-prosses.php");
?>
<body class="app"> 
<?php include_once("../includes/sidepanel.php"); ?>  	
<div class="app-wrapper">
<div class="app-content pt-3 p-md-3 p-lg-5">
<div class="container-xl">
<div class="row g-3 mb-4 align-items-center justify-content-between">
<div class="col-auto">
<h1 class="app-page-title mb-0">Create New Announcement</h1>
</div>
</div>
</div>			   
<div class="col-12 col-md-12 col-lg-12 auth-main-col text-center">
<div class="d-flex flex-column align-content-end">
<div class="app-auth-body mx-auto col-10 col-md-10 col-lg-10">	
<div class="auth-form-container text-start ">

    <?php if (!empty($message)) echo $message; ?>

	<form action="" method="post" class="auth-form login-form">         
        <div class="row">	
        <div class="mb-3 col-5 col-md-5 col-lg-5">
            <label>Title</label>
			<input name="title" type="text" class="form-control" placeholder="Enter Announcement Title" required>
        </div>
		<div class="mb-3 col-5 col-md-5 col-lg-5">
            <label>Broadcast time</label>
            <select name="time" class="form-control">
                <option value="Now">Now</option>
            </select>
        </div>
        </div>
        <div class="row">
        <div class="mb-3 col-3 col-md-3 col-lg-3">
            <label>Loop (times)</label>
            <select name="loops" class="form-control" required>
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <option value="<?php echo $i ?>"><?php echo $i ?></option>
                <?php endfor; ?>
            </select>
        </div>

		<div class="mb-3 col-3 col-md-3 col-lg-3">
            <label>Loop every (minutes)</label>
            <select name="loop_every" class="form-control" required>
                <?php for ($i = 1; $i <= 60; $i++): ?>
                    <option value="<?php echo $i ?>"><?php echo $i ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="mb-3 col-3 col-md-3 col-lg-3">
            <label>For user</label>
            <select name="user" class="form-control" required>
                <option value="">-- Select User --</option>
                <?php 
                    /*--------------------------------------------------------------------------
                    | Use Push::listUsers() to get the list (already implemented in the class)
                    |--------------------------------------------------------------------------*/
                    $users = $push->listUsers(); 
                    foreach ($users as $key) {
                        echo '<option value="'.htmlspecialchars($key['username']).'">'
                           . htmlspecialchars($key['username'])
                           . '</option>';
                    }
                ?>
            </select>
        </div>
        </div>
        <div class="mb-3">
            <label>Announcement</label>
            <textarea name="msg" class="form-control tinymce" rows="8" required></textarea>
        </div>
        <div class="text-center col-5 col-md-5 col-lg-5">
			<button type="submit" name="submit" class="btn app-btn-primary w-100 theme-btn mx-auto">Create Announcement</button>
		</div>
	</form>
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
