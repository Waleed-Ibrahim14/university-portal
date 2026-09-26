<?php
/*--------------------------------------------------------------------------
| DataBase Config File
|--------------------------------------------------------------------------*/
require_once("../../admin/Models/DataBaseConnection.php");

/*--------------------------------------------------------------------------
| Error Function To Show Error Message
|--------------------------------------------------------------------------*/
function error422($message) {
    $data = ['status' => 422, 'message' => $message];
    header("HTTP/1.0 422 Unprocessable Entity");
    echo json_encode($data);
    exit();
}

/*--------------------------------------------------------------------------
| Create New User Function ::
|
| CHANGES:
|   - Accepts role_id / scholarship_id / group_id (integers) instead of
|     the removed text columns (role, scholarship_name, group_name).
|   - Uses prepared statements to prevent SQL Injection.
|   - Hashes the password with password_hash() before inserting.
|--------------------------------------------------------------------------*/
function InserUser($userInput) {
    global $connection;

    /*--------------------------------------------------------------------------
    | Read and sanitize inputs.
    | Integer fields use (int) cast; text fields are trimmed.
    |--------------------------------------------------------------------------*/
    $fullname       = trim($userInput['fullname']       ?? '');
    $country        = trim($userInput['country']        ?? '');
    $gender         = trim($userInput['gender']         ?? '');
    $username       = trim($userInput['username']       ?? '');
    $email          = trim($userInput['email']          ?? '');
    $password_raw   = $userInput['password'] ?? '';
    $profile        = trim($userInput['profile']        ?? '');
    $user_status    = trim($userInput['user_status']    ?? '');

    // Optional FK fields (nullable)
    $role_id        = isset($userInput['role_id'])        && $userInput['role_id'] !== ''        ? (int)$userInput['role_id']        : null;
    $scholarship_id = isset($userInput['scholarship_id']) && $userInput['scholarship_id'] !== '' ? (int)$userInput['scholarship_id'] : null;
    $group_id       = isset($userInput['group_id'])       && $userInput['group_id'] !== ''       ? (int)$userInput['group_id']       : null;

    /*--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------*/
    if (empty($fullname))                       return error422('enter your fullname');
    if (empty($country))                        return error422('enter your country');
    if (empty($gender))                         return error422('enter your gender');
    if (empty($username))                       return error422('enter your username');
    if (empty($email))                          return error422('enter your email');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return error422('enter a valid email');
    if (empty($password_raw))                   return error422('enter your password');
    if (strlen($password_raw) < 8)              return error422('password must be at least 8 characters');
    if (empty($profile))                        return error422('enter your profile');
    if (empty($user_status))                    return error422('enter your user status');

    // Check role_id exists if provided
    if ($role_id !== null) {
        $chk = $connection->prepare("SELECT id FROM roles WHERE id = ? LIMIT 1");
        $chk->bind_param("i", $role_id);
        $chk->execute();
        if ($chk->get_result()->num_rows === 0) {
            $chk->close();
            return error422('invalid role_id');
        }
        $chk->close();
    }

    /*--------------------------------------------------------------------------
    | Check for duplicate username/email (prepared statement).
    |--------------------------------------------------------------------------*/
    $dup = $connection->prepare("SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1");
    $dup->bind_param("ss", $username, $email);
    $dup->execute();
    if ($dup->get_result()->num_rows > 0) {
        $dup->close();
        return error422('username or email already exists');
    }
    $dup->close();

    /*--------------------------------------------------------------------------
    | Hash password
    |--------------------------------------------------------------------------*/
    $password = password_hash($password_raw, PASSWORD_DEFAULT);

    /*--------------------------------------------------------------------------
    | Insert (prepared statement)
    |--------------------------------------------------------------------------*/
    $sql = "INSERT INTO users
                (fullname, country, gender, username, email, password, profile,
                 role_id, user_status, scholarship_id, group_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $connection->prepare($sql);
    if ($stmt === false) {
        $data = ['status' => 500, 'message' => 'Internal Server Error: ' . $connection->error];
        header("HTTP/1.0 500 Internal Server Error");
        echo json_encode($data);
        return;
    }

    $stmt->bind_param(
        "sssssssisii",
        $fullname, $country, $gender, $username, $email, $password, $profile,
        $role_id, $user_status, $scholarship_id, $group_id
    );

    if ($stmt->execute()) {
        $data = ['status' => 201, 'message' => 'User Created Successfully'];
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
| Get all users Function ::
|
| CHANGES:
|   - JOIN with roles to expose role_name (the old `role` column is gone).
|   - Excludes the password hash from the response (security best practice).
|--------------------------------------------------------------------------*/
function getUserList() {
    global $connection;

    $sql = "SELECT u.id, u.fullname, u.country, u.gender, u.username, u.email,
                   u.profile, u.role_id, u.user_status, u.scholarship_id, u.group_id,
                   u.created_at, u.updated_at,
                   r.role_name AS role_name
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id
            ORDER BY u.id DESC";

    $result = mysqli_query($connection, $sql);

    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            $response = mysqli_fetch_all($result, MYSQLI_ASSOC);
            $data = ['status' => 200, 'message' => 'Users Fetched Successfully', 'data' => $response];
            header("HTTP/1.0 200 Success");
            echo json_encode($data);
        } else {
            $data = ['status' => 404, 'message' => 'No User Found'];
            header("HTTP/1.0 404 No User Found");
            echo json_encode($data);
        }
    } else {
        $data = ['status' => 500, 'message' => 'Internal Server Error'];
        header("HTTP/1.0 500 Internal Server Error");
        echo json_encode($data);
    }
}

/*--------------------------------------------------------------------------
| Get Single User Function ::
|
| CHANGES:
|   - JOIN with roles to expose role_name.
|   - Prepared statement.
|   - Excludes password hash from response.
|--------------------------------------------------------------------------*/
function getSingleUser($userParams) {
    global $connection;

    if (!isset($userParams['id']) || $userParams['id'] === null || $userParams['id'] === '') {
        return error422('Enter your user id');
    }

    $userId = (int) $userParams['id'];

    $sql = "SELECT u.id, u.fullname, u.country, u.gender, u.username, u.email,
                   u.profile, u.role_id, u.user_status, u.scholarship_id, u.group_id,
                   u.created_at, u.updated_at,
                   r.role_name AS role_name
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id
            WHERE u.id = ?
            LIMIT 1";

    $stmt = $connection->prepare($sql);
    if ($stmt === false) {
        $data = ['status' => 500, 'message' => 'Internal Server Error'];
        header("HTTP/1.0 500 Internal Server Error");
        echo json_encode($data);
        return;
    }

    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $response = $result->fetch_assoc();
        $data = ['status' => 200, 'message' => 'User Fetched Successfully', 'data' => $response];
        header("HTTP/1.0 200 Success");
        echo json_encode($data);
    } else {
        $data = ['status' => 404, 'message' => 'No User Found'];
        header("HTTP/1.0 404 No User Found");
        echo json_encode($data);
    }
    $stmt->close();
}

