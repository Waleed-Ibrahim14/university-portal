<?php
	if (isset($_SESSION['id'])) { 
		header("Location:login.php");
	}
	include_once("../includes/header.php");
    include_once("../includes/notification/Push.php");  
    $push = new Push();
?>
<body class="app"> 
	<?php include_once("../includes/sidepanel.php"); ?>  	
<div class="app-wrapper">
<div class="app-content pt-3 p-md-3 p-lg-5">
<div class="container-xl">
<div class="row g-3 mb-4 align-items-center justify-content-between">
<div class="col-auto">
<h1 class="app-page-title mb-0">Create New Classes</h1>
</div>
</div>
</div>			   
<div class="col-12 col-md-12 col-lg-12 auth-main-col text-center p-5">
<div class="d-flex flex-column align-content-end">
<div class="app-auth-body mx-auto col-10 col-md-10 col-lg-10">	
<div class="auth-form-container text-start ">
	<?php 	if (isset($_POST['submit'])) { 
		if(isset($_POST['msg']) and isset($_POST['time']) and isset($_POST['loops']) and isset($_POST['loop_every']) and isset($_POST['user'])) {
			$title = $_POST['title'];	
			$msg = $_POST['msg']; 
			$time = date('Y-m-d H:i:s'); 
			$loop= $_POST['loops']; 
			$loop_every=$_POST['loop_every']; 
			$user = $_POST['user']; 
            if(empty($title)){
				echo $message = '<div class="alert alert-danger" role="alert">Enter The Notification Title</div>';
            }else if(empty($msg)){
				echo $message = '<div class="alert alert-danger" role="alert">Enter The Notification Body</div>';
            }else if(empty($time)){
				echo $message = '<div class="alert alert-danger" role="alert">Enter The Broadcast time</div>';
            }else if(empty($loop)){
				echo $message = '<div class="alert alert-danger" role="alert">Enter Loop (time) Number </div>';
            }else if(empty($loop_every)){
				echo $message = '<div class="alert alert-danger" role="alert">Enter Loop Every (Minute)</div>';
            }else if(empty($user)){
				echo $message = '<div class="alert alert-danger" role="alert">Enter The User </div>';
            }else{
                $isSaved = $push->saveNotification($title, $msg,$time,$loop,$loop_every,$user);
                if($isSaved) {
                    echo $message = '<div class="alert alert-success" role="alert">Notification  Saved successfully</div>';
                } else {
                    echo $message = '<div class="alert alert-danger" role="alert">Notification  error save data</div>';
                }
            }
		} else {
			echo  $message = '<div class="alert alert-danger" role="alert">completed the parameter above</div>';
		}
	} 
    ?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="auth-form login-form">         
		<div class="email mb-3 col-5 col-md-5 col-lg-5">
        <tr><td>Title</td>
			<input name="title" type="text" class="form-control signin-email" placeholder="Enter Announcement Title" >
            </tr></div>
            <tr><td>Announcement</td>
		<div class="email mb-3">
			<textarea name="msg" class="form-control tinymce"   id="scholarship_description" rows="8"></textarea>
		</div></tr>
		<div class="email mb-3 col-5 col-md-5 col-lg-5">
            <tr><td>Broadcast time</td>
                <select name="time" class="form-control"><option>Now</option></select>
            </tr>
        </div>
        <div class="email mb-3 col-5 col-md-5 col-lg-5">
            <tr><td>Loop (time)</td>
                <select name="loops" class="form-control">
                    <?php 
                        for ($i=1; $i<=5 ; $i++) { ?>
                            <option value="<?php echo $i ?>"><?php echo $i ?></option>
                    <?php } ?>
                </select>
            </tr>
        </div>
		<div class="mb-3 col-5 col-md-5 col-lg-5">
            <tr><td>Loop Every (Minute)</td>
                <select name="loop_every" class="form-control">
                    <?php 
                    for ($i=1; $i<=60 ; $i++) { ?>
                        <option value="<?php echo $i ?>"><?php echo $i ?></option>
                    <?php } ?>
                </select>
            </tr>
        </div>
        <div class="mb-3 col-5 col-md-5 col-lg-5">
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
?>