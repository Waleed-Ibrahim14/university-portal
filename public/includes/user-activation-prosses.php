<?php 
session_start();
include_once("../admin/Models/DataBaseConnection.php");
/*--------------------------------------------------------------------------
| User Activation Prosses::
|--------------------------------------------------------------------------*/
	$msg = ""; // Initialize the message variable
	if (isset($_SESSION['role'])) {
		$sql = mysqli_query($connection, "SELECT * FROM `users`  WHERE `username` = '$_SESSION[username]'");
		$cate = mysqli_fetch_assoc($sql);
	}
	if (isset($_POST['activation-account'])) {
		$user_status = $_SESSION['user_status'];
		if (empty($_POST['email'])) {
			$msg = '<div class="alert alert-danger" role="alert">Please Enter Your Email</div>';
		}elseif ($user_status  == 'active') {
				$msg = '<div class="alert alert-danger" role="alert">Your Account Is Alredy Active "'.$_SESSION['id'].'"</div>';
			}else {
			if (isset($_SESSION['email']) == ($_POST['email'] )) {	
				$sql = mysqli_query($connection, "UPDATE `users` SET `user_status` = 'active' , `updated_at` = NOW() WHERE `id` = '$_SESSION[id]'");
				if (isset($sql)) {
					$msg = '<div class="alert alert-success" role="alert">Activated successfully.</div>
                <meta http-equiv="refresh"content="2; \'../index.php\' "/>';
				}
			}
		}
	}         
?>