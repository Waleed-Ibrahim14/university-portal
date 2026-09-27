<?php
/*--------------------------------------------------------------------------
| Create Certificate Process ::
|--------------------------------------------------------------------------*/
include_once(__DIR__ . "/../Models/DataBaseConnection.php");

$msg = '';

if (isset($_POST['create_certificate'])) {

    /*--------------------------------------------------------------------------
    | Read and sanitize inputs
    |--------------------------------------------------------------------------*/
    $student_id       = isset($_POST['student_id']) && $_POST['student_id'] !== ''
                        ? (int) $_POST['student_id']
                        : null;
    $certificate_name = trim($_POST['certificate_name'] ?? '');
    $issue_date       = trim($_POST['issue_date'] ?? '');
    $teacher_id       = isset($_POST['teacher_id']) && $_POST['teacher_id'] !== ''
                        ? (int) $_POST['teacher_id']
                        : null;
    $course_id        = isset($_POST['course_id']) && $_POST['course_id'] !== ''
                        ? (int) $_POST['course_id']
                        : null;

    // File info
    $certificate_file = $_FILES['certificate_file']['name'] ?? '';
    $target           = "../../assets/images/certificates/" . basename($certificate_file);
    $imageFileType    = strtolower(pathinfo($target, PATHINFO_EXTENSION));
    $maxFileSize      = 5 * 1024 * 1024; // 5 MB

    /*--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------*/
    if (empty($student_id)) {
        $msg = '<div class="alert alert-danger" role="alert">Please select student</div>';
    } elseif (empty($certificate_name)) {
        $msg = '<div class="alert alert-danger" role="alert">Please enter certificate name</div>';
    } elseif (empty($issue_date)) {
        $msg = '<div class="alert alert-danger" role="alert">Please select issue date</div>';
    } elseif (empty($teacher_id)) {
        $msg = '<div class="alert alert-danger" role="alert">Please select teacher</div>';
    } elseif (empty($course_id)) {
        $msg = '<div class="alert alert-danger" role="alert">Please select course</div>';
    } elseif ($imageFileType !== "pdf") {
        $msg = '<div class="alert alert-danger" role="alert">Choose a correct file (*.pdf)</div>';
    } elseif (($_FILES["certificate_file"]["size"] ?? 0) > $maxFileSize) {
        $msg = '<div class="alert alert-danger" role="alert">File size too big (max 5 MB)</div>';
    } elseif (file_exists($target)) {
        $msg = '<div class="alert alert-danger" role="alert">This certificate file was uploaded before</div>';

    } else {
        /*--------------------------------------------------------------------------
        | Verify FK targets exist
        |--------------------------------------------------------------------------*/
        $checkStudent = $connection->prepare("SELECT id FROM users WHERE id = ? LIMIT 1");
        $checkStudent->bind_param("i", $student_id);
        $checkStudent->execute();
        $studentExists = $checkStudent->get_result()->num_rows > 0;
        $checkStudent->close();

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

        if (!$studentExists) {
            $msg = '<div class="alert alert-danger" role="alert">Selected student does not exist</div>';
        } elseif (!$teacherExists) {
            $msg = '<div class="alert alert-danger" role="alert">Selected teacher does not exist</div>';
        } elseif (!$courseExists) {
            $msg = '<div class="alert alert-danger" role="alert">Selected course does not exist</div>';
        } else {
            /*--------------------------------------------------------------------------
            | INSERT with prepared statement
            |--------------------------------------------------------------------------*/
            $stmt = $connection->prepare(
                "INSERT INTO certificates 
                    (student_id, certificate_name, issue_date, teacher_id, course_id, certificate_file, created_at, updated_at)
                 VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())"
            );

            if ($stmt === false) {
                $msg = '<div class="alert alert-danger" role="alert">Database error: ' . htmlspecialchars($connection->error) . '</div>';
            } else {
                $stmt->bind_param(
                    "issiis",
                    $student_id,
                    $certificate_name,
                    $issue_date,
                    $teacher_id,
                    $course_id,
                    $certificate_file
                );

                if ($stmt->execute()) {
                    if (move_uploaded_file($_FILES['certificate_file']['tmp_name'], $target)) {
                        $msg = '<div class="alert alert-success" role="alert">Certificate created successfully</div>'
                             . '<meta http-equiv="refresh" content="2; url=show-certificates.php" />';
                    } else {
                        $msg = '<div class="alert alert-danger" role="alert">Certificate saved, but file upload failed</div>';
                    }
                } else {
                    $msg = '<div class="alert alert-danger" role="alert">Database error: ' . htmlspecialchars($stmt->error) . '</div>';
                }
                $stmt->close();
            }
        }
    }
}
?>
