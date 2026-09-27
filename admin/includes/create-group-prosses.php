<?php 	
/*--------------------------------------------------------------------------
| Create Group Process ::
|--------------------------------------------------------------------------*/

/*--------------------------------------------------------------------------
| Use __DIR__ for reliable path resolution.
|--------------------------------------------------------------------------*/
include_once(__DIR__ . "/../Models/DataBaseConnection.php");

$message = '';

/*--------------------------------------------------------------------------
| FIXED: match the submit button name in the view (name="create_group").
|--------------------------------------------------------------------------*/
if (isset($_POST['create_group'])) {

    /*--------------------------------------------------------------------------
    | Read and sanitize input
    |--------------------------------------------------------------------------*/
    $group_name = trim($_POST['group_name'] ?? '');

    /*--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------*/
    if (empty($group_name)) {
        $message = '<div class="alert alert-danger" role="alert">Enter the group name</div>';
    } else {

        /*--------------------------------------------------------------------------
        | Check for duplicate group name (prepared statement)
        |--------------------------------------------------------------------------*/
        $check = $connection->prepare("SELECT id FROM groups WHERE group_name = ? LIMIT 1");
        $check->bind_param("s", $group_name);
        $check->execute();
        $exists = $check->get_result()->num_rows > 0;
        $check->close();

        if ($exists) {
            $message = '<div class="alert alert-danger" role="alert">This group name already exists</div>';
        } else {

            /*--------------------------------------------------------------------------
            | INSERT with prepared statement
            |--------------------------------------------------------------------------*/
            $stmt = $connection->prepare(
                "INSERT INTO groups (group_name, created_at, updated_at)
                 VALUES (?, NOW(), NOW())"
            );

            if ($stmt === false) {
                $message = '<div class="alert alert-danger" role="alert">Database error: '
                         . htmlspecialchars($connection->error) . '</div>';
            } else {
                $stmt->bind_param("s", $group_name);

                /*--------------------------------------------------------------------------
                | FIXED: check the result of execute(), not the string $stmt
                |--------------------------------------------------------------------------*/
                if ($stmt->execute()) {
                    $message = '<div class="alert alert-success" role="alert">Group saved successfully</div>'
                             . '<meta http-equiv="refresh" content="2; url=show-groups.php" />';
                } else {
                    $message = '<div class="alert alert-danger" role="alert">Error saving data</div>';
                }
                $stmt->close();
            }
        }
    }
}
?>
