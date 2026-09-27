<?php
session_start();

/*--------------------------------------------------------------------------
| Backward-compatible role check.
|--------------------------------------------------------------------------*/
$current_role = $_SESSION['role_name'] ?? $_SESSION['role'] ?? '';

if ($current_role === 'admin') {
    include_once("../Models/DataBaseConnection.php");

    $msg = '';

    /*--------------------------------------------------------------------------
    | Handle form submission.
    |--------------------------------------------------------------------------*/
    if (isset($_POST['create_referral'])) {
        $student_id      = isset($_POST['student_id']) && $_POST['student_id'] !== ''
                            ? (int)$_POST['student_id']
                            : null;
        $referral_reason = trim($_POST['referral_reason'] ?? '');
        $date            = trim($_POST['date'] ?? '');

        if (empty($student_id)) {
            $msg = '<div class="alert alert-danger">Please select a student</div>';
        } elseif (empty($referral_reason)) {
            $msg = '<div class="alert alert-danger">Please enter a referral reason</div>';
        } elseif (empty($date)) {
            $msg = '<div class="alert alert-danger">Please select a date</div>';
        } else {
            $stmt = $connection->prepare(
                "INSERT INTO referrals (student_id, referral_reason, date) VALUES (?, ?, ?)"
            );
            $stmt->bind_param("iss", $student_id, $referral_reason, $date);
            if ($stmt->execute()) {
                $msg = '<div class="alert alert-success">Referral Created Successfully</div>'
                     . '<meta http-equiv="refresh" content="2; \'Referrals.php\'" />';
            } else {
                $msg = '<div class="alert alert-danger">Database error: ' . htmlspecialchars($stmt->error) . '</div>';
            }
            $stmt->close();
        }
    }

    include_once("header.php");
?>

<body class="app">   	
<?php include_once("sidepanel.php"); ?>
    
    <div class="app-wrapper">
	    <div class="app-content pt-3 p-md-3 p-lg-4">
		    <div class="container-xl">
			    <div class="row g-3 mb-4 align-items-center justify-content-between">
				    <div class="col-auto">
			            <h1 class="app-page-title mb-0">Create New Referral</h1>
				    </div>
			    </div>

<div class="col-10 col-md-10 col-lg-10 auth-main-col text-center">
<div class="d-flex flex-column align-content-end">
<div class="app-auth-body mx-auto">	
<div class="auth-form-container text-start">

<?php echo $msg; ?>

<form class="auth-form" action="" method="post">
    
    <div class="mb-3">
        <label for="student_id" class="form-label">Student</label>
        <select name="student_id" id="student_id" class="form-control" required>
            <option value="">-- Select Student --</option>
            <?php
                $students = mysqli_query(
                    $connection,
                    "SELECT u.id, u.fullname 
                     FROM users u 
                     LEFT JOIN roles r ON u.role_id = r.id 
                     WHERE r.role_name = 'user' 
                     ORDER BY u.fullname ASC"
                );
                while ($student = mysqli_fetch_assoc($students)) {
                    echo '<option value="'.(int)$student['id'].'">'
                        . htmlspecialchars($student['fullname'])
                        . '</option>';
                }
            ?>
        </select>
    </div>

    <div class="mb-3">
        <label for="referral_reason" class="form-label">Referral Reason</label>
        <textarea name="referral_reason" id="referral_reason" class="form-control" rows="4" required></textarea>
    </div>

    <div class="mb-3">
        <label for="date" class="form-label">Date</label>
        <input type="date" name="date" id="date" class="form-control" required>
    </div>

    <div class="text-center mb-3">
        <button type="submit" name="create_referral" class="btn app-btn-primary w-100 theme-btn mx-auto">Create Referral</button>
    </div>
</form>

</div>
</div>
<?php
	include_once("footer.php");	
} else {
	header("Location: login.php");
	exit;
}
?>
