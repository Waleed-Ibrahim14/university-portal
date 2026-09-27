<?php 	
/*--------------------------------------------------------------------------
| Create Student Debt Process ::
| (Note: "depts" here means "debts" — student financial debt)
|--------------------------------------------------------------------------*/

/*--------------------------------------------------------------------------
| Use __DIR__ for reliable path resolution.
|--------------------------------------------------------------------------*/
include_once(__DIR__ . "/../Models/DataBaseConnection.php");

$msg = '';

/*--------------------------------------------------------------------------
| FIXED: match the submit button name in the view (name="create_debt").
|--------------------------------------------------------------------------*/
if (isset($_POST['create_debt'])) {

    /*--------------------------------------------------------------------------
    | Read and sanitize inputs
    |--------------------------------------------------------------------------*/
    $student_id = isset($_POST['student_id']) && $_POST['student_id'] !== ''
                  ? (int) $_POST['student_id']
                  : null;
    $amount     = trim($_POST['amount'] ?? '');
    $date       = trim($_POST['date'] ?? '');
    $reason     = trim($_POST['reason'] ?? '');

    /*--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------*/
    if (empty($student_id)) {
        $msg = '<div class="alert alert-danger" role="alert">Select student</div>';
    } elseif (empty($amount)) {
        $msg = '<div class="alert alert-danger" role="alert">Enter amount</div>';
    } elseif (!is_numeric($amount) || $amount < 0) {
        $msg = '<div class="alert alert-danger" role="alert">Amount must be a positive number</div>';
    } elseif (empty($date)) {
        $msg = '<div class="alert alert-danger" role="alert">Enter the date</div>';
    } elseif (empty($reason)) {
        $msg = '<div class="alert alert-danger" role="alert">Enter the reason for the debt</div>';
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
                "INSERT INTO debts (student_id, amount, date, reason, created_at, updated_at)
                 VALUES (?, ?, ?, ?, NOW(), NOW())"
            );

            if ($stmt === false) {
                $msg = '<div class="alert alert-danger" role="alert">Database error: '
                     . htmlspecialchars($connection->error) . '</div>';
            } else {
                $stmt->bind_param("idss", $student_id, $amount, $date, $reason);

                /*--------------------------------------------------------------------------
                | FIXED: check the RESULT of execute(), not the query handle.
                | The original `if (isset($insert))` was ALWAYS true — even on failure.
                |--------------------------------------------------------------------------*/
                if ($stmt->execute()) {
                    $msg = '<div class="alert alert-success" role="alert">Debt created successfully</div>'
                         . '<meta http-equiv="refresh" content="2; url=show-depts.php" />';
                } else {
                    $msg = '<div class="alert alert-danger" role="alert">Error saving debt</div>';
                }
                $stmt->close();
            }
        }
    }
}
?>
