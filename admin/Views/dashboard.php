<?php
session_start();
// if ($_SESSION['role']  !== 'admin') { 
include_once("../Models/DataBaseConnection.php");
$get_user = mysqli_query($connection, "SELECT * FROM `users` WHERE `id` = '$_SESSION[id]'");
$user = mysqli_fetch_object($get_user);

    //   Count users
	$user = mysqli_query($connection, "SELECT * FROM `users`");
	$useres = mysqli_num_rows($user);
	//   Count scholarships
	$scholarships = mysqli_query($connection, "SELECT * FROM `scholarships`");
	$scholarshipsCount = mysqli_num_rows($scholarships);
	//   Count courses 
	$courses = mysqli_query($connection, "SELECT * FROM `courses`");
	$coursesCount = mysqli_num_rows($courses);
	//   Count teachers 
	$teacher = mysqli_query($connection, "SELECT * FROM `courses`");
	$teacherCount = mysqli_num_rows($teacher);
?>
<!DOCTYPE html>
<html lang="en"> 
<head>
    <title>University Portal</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="University Portal">
    <meta name="author" content="Waleed Ibrahim">    
    <link rel="shortcut icon" href="../assets/images/logo.png">
    <script defer src="../assets/plugins/fontawesome/js/all.min.js"></script>
    <link id="theme-style" rel="stylesheet" href="../assets/css/portal.css">
	<script src="../includes/notification/notification.js"></script>
</head>

<?php include_once("../includes/sidepanel.php"); ?>

<body class="app">   	
	<div class="app-wrapper">	    
		<div class="app-content pt-3 p-md-3 p-lg-4">
			<div class="container-xl">

<?php 

	include_once("../includes/statistics.php");
?>

			    <div class="row g-4 mb-4">
			        
			    </div><!--//row-->

			    <div class="row g-4 mb-4">
				       
			    </div><!--//row-->
			    			    
		    </div><!--//container-fluid-->
	    </div><!--//app-content-->
		<div class="row g-4 mb-4">
				       
		</div><!--//row-->
<?php
	include_once("../includes/footer.php");	
// }else{
// 	header("Location:login.php");
// }
?>
</body>
</html>