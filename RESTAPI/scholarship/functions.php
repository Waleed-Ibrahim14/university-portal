<?php
/*--------------------------------------------------------------------------
| DataBase Config File
|--------------------------------------------------------------------------*/
require_once("../../admin/Models/DataBaseConnection.php");

/*--------------------------------------------------------------------------
| Error Function To Show Error Message
|
| FIXED: HTTP status code was 200 (wrong) — now 422 (Unprocessable Entity).
|--------------------------------------------------------------------------*/
function error422($message) {
    $data = ['status' => 422, 'message' => $message];
    header("HTTP/1.0 422 Unprocessable Entity");
    echo json_encode($data);
    exit();
}

/*--------------------------------------------------------------------------
| Create New Scholarship Function ::
|
| CHANGES:
|   - Prepared statements (SQL Injection protection).
|   - Includes the `image` column (required by the schema, NOT NULL).
|   - Trims inputs before validation.
|--------------------------------------------------------------------------*/
function InserScholarship($scholarshipInput) {
    global $connection;

    /*--------------------------------------------------------------------------
    | Read and trim inputs
    |--------------------------------------------------------------------------*/
    $scholarship_name        = trim($scholarshipInput['scholarship_name']        ?? '');
    $scholarship_description = trim($scholarshipInput['scholarship_description'] ?? '');
    $image                   = trim($scholarshipInput['image']                   ?? '');
    $amount                  = trim($scholarshipInput['amount']                  ?? '');
    $date                    = trim($scholarshipInput['date']                    ?? '');
    $scholarship_status      = trim($scholarshipInput['scholarship_status']      ?? '');
    $added_by                = trim($scholarshipInput['added_by']                ?? '');

    /*--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------*/
    if (empty($scholarship_name))        return error422('enter scholarship name');
    if (empty($scholarship_description)) return error422('enter scholarship description');
    if (empty($image))                   return error422('enter image');
    if (empty($amount))                  return error422('enter amount');
    if (empty($date))                    return error422('enter date');
    if (empty($scholarship_status))      return error422('enter scholarship status');
    if (empty($added_by))                return error422('enter added_by');

    /*--------------------------------------------------------------------------
    | Validate amount is numeric (column is DECIMAL(10,2))
    |--------------------------------------------------------------------------*/
    if (!is_numeric($amount)) {
        return error422('amount must be a number');
    }

    /*--------------------------------------------------------------------------
    | Insert (prepared statement)
    |--------------------------------------------------------------------------*/
    $sql = "INSERT INTO scholarships
                (scholarship_name, scholarship_description, image,
                 amount, date, scholarship_status, added_by)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $connection->prepare($sql);
    if ($stmt === false) {
        $data = ['status' => 500, 'message' => 'Internal Server Error: ' . $connection->error];
        header("HTTP/1.0 500 Internal Server Error");
        echo json_encode($data);
        return;
    }

    $stmt->bind_param(
        "sssssss",
        $scholarship_name, $scholarship_description, $image,
        $amount, $date, $scholarship_status, $added_by
    );

    if ($stmt->execute()) {
        $data = ['status' => 201, 'message' => 'Scholarship Created Successfully'];
        header("HTTP/1.0 201 Created");
        echo json_encode($data);
    } else {
        $data = ['status' => 500, 'message' => 'Internal Server Error'];
        header("HTTP/1.0 500 Internal Server Error");
        echo json_encode($data);
    }
    $stmt->close();
}

/*--------------------------------------------------------------------------
| Get All Scholarships Function ::
|
| CHANGES:
|   - Corrected error messages ("Scholarship" instead of "User").
|   - Added ORDER BY id DESC for consistent ordering.
|--------------------------------------------------------------------------*/
function getScholarshipList() {
    global $connection;

    $sql_select = mysqli_query($connection, "SELECT * FROM `scholarships` ORDER BY `id` DESC");

    if ($sql_select) {
        if (mysqli_num_rows($sql_select) > 0) {
            $response = mysqli_fetch_all($sql_select, MYSQLI_ASSOC);
            $data = [
                'status'  => 200,
                'message' => 'Scholarships Fetched Successfully',
                'data'    => $response
            ];
            header("HTTP/1.0 200 Success");
            echo json_encode($data);
        } else {
            $data = ['status' => 404, 'message' => 'No Scholarship Found'];
            header("HTTP/1.0 404 Not Found");
            echo json_encode($data);
        }
    } else {
        $data = ['status' => 500, 'message' => 'Internal Server Error'];
        header("HTTP/1.0 500 Internal Server Error");
        echo json_encode($data);
    }
}

/*--------------------------------------------------------------------------
| Get Single Scholarship Function ::
|
| CHANGES:
|   - Prepared statement + (int) cast.
|   - Corrected error messages.
|--------------------------------------------------------------------------*/
function getSingleScholarship($userParams) {
    global $connection;

    if (!isset($userParams['id']) || $userParams['id'] === null || $userParams['id'] === '') {
        return error422('Enter scholarship id');
    }

    $scholarshipId = (int) $userParams['id'];

    $stmt = $connection->prepare("SELECT * FROM scholarships WHERE id = ? LIMIT 1");
    if ($stmt === false) {
        $data = ['status' => 500, 'message' => 'Internal Server Error'];
        header("HTTP/1.0 500 Internal Server Error");
        echo json_encode($data);
        return;
    }

    $stmt->bind_param("i", $scholarshipId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $response = $result->fetch_assoc();
        $data = [
            'status'  => 200,
            'message' => 'Scholarship Fetched Successfully',
            'data'    => $response
        ];
        header("HTTP/1.0 200 Success");
        echo json_encode($data);
    } else {
        $data = ['status' => 404, 'message' => 'No Scholarship Found'];
        header("HTTP/1.0 404 Not Found");
        echo json_encode($data);
    }
    $stmt->close();
}

