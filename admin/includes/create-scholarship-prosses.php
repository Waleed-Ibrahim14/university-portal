<?php   
/*--------------------------------------------------------------------------
| Add Scholarship Process ::
|--------------------------------------------------------------------------*/

/*--------------------------------------------------------------------------
| Use __DIR__ for reliable path resolution.
|--------------------------------------------------------------------------*/
include_once(__DIR__ . "/../Models/DataBaseConnection.php");

$msg = '';

if (isset($_POST['add_scholarship'])) {

    /*--------------------------------------------------------------------------
    | Read and sanitize inputs
    |--------------------------------------------------------------------------*/
    $scholarship_name        = trim(strip_tags($_POST['scholarship_name'] ?? ''));
    $scholarship_description = trim($_POST['scholarship_description'] ?? '');
    $amount                  = trim($_POST['amount'] ?? '');
    $date                    = trim($_POST['date'] ?? '');
    $scholarship_status      = trim($_POST['scholarship_status'] ?? '');
    $added_by                = trim($_POST['added_by'] ?? '');

    // File info
    $image         = $_FILES['image']['name'] ?? '';
    $target        = "../../assets/images/scholarships/" . basename($image);
    $imageFileType = strtolower(pathinfo($target, PATHINFO_EXTENSION));

    // Max file size: 5 MB (was 50 ZB — absurd)
    $maxFileSize = 5 * 1024 * 1024;

    /*--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------*/
    if (empty($scholarship_name)) {
        $msg = '<div class="alert alert-danger" role="alert">Please enter scholarship name</div>';
    } elseif (empty($image)) {
        $msg = '<div class="alert alert-danger" role="alert">Please select scholarship image</div>';
    } elseif (empty($amount)) {
        $msg = '<div class="alert alert-danger" role="alert">Please enter scholarship amount</div>';
    } elseif (!is_numeric($amount) || $amount < 0) {
        $msg = '<div class="alert alert-danger" role="alert">Amount must be a positive number</div>';
    } elseif (empty($date)) {
        $msg = '<div class="alert alert-danger" role="alert">Please enter scholarship date</div>';
    } elseif (empty($scholarship_status)) {
        $msg = '<div class="alert alert-danger" role="alert">Please select scholarship status</div>';
    } elseif (!in_array($scholarship_status, ['published', 'draft'], true)) {
        $msg = '<div class="alert alert-danger" role="alert">Invalid scholarship status</div>';
    } elseif (empty($added_by)) {
        $msg = '<div class="alert alert-danger" role="alert">Please select admin name</div>';
    } elseif (empty($scholarship_description)) {
        $msg = '<div class="alert alert-danger" role="alert">Please enter scholarship description</div>';
    } elseif (!in_array($imageFileType, ['pdf', 'jpg', 'jpeg', 'png', 'gif'], true)) {
        $msg = '<div class="alert alert-danger" role="alert">Choose a correct file (PDF/JPG/PNG/GIF)</div>';
    } elseif (($_FILES["image"]["size"] ?? 0) > $maxFileSize) {
        $msg = '<div class="alert alert-danger" role="alert">The image size is too large (max 5 MB)</div>';
    } elseif (file_exists($target)) {
        $msg = '<div class="alert alert-danger" role="alert">Sorry, this file has been uploaded before</div>';

    } else {

        /*--------------------------------------------------------------------------
        | Verify the admin exists (FK integrity — even though added_by is VARCHAR)
        |--------------------------------------------------------------------------*/
        $check = $connection->prepare(
            "SELECT u.username
             FROM users u
             LEFT JOIN roles r ON u.role_id = r.id
             WHERE u.username = ? AND r.role_name = 'admin'
             LIMIT 1"
        );
        $check->bind_param("s", $added_by);
        $check->execute();
        $adminExists = $check->get_result()->num_rows > 0;
        $check->close();

        if (!$adminExists) {
            $msg = '<div class="alert alert-danger" role="alert">Selected admin does not exist</div>';
        } else {

            /*--------------------------------------------------------------------------
            | INSERT with prepared statement
            |--------------------------------------------------------------------------*/
            $stmt = $connection->prepare(
                "INSERT INTO scholarships 
                    (scholarship_name, image, scholarship_description, amount, date, scholarship_status, added_by, created_at, updated_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())"
            );

            if ($stmt === false) {
                $msg = '<div class="alert alert-danger" role="alert">Database error: '
                     . htmlspecialchars($connection->error) . '</div>';
            } else {
                $stmt->bind_param(
                    "sssssss",
                    $scholarship_name,
                    $image,
                    $scholarship_description,
                    $amount,
                    $date,
                    $scholarship_status,
                    $added_by
                );

                if ($stmt->execute()) {
                    /*--------------------------------------------------------------------------
                    | Move uploaded file
                    |--------------------------------------------------------------------------*/
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                        // FIXED meta refresh syntax
                        $msg = '<div class="alert alert-success" role="alert">The scholarship has been added successfully</div>'
                             . '<meta http-equiv="refresh" content="2; url=show-scholarships.php" />';
                    } else {
                        $msg = '<div class="alert alert-danger" role="alert">An error occurred while uploading the file</div>';
                    }
                } else {
                    $msg = '<div class="alert alert-danger" role="alert">Database error: '
                         . htmlspecialchars($stmt->error) . '</div>';
                }
                $stmt->close();
            }
        }
    }
}
?>
