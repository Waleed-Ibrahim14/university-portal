<?php 	
/*--------------------------------------------------------------------------
| Create Course Process ::
|--------------------------------------------------------------------------*/

/*--------------------------------------------------------------------------
| Use __DIR__ for reliable path resolution.
|--------------------------------------------------------------------------*/
include_once(__DIR__ . "/../Models/DataBaseConnection.php");

$message = '';

/*--------------------------------------------------------------------------
| FIXED: this must match the submit button's `name` attribute in the view.
| The view uses name="create_course" (not "submit").
|--------------------------------------------------------------------------*/
if (isset($_POST['create_course'])) {

    /*--------------------------------------------------------------------------
    | Read and sanitize inputs
    |--------------------------------------------------------------------------*/
    $course_name = trim($_POST['course_name'] ?? '');
    $teacher_id  = isset($_POST['teacher_id']) && $_POST['teacher_id'] !== ''
                   ? (int) $_POST['teacher_id']
                   : null;

    /*--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------*/
    if (empty($course_name)) {
        $message = '<div class="alert alert-danger" role="alert">Enter the course name</div>';
    } elseif (empty($teacher_id)) {
        $message = '<div class="alert alert-danger" role="alert">Select a teacher</div>';
    } else {

        /*--------------------------------------------------------------------------
        | Verify the teacher exists (FK integrity)
        |--------------------------------------------------------------------------*/
        $check = $connection->prepare("SELECT id FROM users WHERE id = ? LIMIT 1");
        $check->bind_param("i", $teacher_id);
        $check->execute();
        $teacherExists = $check->get_result()->num_rows > 0;
        $check->close();

        if (!$teacherExists) {
            $message = '<div class="alert alert-danger" role="alert">Selected teacher does not exist</div>';
        } else {

            /*--------------------------------------------------------------------------
            | INSERT with prepared statement
            |--------------------------------------------------------------------------*/
            $stmt = $connection->prepare(
                "INSERT INTO courses (course_name, teacher_id, created_at, updated_at)
                 VALUES (?, ?, NOW(), NOW())"
            );

            if ($stmt === false) {
                $message = '<div class="alert alert-danger" role="alert">Database error: '
                         . htmlspecialchars($connection->error) . '</div>';
            } else {
                $stmt->bind_param("si", $course_name, $teacher_id);

                /*--------------------------------------------------------------------------
                | FIXED: check the RESULT of execute(), not the string $stmt
                |--------------------------------------------------------------------------*/
                if ($stmt->execute()) {
                    $message = '<div class="alert alert-success" role="alert">Course saved successfully</div>'
                             . '<meta http-equiv="refresh" content="2; url=show-courses.php" />';
                } else {
                    $message = '<div class="alert alert-danger" role="alert">Error saving data</div>';
                }
                $stmt->close();
            }
        }
    }
}
?>