/*--------------------------------------------------------------------------
| Update Scholarship Function ::
|
| CHANGES:
|   - Renamed from `UpdateUser` (copy-paste bug) to `UpdateScholarship`.
|   - Prepared statement.
|   - Verifies existence before update.
|   - Includes `image` in the UPDATE.
|--------------------------------------------------------------------------*/
function UpdateScholarship($scholarshipInput, $userParams) {
    global $connection;

    if (!isset($userParams['id']) || $userParams['id'] === null || $userParams['id'] === '') {
        return error422('Scholarship Id Not Found In URL');
    }

    $scholarshipId = (int) $userParams['id'];

    /*--------------------------------------------------------------------------
    | Verify existence first
    |--------------------------------------------------------------------------*/
    $chk = $connection->prepare("SELECT id FROM scholarships WHERE id = ? LIMIT 1");
    $chk->bind_param("i", $scholarshipId);
    $chk->execute();
    if ($chk->get_result()->num_rows === 0) {
        $chk->close();
        $data = ['status' => 404, 'message' => 'Scholarship Not Found'];
        header("HTTP/1.0 404 Not Found");
        echo json_encode($data);
        return;
    }
    $chk->close();

    /*--------------------------------------------------------------------------
    | Read inputs
    |--------------------------------------------------------------------------*/
    $scholarship_name        = trim($scholarshipInput['scholarship_name']        ?? '');
    $scholarship_description = trim($scholarshipInput['scholarship_description'] ?? '');
    $image                   = trim($scholarshipInput['image']                   ?? '');
    $amount                  = trim($scholarshipInput['amount']                  ?? '');
    $date                    = trim($scholarshipInput['date']                    ?? '');
    $scholarship_status      = trim($scholarshipInput['scholarship_status']      ?? '');
    $added_by                = trim($scholarshipInput['added_by']                ?? '');

    /*--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------*/
    if (empty($scholarship_name))        return error422('enter scholarship name');
    if (empty($scholarship_description)) return error422('enter scholarship description');
    if (empty($image))                   return error422('enter image');
    if (empty($amount))                  return error422('enter amount');
    if (!is_numeric($amount))            return error422('amount must be a number');
    if (empty($date))                    return error422('enter date');
    if (empty($scholarship_status))      return error422('enter scholarship status');
    if (empty($added_by))                return error422('enter added_by');

    /*--------------------------------------------------------------------------
    | Update (prepared statement)
    |--------------------------------------------------------------------------*/
    $sql = "UPDATE scholarships SET
                scholarship_name        = ?,
                scholarship_description = ?,
                image                   = ?,
                amount                  = ?,
                date                    = ?,
                scholarship_status      = ?,
                added_by                = ?
            WHERE id = ? LIMIT 1";

    $stmt = $connection->prepare($sql);
    if ($stmt === false) {
        $data = ['status' => 500, 'message' => 'Internal Server Error'];
        header("HTTP/1.0 500 Internal Server Error");
        echo json_encode($data);
        return;
    }

    $stmt->bind_param(
        "sssssssi",
        $scholarship_name, $scholarship_description, $image,
        $amount, $date, $scholarship_status, $added_by, $scholarshipId
    );

    if ($stmt->execute()) {
        $data = ['status' => 200, 'message' => 'Scholarship Updated Successfully'];
        header("HTTP/1.0 200 OK");
        echo json_encode($data);
    } else {
        $data = ['status' => 500, 'message' => 'Internal Server Error'];
        header("HTTP/1.0 500 Internal Server Error");
        echo json_encode($data);
    }
    $stmt->close();
}

/*--------------------------------------------------------------------------
| Delete Scholarship Function ::
|
| CHANGES:
|   - Renamed from `deleteleUser` (copy-paste bug) to `deleteScholarship`.
|   - Prepared statement.
|   - Fixed column case (`ID` → `id`).
|   - Checks affected_rows.
|--------------------------------------------------------------------------*/
function deleteScholarship($userParams) {
    global $connection;

    if (!isset($userParams['id']) || $userParams['id'] === null || $userParams['id'] === '') {
        return error422('Scholarship Id Not Found In URL');
    }

    $scholarshipId = (int) $userParams['id'];

    $stmt = $connection->prepare("DELETE FROM scholarships WHERE id = ? LIMIT 1");
    if ($stmt === false) {
        $data = ['status' => 500, 'message' => 'Internal Server Error'];
        header("HTTP/1.0 500 Internal Server Error");
        echo json_encode($data);
        return;
    }

    $stmt->bind_param("i", $scholarshipId);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $data = ['status' => 200, 'message' => 'Scholarship Deleted Successfully'];
            header("HTTP/1.0 200 OK");
            echo json_encode($data);
        } else {
            $data = ['status' => 404, 'message' => 'Scholarship Not Found'];
            header("HTTP/1.0 404 Not Found");
            echo json_encode($data);
        }
    } else {
        $data = ['status' => 500, 'message' => 'Internal Server Error'];
        header("HTTP/1.0 500 Internal Server Error");
        echo json_encode($data);
    }
    $stmt->close();
}
?>
