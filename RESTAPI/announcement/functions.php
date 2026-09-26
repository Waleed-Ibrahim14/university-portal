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
| Create New Announcement Function ::
|
| CHANGES:
|   - Uses prepared statements to prevent SQL Injection.
|   - Trims inputs before validation.
|   - created_at/updated_at default to CURRENT_TIMESTAMP in the DB.
|--------------------------------------------------------------------------*/
function Insertnotifi($userInput) {
    global $connection;

    /*--------------------------------------------------------------------------
    | Read and trim inputs
    |--------------------------------------------------------------------------*/
    $title         = trim($userInput['title']          ?? '');
    $notifi_msg    = trim($userInput['notifi_msg']     ?? '');
    $notifi_time   = trim($userInput['notifi_time']    ?? '');
    $notifi_repeat = trim($userInput['notifi_repeat']  ?? '');
    $notifi_loop   = trim($userInput['notifi_loop']    ?? '');
    $publish_date  = trim($userInput['publish_date']   ?? '');
    $username      = trim($userInput['username']       ?? '');

    /*--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------*/
    if (empty($title))         return error422('enter your title');
    if (empty($notifi_msg))    return error422('enter your notifi msg');
    if (empty($notifi_time))   return error422('enter your notifi time');
    if (empty($notifi_repeat)) return error422('enter your notifi repeat');
    if (empty($notifi_loop))   return error422('enter your notifi loop');
    if (empty($publish_date))  return error422('enter your publish date');
    if (empty($username))      return error422('enter your username');

    /*--------------------------------------------------------------------------
    | Insert (prepared statement)
    |--------------------------------------------------------------------------*/
    $sql = "INSERT INTO announcements
                (title, notifi_msg, notifi_time, notifi_repeat, notifi_loop,
                 publish_date, username)
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
        $title, $notifi_msg, $notifi_time, $notifi_repeat,
        $notifi_loop, $publish_date, $username
    );

    if ($stmt->execute()) {
        $data = ['status' => 201, 'message' => 'Announcement Created Successfully'];
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
| Get all announcements Function ::
|
| CHANGES:
|   - Uses COUNT(*) implicitly via fetch_all (unchanged logic).
|   - Corrected error messages ("announcements" instead of "user").
|--------------------------------------------------------------------------*/
function getNotifiList() {
    global $connection;

    $sql_select = mysqli_query($connection, "SELECT * FROM `announcements` ORDER BY `id` DESC");

    if ($sql_select) {
        if (mysqli_num_rows($sql_select) > 0) {
            $response = mysqli_fetch_all($sql_select, MYSQLI_ASSOC);
            $data = [
                'status'  => 200,
                'message' => 'Announcements Fetched Successfully',
                'data'    => $response
            ];
            header("HTTP/1.0 200 Success");
            echo json_encode($data);
        } else {
            $data = ['status' => 404, 'message' => 'No Announcements Found'];
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
| Get Single Announcement Function ::
|
| CHANGES:
|   - Prepared statement + (int) cast.
|   - Corrected error messages.
|--------------------------------------------------------------------------*/
function getSingleNotifi($userParams) {
    global $connection;

    if (!isset($userParams['id']) || $userParams['id'] === null || $userParams['id'] === '') {
        return error422('Enter the announcement id');
    }

    $announcementId = (int) $userParams['id'];

    $stmt = $connection->prepare("SELECT * FROM announcements WHERE id = ? LIMIT 1");
    if ($stmt === false) {
        $data = ['status' => 500, 'message' => 'Internal Server Error'];
        header("HTTP/1.0 500 Internal Server Error");
        echo json_encode($data);
        return;
    }

    $stmt->bind_param("i", $announcementId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $response = $result->fetch_assoc();
        $data = [
            'status'  => 200,
            'message' => 'Announcement Fetched Successfully',
            'data'    => $response
        ];
        header("HTTP/1.0 200 Success");
        echo json_encode($data);
    } else {
        $data = ['status' => 404, 'message' => 'No Announcement Found'];
        header("HTTP/1.0 404 Not Found");
        echo json_encode($data);
    }
    $stmt->close();
}

/*--------------------------------------------------------------------------
| Update Announcement Function ::
|
| CHANGES:
|   - Prepared statement.
|   - FIXED SYNTAX BUG: original query had `username = '$username'
|     updated_at = date()` with no comma between the two assignments.
|     Now uses `updated_at = NOW()` correctly.
|   - Verifies the announcement exists before updating.
|--------------------------------------------------------------------------*/
function updateNotifi($userInput, $userParams) {
    global $connection;

    if (!isset($userParams['id']) || $userParams['id'] === null || $userParams['id'] === '') {
        return error422('Announcement Id Not Found In URL');
    }

    $announcementId = (int) $userParams['id'];

    /*--------------------------------------------------------------------------
    | Verify existence first
    |--------------------------------------------------------------------------*/
    $chk = $connection->prepare("SELECT id FROM announcements WHERE id = ? LIMIT 1");
    $chk->bind_param("i", $announcementId);
    $chk->execute();
    if ($chk->get_result()->num_rows === 0) {
        $chk->close();
        $data = ['status' => 404, 'message' => 'Announcement Not Found'];
        header("HTTP/1.0 404 Not Found");
        echo json_encode($data);
        return;
    }
    $chk->close();

    /*--------------------------------------------------------------------------
    | Read inputs
    |--------------------------------------------------------------------------*/
    $title         = trim($userInput['title']          ?? '');
    $notifi_msg    = trim($userInput['notifi_msg']     ?? '');
    $notifi_time   = trim($userInput['notifi_time']    ?? '');
    $notifi_repeat = trim($userInput['notifi_repeat']  ?? '');
    $notifi_loop   = trim($userInput['notifi_loop']    ?? '');
    $publish_date  = trim($userInput['publish_date']   ?? '');
    $username      = trim($userInput['username']       ?? '');

    /*--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------*/
    if (empty($title))         return error422('enter your title');
    if (empty($notifi_msg))    return error422('enter your notifi msg');
    if (empty($notifi_time))   return error422('enter your notifi time');
    if (empty($notifi_repeat)) return error422('enter your notifi repeat');
    if (empty($notifi_loop))   return error422('enter your notifi loop');
    if (empty($publish_date))  return error422('enter your publish date');
    if (empty($username))      return error422('enter your username');

    /*--------------------------------------------------------------------------
    | Update (prepared statement) — updated_at set explicitly to NOW()
    |--------------------------------------------------------------------------*/
    $sql = "UPDATE announcements SET
                title         = ?,
                notifi_msg    = ?,
                notifi_time   = ?,
                notifi_repeat = ?,
                notifi_loop   = ?,
                publish_date  = ?,
                username      = ?,
                updated_at    = NOW()
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
        $title, $notifi_msg, $notifi_time, $notifi_repeat,
        $notifi_loop, $publish_date, $username, $announcementId
    );

    if ($stmt->execute()) {
        $data = ['status' => 200, 'message' => 'Announcement Updated Successfully'];
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
| Delete Announcement Function ::
|
| CHANGES:
|   - Prepared statement.
|   - Fixed column case (`ID` → `id`).
|   - Corrected messages ("Announcement" instead of "User").
|   - Checks affected_rows to detect non-existent IDs.
|--------------------------------------------------------------------------*/
function deleteleNotifi($userParams) {
    global $connection;

    if (!isset($userParams['id']) || $userParams['id'] === null || $userParams['id'] === '') {
        return error422('Announcement Id Not Found In URL');
    }

    $announcementId = (int) $userParams['id'];

    $stmt = $connection->prepare("DELETE FROM announcements WHERE id = ? LIMIT 1");
    if ($stmt === false) {
        $data = ['status' => 500, 'message' => 'Internal Server Error'];
        header("HTTP/1.0 500 Internal Server Error");
        echo json_encode($data);
        return;
    }

    $stmt->bind_param("i", $announcementId);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $data = ['status' => 200, 'message' => 'Announcement Deleted Successfully'];
            header("HTTP/1.0 200 OK");
            echo json_encode($data);
        } else {
            $data = ['status' => 404, 'message' => 'Announcement Not Found'];
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
