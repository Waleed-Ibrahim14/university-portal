<?php
session_start();

/*--------------------------------------------------------------------------
| Backward-compatible role check.
|--------------------------------------------------------------------------*/
$current_role = $_SESSION['role_name'] ?? $_SESSION['role'] ?? '';

if ($current_role !== 'admin') {
    include_once("../includes/header.php");
    include_once(__DIR__ . "/../Models/DataBaseConnection.php");

    $msg = '';

    /*--------------------------------------------------------------------------
    | Handle form submission
    |--------------------------------------------------------------------------*/
    if (isset($_POST['create_class'])) {

        $class_name = trim($_POST['class_name'] ?? '');
        $teacher_id = isset($_POST['teacher_id']) && $_POST['teacher_id'] !== ''
                      ? (int) $_POST['teacher_id']
                      : null;

        /*--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------*/
        if (empty($class_name)) {
            $msg = '<div class="alert alert-danger" role="alert">Please enter class name</div>';
        } elseif (empty($teacher_id)) {
            $msg = '<div class="alert alert-danger" role="alert">Please select a teacher</div>';
        } else {

            /*--------------------------------------------------------------------------
            | Verify teacher exists (FK integrity)
            |--------------------------------------------------------------------------*/
            $chk = $connection->prepare("SELECT id FROM users WHERE id = ? LIMIT 1");
            $chk->bind_param("i", $teacher_id);
            $chk->execute();
            $teacherExists = $chk->get_result()->num_rows > 0;
            $chk->close();

            if (!$teacherExists) {
                $msg = '<div class="alert alert-danger" role="alert">Selected teacher does not exist</div>';
            } else {

                /*--------------------------------------------------------------------------
                | INSERT with prepared statement
                |--------------------------------------------------------------------------*/
                $stmt = $connection->prepare(
                    "INSERT INTO classes (class_name, teacher_id, created_at, updated_at)
                     VALUES (?, ?, NOW(), NOW())"
                );

                if ($stmt === false) {
                    $msg = '<div class="alert alert-danger" role="alert">Database error: '
                         . htmlspecialchars($connection->error) . '</div>';
                } else {
                    $stmt->bind_param("si", $class_name, $teacher_id);

                    if ($stmt->execute()) {
                        $msg = '<div class="alert alert-success" role="alert">Class created successfully</div>'
                             . '<meta http-equiv="refresh" content="2; url=show-classes.php" />';
                    } else {
                        $msg = '<div class="alert alert-danger" role="alert">Error saving class</div>';
                    }
                    $stmt->close();
                }
            }
        }
    }

    include_once("../includes/sidepanel.php");
?>
<body class="app">   	
	<div class="app-wrapper">
		<div class="app-content pt-3 p-md-3 p-lg-5">
			<div class="container-xl">
				<div class="row g-3 mb-4 align-items-center justify-content-between">
					<div class="col-auto">
						<h1 class="app-page-title mb-0">Create New Class</h1>
					</div>
				</div>
			</div>			   
			<div class="col-12 col-md-12 col-lg-12 auth-main-col text-center">
				<div class="d-flex flex-column align-content-end">
					<div class="app-auth-body mx-auto col-6 col-md-6 col-lg-6">	
						<div class="auth-form-container text-start ">
							<?php echo $msg; ?>
							<form class="auth-form login-form" action="" method="post">
								<div class="mb-3">
									<label>Class Name</label>
									<input name="class_name" type="text" class="form-control" placeholder="Enter Class Name" required>
								</div>

								<div class="mb-3">
									<label>Teacher</label>
									<select name="teacher_id" class="form-control" required>
										<option value="">-- Select Teacher --</option>
										<?php 
											$teachers = mysqli_query(
												$connection,
												"SELECT u.id, u.username
												 FROM users u
												 LEFT JOIN roles r ON u.role_id = r.id
												 WHERE r.role_name = 'teacher'
												 ORDER BY u.username ASC"
											);
											while ($teacher = mysqli_fetch_assoc($teachers)) {
												echo '<option value="'.(int)$teacher['id'].'">'
												   . htmlspecialchars($teacher['username'])
												   . '</option>';
											}
										?>
									</select>
								</div>

								<div class="text-center">
									<button type="submit" name="create_class" class="btn app-btn-primary w-100 theme-btn mx-auto">Create Class</button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php
	include_once("../includes/footer.php");	
} else {
    header("Location: login.php");
    exit;
}
?>
