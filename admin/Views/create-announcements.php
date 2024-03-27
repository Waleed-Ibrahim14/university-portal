<?php
session_start();
if ($_SESSION['role']  !== 'admin') { 
	include_once("../includes/header.php");
?>
<body class="app"> 
<?php include_once("../includes/sidepanel.php"); ?>  	
<div class="app-wrapper">
<div class="app-content pt-3 p-md-3 p-lg-5">
<div class="container-xl">
<div class="row g-3 mb-4 align-items-center justify-content-between">
<div class="col-auto">
<h1 class="app-page-title mb-0">Create New announcement</h1>
</div>
</div>
</div>			   
<div class="col-12 col-md-12 col-lg-12 auth-main-col text-center">
<div class="d-flex flex-column align-content-end">
<div class="app-auth-body mx-auto col-10 col-md-10 col-lg-10">	
<div class="auth-form-container text-start ">
    
	<?php include_once("../includes/create-announcement-prosess.php");?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="auth-form login-form">         
        <div class="row">	
        <div class="mb-3 col-5 col-md-5 col-lg-5">
        <tr><td>Title</td>
			<input name="title" type="text" class="form-control" placeholder="Enter Announcement Title" >
            </tr>
        </div>
		<div class=" mb-3 col-5 col-md-5 col-lg-5">
            <tr><td>Broadcast time</td>
                <select name="time" class="form-control"><option>Now</option></select>
            </tr>
        </div>
        </div>
        <div class="row">
        <div class="mb-3 col-3 col-md-3 col-lg-3">
            <tr><td>Loop (time)</td>
                <select name="loops" class="form-control">
                    <?php 
                        for ($i=1; $i<=5 ; $i++) { ?>
                            <option value="<?php echo $i ?>"><?php echo $i ?></option>
                    <?php } ?>
                </select>
            </tr>
        </div>

		<div class="mb-3 col-5 col-3 col-md-3 col-lg-3">
            <tr><td>Loop Every (Minute)</td>
                <select name="loop_every" class="form-control">
                    <?php 
                    for ($i=1; $i<=60 ; $i++) { ?>
                        <option value="<?php echo $i ?>"><?php echo $i ?></option>
                    <?php } ?>
                </select>
            </tr>
        </div>
        <div class="mb-3 col-5 col-3 col-md-3 col-lg-3">
        <tr><td>For</td>
		    <select name="user" class="form-control">
            <option value=""></option>
                <?php 		
                    $user = $push->listUsers(); 
                    foreach ($user as $key) {
                ?>
                    <option value="<?php echo $key['username'] ?>"><?php echo $key['username'] ?></option>
            <?php } ?>
            </select></tr>
        </div>
        </div>
            <tr><td>Announcement</td>
		<div class="mb-3">
			<textarea name="msg" class="form-control tinymce" rows="8"></textarea>
		</div></tr>
        <div class="text-center col-5 col-md-5 col-lg-5">
			<button type="submit" name="submit" class="btn app-btn-primary w-100 theme-btn mx-auto">Create Notification</button>
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
}else{
	header("Location:login.php");
}
?>