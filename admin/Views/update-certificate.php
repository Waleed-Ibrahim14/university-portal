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
    | Load existing certificate (Prepared Statement)
    |--------------------------------------------------------------------------*/
    $certificateId = isset($_GET['certificateId']) ? (int) $_GET['certificateId'] : 0;
    $certifiupdate = null;

    if ($certificateId > 0) {
        $stmt = $connection->prepare("SELECT * FROM certificates WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $certificateId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $certifiupdate = $result->fetch_assoc();
        }
        $stmt->close();
    }

    if (!$certifiupdate) {
        // Certificate not found — redirect back
        header("Location: show-certificates.php");
        exit;
    }

    /*--------------------------------------------------------------------------
    | Handle update submission
    |--------------------------------------------------------------------------*/
    if (isset($_POST['update_certificate'])) {

        /*--------------------------------------------------------------------------
        | Read inputs
        |--------------------------------------------------------------------------*/
        $certificate_name = trim($_POST['certificate_name'] ?? '');
        $issue_date       = trim($_POST['issue_date'] ?? '');
        $teacher_id       = isset($_POST['teacher_id']) && $_POST['teacher_id'] !== ''
                            ? (int) $_POST['teacher_id']
                            : null;
        $course_id        = isset($_POST['course_id']) && $_POST['course_id'] !== ''
                            ? (int) $_POST['course_id']
                            : null;

        // File handling (optional on update)
        $newFileUploaded = isset($_FILES['certificate_file'])
                          && $_FILES['certificate_file']['error'] === UPLOAD_ERR_OK
                          && !empty($_FILES['certificate_file']['name']);

        $certificate_file = $certifiupdate['certificate_file']; // default: keep old
        $target           = null;
        $imageFileType    = null;
        $maxFileSize      = 5 * 1024 * 1024; // 5 MB

        if ($newFileUploaded) {
            $certificate_file = $_FILES['certificate_file']['name'];
            $target           = "../../assets/images/certificates/" . basename($certificate_file);
            $imageFileType    = strtolower(pathinfo($target, PATHINFO_EXTENSION));
        }

        /*--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------*/
        if (empty($certificate_name)) {
            $msg = '<div class="alert alert-danger" role="alert">Please enter certificate name</div>';
        } elseif (empty($issue_date)) {
            $msg = '<div class="alert alert-danger" role="alert">Please select issue date</div>';
        } elseif (empty($teacher_id)) {
            $msg = '<div class="alert alert-danger" role="alert">Please select teacher</div>';
        } elseif (empty($course_id)) {
            $msg = '<div class="alert alert-danger" role="alert">Please select course</div>';
        } elseif ($newFileUploaded && $imageFileType !== "pdf") {
            $msg = '<div class="alert alert-danger" role="alert">Choose a correct file (*.pdf)</div>';
        } elseif ($newFileUploaded && $_FILES["certificate_file"]["size"] > $maxFileSize) {
            $msg = '<div class="alert alert-danger" role="alert">File size too big (max 5 MB)</div>';
        } elseif ($newFileUploaded && file_exists($target)) {
            $msg = '<div class="alert alert-danger" role="alert">This certificate file was uploaded before</div>';

        } else {
            /*--------------------------------------------------------------------------
            | Verify FK targets exist
            |--------------------------------------------------------------------------*/
            $checkTeacher = $connection->prepare("SELECT id FROM users WHERE id = ? LIMIT 1");
            $checkTeacher->bind_param("i", $teacher_id);
            $checkTeacher->execute();
            $teacherExists = $checkTeacher->get_result()->num_rows > 0;
            $checkTeacher->close();

            $checkCourse = $connection->prepare("SELECT id FROM courses WHERE id = ? LIMIT 1");
            $checkCourse->bind_param("i", $course_id);
            $checkCourse->execute();
            $courseExists = $checkCourse->get_result()->num_rows > 0;
            $checkCourse->close();

            if (!$teacherExists) {
                $msg = '<div class="alert alert-danger" role="alert">Selected teacher does not exist</div>';
            } elseif (!$courseExists) {
                $msg = '<div class="alert alert-danger" role="alert">Selected course does not exist</div>';
            } else {
                /*--------------------------------------------------------------------------
                | UPDATE with prepared statement
                |--------------------------------------------------------------------------*/
                $stmt = $connection->prepare(
                    "UPDATE certificates SET
                        certificate_name = ?,
                        issue_date       = ?,
                        teacher_id       = ?,
                        course_id        = ?,
                        certificate_file = ?,
                        updated_at       = NOW()
                     WHERE id = ? LIMIT 1"
                );

                if ($stmt === false) {
                    $msg = '<div class="alert alert-danger" role="alert">Database error: ' . htmlspecialchars($connection->error) . '</div>';
                } else {
                    $stmt->bind_param(
                        "ssiisi",
                        $certificate_name,
                        $issue_date,
                        $teacher_id,
                        $course_id,
                        $certificate_file,
                        $certificateId
                    );

                    if ($stmt->execute()) {
                        // Move new file if uploaded
                        if ($newFileUploaded && !move_uploaded_file($_FILES['certificate_file']['tmp_name'], $target)) {
                            $msg = '<div class="alert alert-warning" role="alert">Certificate updated, but file upload failed</div>';
                        } else {
                            $msg = '<div class="alert alert-success" role="alert">Certificate updated successfully</div>'
                                 . '<meta http-equiv="refresh" content="2; url=show-certificates.php" />';
                        }
                    } else {
                        $msg = '<div class="alert alert-danger" role="alert">Database error: ' . htmlspecialchars($stmt->error) . '</div>';
                    }
                    $stmt->close();
                }
            }
        }

        /*--------------------------------------------------------------------------
        | Reload fresh data after successful update
        |--------------------------------------------------------------------------*/
        if (strpos($msg, 'alert-success') !== false) {
            $stmt = $connection->prepare("SELECT * FROM certificates WHERE id = ? LIMIT 1");
            $stmt->bind_param("i", $certificateId);
            $stmt->execute();
            $certifiupdate = $stmt->get_result()->fetch_assoc();
            $stmt->close();
        }
    }
