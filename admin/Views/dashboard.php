<?php
session_start();

/*--------------------------------------------------------------------------
| Authorization check.
| NOTE: The original file had this check fully commented out,
|       which made the page publicly accessible (even to non-logged-in users).
|       We now require a valid session; the admin-only intent is preserved
|       in the commented variant below if you want it stricter.
|--------------------------------------------------------------------------*/
if (empty($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

// Optional (stricter): uncomment to allow admins only
// $current_role = $_SESSION['role_name'] ?? $_SESSION['role'] ?? '';
// if ($current_role !== 'admin') { header("Location: login.php"); exit; }

include_once("../Models/DataBaseConnection.php");

/*--------------------------------------------------------------------------
| Load the currently logged-in user (prepared statement + JOIN with roles).
|
| NOTE: The original file loaded $user TWICE with different meanings:
|   1) $user = mysqli_fetch_object($get_user);                 // user object
|   2) $user = mysqli_query(..."SELECT * FROM users");         // result set
| This name collision overwrote the user object and could break
| sidepanel.php (which expects $user to be the user object).
|
| We now keep $user = user object, and use $usersResult for the count query.
|--------------------------------------------------------------------------*/
$user = null;

$stmt = $connection->prepare(
    "SELECT u.*, r.role_name AS role_name
     FROM users u
     LEFT JOIN roles r ON u.role_id = r.id
     WHERE u.id = ?
     LIMIT 1"
);

if ($stmt !== false) {
    $session_id = (int) $_SESSION['id'];
    $stmt->bind_param("i", $session_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $user = mysqli_fetch_object($result);
        $user->role = $user->role_name; // backward-compat alias
    }
    $stmt->close();
}

/*--------------------------------------------------------------------------
| Dashboard counters.
| Variable names are kept IDENTICAL to the original file, because
| includes/statistics.php depends on them:
|   - $usersCount
|   - $scholarshipsCount
|   - $coursesCount
|   - $teacherCount
|
| Two logic fixes:
|   - $teacherCount was previously counting COURSES (copy-paste bug).
|   - We now use COUNT(*) instead of SELECT * + mysqli_num_rows (faster).
|--------------------------------------------------------------------------*/

// Count all users
$usersResult      = mysqli_query($connection, "SELECT COUNT(*) AS total FROM users");
usersCount           = $usersResult ? (int)mysqli_fetch_assoc($usersResult)['total'] : 0;

// Count scholarships
$scholarships     = mysqli_query($connection, "SELECT COUNT(*) AS total FROM scholarships");
$scholarshipsCount= $scholarships ? (int)mysqli_fetch_assoc($scholarships)['total'] : 0;

// Count courses
$courses          = mysqli_query($connection, "SELECT COUNT(*) AS total FROM courses");
$coursesCount     = $courses ? (int)mysqli_fetch_assoc($courses)['total'] : 0;

// Count teachers (FIXED: previously queried `courses` by mistake)
$teacherQuery     = mysqli_query(
    $connection,
    "SELECT COUNT(*) AS total
     FROM users u
     LEFT JOIN roles r ON u.role_id = r.id
     WHERE r.role_name = 'teacher'"
);
$teacherCount     = $teacherQuery ? (int)mysqli_fetch_assoc($teacherQuery)['total'] : 0;
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
?>
</body>
</html>
