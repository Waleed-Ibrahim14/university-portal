<?php
session_start();
	include_once("includes/header.php");
	include_once("includes/top_header.php");
	include_once("../admin/Models/DataBaseConnection.php");
?>

<body class="app">   	
    
    <div class="app-wrapper">
	    
	    <div class="app-content pt-3 p-md-3 p-lg-4">
		    <div class="container-xl">
			<?php
			    
				$announcements = mysqli_query($connection, "SELECT * FROM `announcements` ORDER BY `id` DESC");

				if (mysqli_num_rows($announcements) > 0) {
				while ($show_notifi = mysqli_fetch_assoc($announcements)) {
				echo '
                <div class="app-card app-card-notification shadow-sm mb-4">
				    <div class="app-card-header px-4 py-3">
				        <div class="row g-3 align-items-center">
					        <div class="col-12 col-lg-auto text-center text-lg-start">						        
				                <img class="profile-image" src="../assets/images/logo.png" alt="">
					        </div><!--//col-->
					        <div class="col-12 col-lg-auto text-center text-lg-start">
						        <div class="notification-type mb-2"><span class="badge bg-info">'.$show_notifi['username'].'</span></div>
						        <h4 class="notification-title mb-1">'.$show_notifi['title'].'</h4>
						        
						        <ul class="notification-meta list-inline mb-0">
							        <li class="list-inline-item">'.$show_notifi['notif_time'].'</li>
							        <li class="list-inline-item">|</li>
							        <li class="list-inline-item">Admin</li>
						        </ul>
						   
					        </div><!--//col-->
				        </div><!--//row-->
				    </div><!--//app-card-header-->
				    <div class="app-card-body p-4">
					    <div class="notification-content">'.$show_notifi['notif_msg'].'</div>
				    </div><!--//app-card-body-->
				    <div class="app-card-footer px-4 py-3">
				
				    </div><!--//app-card-footer-->
				</div><!--//app-card-->';
				}
			}
				?>
				
				
				<div class="text-center mt-4"><a class="btn app-btn-secondary" href="#">Load more notifications</a></div>
			    
		    </div><!--//container-fluid-->
	    </div><!--//app-content-->
	    
<?php
	include_once("includes/footer.php");
?>

