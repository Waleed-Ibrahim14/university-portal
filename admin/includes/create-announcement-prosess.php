<?php 	
/*--------------------------------------------------------------------------
| Create Certificate Prosses::
|--------------------------------------------------------------------------*/
include_once("../Models/DataBaseConnection.php");
include_once("notification/Push.php");  
    $push = new Push();
    $message = '';
if (isset($_POST['submit'])) { 
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