?>
<body class="app"> 
	<?php include_once("../includes/sidepanel.php"); ?>  	
<div class="app-wrapper">
<div class="app-content pt-3 p-md-3 p-lg-5">
<div class="container-xl">
<div class="row g-3 mb-4 align-items-center justify-content-between">
<div class="col-auto">
<h1 class="app-page-title mb-0">Update Certificate</h1>
</div>
</div>
</div>			   
<div class="col-10 col-md-10 col-lg-10 auth-main-col text-center p-6">
<div class="d-flex flex-column align-content-end">
<div class="app-auth-body mx-auto ">	
<div class="auth-form-container text-start ">
<?php echo $msg; ?>
        <form action="" method="post" enctype="multipart/form-data">         		
            <div class="row">
            <div class="mb-3 col-6">
            <input name="student_id_display" value="<?php echo (int)$certifiupdate['student_id']; ?>" type="text" class="form-control" disabled>
            </div>
            <div class="mb-3 col-6">
				<input name="certificate_name" value="<?php echo htmlspecialchars($certifiupdate['certificate_name']); ?>" type="text" class="form-control" placeholder="Certificate name" required>
			</div>
			</div>
            <div class="row">
			<div class="mb-3 col-12">
				<input name="issue_date" value="<?php echo htmlspecialchars($certifiupdate['issue_date']); ?>" type="date" class="form-control" required>
			</div>
            </div>
            <div class="row">
            <div class="mb-3 col-6">
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
                        $selected = ($certifiupdate['teacher_id'] == $teacher['id']) ? ' selected' : '';
                        echo '<option value="'.(int)$teacher['id'].'"'.$selected.'>'
                           . htmlspecialchars($teacher['username'])
                           . '</option>';
                    }
                ?>
            </select>
            </div>
            <div class="mb-3 col-6">
            <select name="course_id" class="form-control" required>
                <option value="">-- Select Course --</option>
                <?php 
                    $courses = mysqli_query(
                        $connection,
                        "SELECT id, course_name FROM courses ORDER BY course_name ASC"
                    );
                    while ($course = mysqli_fetch_assoc($courses)) {
                        $selected = ($certifiupdate['course_id'] == $course['id']) ? ' selected' : '';
                        echo '<option value="'.(int)$course['id'].'"'.$selected.'>'
                           . htmlspecialchars($course['course_name'])
                           . '</option>';
                    }
                ?>
            </select>
            </div>
            </div>
            <div class="row">
			<div class="mb-3">
				<label>Current file: <a href="../../assets/images/certificates/<?php echo htmlspecialchars($certifiupdate['certificate_file']); ?>"><?php echo htmlspecialchars($certifiupdate['certificate_file']); ?></a></label>
				<input name="certificate_file" type="file" accept=".pdf" class="form-control">
				<small class="text-muted">Leave empty to keep the current file</small>
			</div>
			</div>
			<div class="mb-3">
				<button type="submit" name="update_certificate" class="btn app-btn-primary w-100 theme-btn mx-auto">Update Certificate</button>
				<a href="show-certificates.php" class="btn app-btn-secondary w-100 theme-btn mx-auto mt-2">Cancel</a>
			</div>
		</form>
</div>
</div>
<?php
	include_once("../includes/footer.php");	
} else {
    header("Location: login.php");
    exit;
}
?>
