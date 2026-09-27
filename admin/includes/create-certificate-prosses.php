<?php
/*--------------------------------------------------------------------------
| Create Certificate Process ::
|--------------------------------------------------------------------------*/

/*--------------------------------------------------------------------------
| Use __DIR__ to avoid path resolution issues
| (this file lives in admin/includes/, so ../../Models would be wrong,
|  and ../Models works only if PHP resolves relative to this file's dir).
| __DIR__ guarantees the correct absolute path.
|--------------------------------------------------------------------------*/
include_once(__DIR__ . "/../Models/DataBaseConnection.php");

$msg = ''; // Initialize the message variable

if (isset($_POST['create_certificate'])) {

    /*--------------------------------------------------------------------------
    | Read and sanitize inputs
    |--------------------------------------------------------------------------*/
    $student_id       = isset($_POST['student_id']) && $_POST['student_id'] !== ''
                        ? (int) $_POST['student_id']
                        : null;
    $certificate_name = trim($_POST['certificate_name'] ?? '');
    $issue_date       = trim($_POST['issue_date'] ?? '');
    $teacher_name     = trim($_POST['teacher_name'] ?? '');

    // File info
    $certificate_file = $_FILES['certificate_file']['name'] ?? '';
    $target           = "../../assets/images/certificates/" . basename($certificate_file);
    $imageFileType    = strtolower(pathinfo($target, PATHINFO_EXTENSION));

    // Max file size: 5 MB (was 50 TB — clearly a typo)
    $maxFileSize = 5 * 1024 * 1024;

    /*--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------*/
    if (empty($student_id)) {
        $msg = '<div class="alert alert-danger" role="alert">Please select student</div>';
    } elseif (empty($certificate_name)) {
        $msg = '<div class="alert alert-danger" role="alert">Please enter certificate name</div>';
    } elseif (empty($issue_date)) {
        $msg = '<div class="alert alert-danger" role="alert">Please select issue date</div>';
    } elseif (empty($teacher_name)) {
        $msg = '<div class="alert alert-danger" role="alert">Please select teacher</div>';
    } elseif ($imageFileType !== "pdf") {
        $msg = '<div class="alert alert-danger" role="alert">Choose a correct file (*.pdf)</div>';
    } elseif (($_FILES["certificate_file"]["size"] ?? 0) > $maxFileSize) {
        $msg = '<div class="alert alert-danger" role="alert">File size too big (max 5 MB)</div>';
    } elseif (file_exists($target)) {
        $msg = '<div class="alert alert-danger" role="alert">Sorry, this certificate file was uploaded before</div>';

    } else {
        /*--------------------------------------------------------------------------
        | Verify the student exists (FK integrity)
        |--------------------------------------------------------------------------*/
        $check = $connection->prepare("SELECT id FROM users WHERE id = ? LIMIT 1");
        $check->bind_param("i", $student_id);
        $check->execute();
        $studentExists = $check->get_result()->num_rows > 0;
        $check->close();

        if (!$studentExists) {
            $msg = '<div class="alert alert-danger" role="alert">Selected student does not exist</div>';
        } else {
            /*--------------------------------------------------------------------------
            | INSERT with prepared statement
            |--------------------------------------------------------------------------*/
            $stmt = $connection->prepare(
                "INSERT INTO certificates 
                    (student_id, certificate_name, issue_date, teacher_name, certificate_file, created_at, updated_at)
                 VALUES (?, ?, ?, ?, ?, NOW(), NOW())"
            );

            if ($stmt === false) {
                $msg = '<div class="alert alert-danger" role="alert">Database error: ' . htmlspecialchars($connection->error) . '</div>';
            } else {
                $stmt->bind_param(
                    "issss",
                    $student_id,
                    $certificate_name,
                    $issue_date,
                    $teacher_name,
                    $certificate_file
                );

                if ($stmt->execute()) {
                    /*--------------------------------------------------------------------------
                    | Move uploaded file
                    |--------------------------------------------------------------------------*/
                    if (move_uploaded_file($_FILES['certificate_file']['tmp_name'], $target)) {
                        // FIXED meta refresh syntax: url= instead of quotes
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
