<?php
session_start();

/*--------------------------------------------------------------------------
| Backward-compatible role check.
|--------------------------------------------------------------------------*/
$current_role = $_SESSION['role_name'] ?? $_SESSION['role'] ?? '';

if ($current_role === 'admin') {
    include_once("../includes/header.php");
    include_once(__DIR__ . "/../Models/DataBaseConnection.php");

    $msg = '';

    /*--------------------------------------------------------------------------
    | Load the course being edited
    |--------------------------------------------------------------------------*/
    $courseId = isset($_GET['courseId']) ? (int) $_GET['courseId'] : 0;
    $course = null;

    if ($courseId > 0) {
        $stmt = $connection->prepare("SELECT * FROM courses WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $courseId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $course = $result->fetch_assoc();
        }
        $stmt->close();
    }

    /*--------------------------------------------------------------------------
    | If course not found, redirect back
    |--------------------------------------------------------------------------*/
    if (!$course) {
        header("Location: show-courses.php");
        exit;
    }

    /*--------------------------------------------------------------------------
    | Handle update submission
    |--------------------------------------------------------------------------*/
    if (isset($_POST['update_course'])) {

        $course_name = trim($_POST['course_name'] ?? '');
        $teacher_id  = isset($_POST['teacher_id']) && $_POST['teacher_id'] !== ''
                       ? (int) $_POST['teacher_id']
                       : null;

        /*--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------*/
        if (empty($course_name)) {
            $msg = '<div class="alert alert-danger" role="alert">Please enter course name</div>';
        } elseif (empty($teacher_id)) {
            $msg = '<div class="alert alert-danger" role="alert">Please select a teacher</div>';
        } else {
            /*--------------------------------------------------------------------------
            | Verify teacher exists
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
                | UPDATE with prepared statement
                |--------------------------------------------------------------------------*/
                $upd = $connection->prepare(
                    "UPDATE courses SET course_name = ?, teacher_id = ?, updated_at = NOW() WHERE id = ? LIMIT 1"
                );

                if ($upd === false) {
                    $msg = '<div class="alert alert-danger" role="alert">Database error: '
                         . htmlspecialchars($connection->error) . '</div>';
                } else {
                    $upd->bind_param("sii", $course_name, $teacher_id, $courseId);

                    if ($upd->execute()) {
                        $msg = '<div class="alert alert-success" role="alert">Course updated successfully</div>'
                             . '<meta http-equiv="refresh" content="2; url=show-courses.php" />';

                        // Reload fresh data for form display
                        $course['course_name'] = $course_name;
                        $course['teacher_id']  = $teacher_id;
                    } else {
                        $msg = '<div class="alert alert-danger" role="alert">Error updating course</div>';
                    }
                    $upd->close();
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
			            <h1 class="app-page-title mb-0">Update Course</h1>
				    </div>
				</div>
			</div>
			   
			<div class="col-12 col-md-12 col-lg-12 auth-main-col text-center p-5">
				<div class="d-flex flex-column align-content-end">
					<div class="app-auth-body mx-auto col-6 col-md-6 col-lg-6">	
						<div class="auth-form-container text-start ">

							<?php if (!empty($msg)) echo $msg; ?>

							<form action="" method="post" class="auth-form login-form">
								<div class="mb-3">
									<label>Course Name</label>
									<input name="course_name" type="text" class="form-control" 
									       value="<?php echo htmlspecialchars($course['course_name']); ?>" required>
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
												$selected = ($course['teacher_id'] == $teacher['id']) ? ' selected' : '';
												echo '<option value="'.(int)$teacher['id'].'"'.$selected.'>'
												   . htmlspecialchars($teacher['username'])
												   . '</option>';
											}
										?>
									</select>
								</div>

								<div class="text-center">
									<button type="submit" name="update_course" class="btn app-btn-primary w-100 theme-btn mx-auto">Update Course</button>
									<a href="show-courses.php" class="btn app-btn-secondary w-100 theme-btn mx-auto mt-2">Cancel</a>
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