/*--------------------------------------------------------------------------
| Update Function ::
|
| CHANGES:
|   - Accepts role_id / scholarship_id / group_id instead of removed text cols.
|   - Prepared statement.
|   - Re-hashes the password only if it's provided (otherwise keeps old one).
|--------------------------------------------------------------------------*/
function UpdateUser($userInput, $userParams) {
    global $connection;

    if (!isset($userParams['id']) || $userParams['id'] === null || $userParams['id'] === '') {
        return error422('Enter User Id');
    }

    $userId = (int) $userParams['id'];

    /*--------------------------------------------------------------------------
    | Verify the user exists first
    |--------------------------------------------------------------------------*/
    $chk = $connection->prepare("SELECT id FROM users WHERE id = ? LIMIT 1");
    $chk->bind_param("i", $userId);
    $chk->execute();
    if ($chk->get_result()->num_rows === 0) {
        $chk->close();
        $data = ['status' => 404, 'message' => 'User Not Found'];
        header("HTTP/1.0 404 Not Found");
        echo json_encode($data);
        return;
    }
    $chk->close();

    /*--------------------------------------------------------------------------
    | Read inputs
    |--------------------------------------------------------------------------*/
    $fullname     = trim($userInput['fullname']    ?? '');
    $country      = trim($userInput['country']     ?? '');
    $gender       = trim($userInput['gender']      ?? '');
    $username     = trim($userInput['username']    ?? '');
    $email        = trim($userInput['email']       ?? '');
    $profile      = trim($userInput['profile']     ?? '');
    $user_status  = trim($userInput['user_status'] ?? '');
    $password_raw = $userInput['password'] ?? '';

    $role_id        = isset($userInput['role_id'])        && $userInput['role_id'] !== ''        ? (int)$userInput['role_id']        : null;
    $scholarship_id = isset($userInput['scholarship_id']) && $userInput['scholarship_id'] !== '' ? (int)$userInput['scholarship_id'] : null;
    $group_id       = isset($userInput['group_id'])       && $userInput['group_id'] !== ''       ? (int)$userInput['group_id']       : null;

    /*--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------*/
    if (empty($fullname))                       return error422('enter your fullname');
    if (empty($country))                        return error422('enter your country');
    if (empty($gender))                         return error422('enter your gender');
    if (empty($username))                       return error422('enter your username');
    if (empty($email))                          return error422('enter your email');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return error422('enter a valid email');
    if (empty($profile))                        return error422('enter your profile');
    if (empty($user_status))                    return error422('enter your user status');

    /*--------------------------------------------------------------------------
    | Update query — if password provided, hash it; otherwise keep existing.
    |--------------------------------------------------------------------------*/
    if (!empty($password_raw)) {
        if (strlen($password_raw) < 8) {
            return error422('password must be at least 8 characters');
        }
        $password = password_hash($password_raw, PASSWORD_DEFAULT);

        $sql = "UPDATE users SET
                    fullname = ?, country = ?, gender = ?, username = ?, email = ?,
                    password = ?, profile = ?, role_id = ?, user_status = ?,
                    scholarship_id = ?, group_id = ?
                WHERE id = ? LIMIT 1";

        $stmt = $connection->prepare($sql);
        if ($stmt === false) {
            $data = ['status' => 500, 'message' => 'Internal Server Error'];
            header("HTTP/1.0 500 Internal Server Error");
            echo json_encode($data);
            return;
        }

        $stmt->bind_param(
            "sssssssisiii",
            $fullname, $country, $gender, $username, $email, $password, $profile,
            $role_id, $user_status, $scholarship_id, $group_id, $userId
        );
    } else {
        $sql = "UPDATE users SET
                    fullname = ?, country = ?, gender = ?, username = ?, email = ?,
                    profile = ?, role_id = ?, user_status = ?,
                    scholarship_id = ?, group_id = ?
                WHERE id = ? LIMIT 1";

        $stmt = $connection->prepare($sql);
        if ($stmt === false) {
            $data = ['status' => 500, 'message' => 'Internal Server Error'];
            header("HTTP/1.0 500 Internal Server Error");
            echo json_encode($data);
            return;
        }

        $stmt->bind_param(
            "ssssssisiii",
            $fullname, $country, $gender, $username, $email, $profile,
            $role_id, $user_status, $scholarship_id, $group_id, $userId
        );
    }

    if ($stmt->execute()) {
        $data = ['status' => 200, 'message' => 'User Updated Successfully'];
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
| Delete Function ::
|
| CHANGES:
|   - Prepared statement.
|   - Fixed column case (ID → id).
|--------------------------------------------------------------------------*/
function deleteleUser($userParams) {
    global $connection;

    if (!isset($userParams['id']) || $userParams['id'] === null || $userParams['id'] === '') {
        return error422('Enter User Id');
    }

    $userId = (int) $userParams['id'];

    $stmt = $connection->prepare("DELETE FROM users WHERE id = ? LIMIT 1");
    if ($stmt === false) {
        $data = ['status' => 500, 'message' => 'Internal Server Error'];
        header("HTTP/1.0 500 Internal Server Error");
        echo json_encode($data);
        return;
    }

    $stmt->bind_param("i", $userId);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $data = ['status' => 200, 'message' => 'User Deleted Successfully'];
            header("HTTP/1.0 200 OK");
            echo json_encode($data);
        } else {
            $data = ['status' => 404, 'message' => 'User Not Found'];
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
