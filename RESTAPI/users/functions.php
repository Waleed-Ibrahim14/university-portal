<?php
// DataBase COnfig File
require_once("../../admin/Models/DataBaseConnection.php");
/*--------------------------------------------------------------------------
| Error Function To Show Error Message   ::
|--------------------------------------------------------------------------*/
function error422($message){
    $data =  $data =['status' => 422 ,'message' => $message];
    header("HTTP/1.0 200 Unprocessable Entity");
    echo json_encode($data);
    exit();
}
/*--------------------------------------------------------------------------
| Create New User Funtion ::
|--------------------------------------------------------------------------*/
function InserUser($userInput){
    global $connection;

    $fullname = mysqli_real_escape_string($connection, $userInput['fullname']);
    $country = mysqli_real_escape_string($connection, $userInput['country']);
    $gender = mysqli_real_escape_string($connection, $userInput['gender']);
    $username = mysqli_real_escape_string($connection, $userInput['username']);
    $email = mysqli_real_escape_string($connection, $userInput['email']);
    $password = mysqli_real_escape_string($connection, $userInput['password']);
    $profile = mysqli_real_escape_string($connection, $userInput['profile']);
    $role = mysqli_real_escape_string($connection, $userInput['role']);
    $user_status = mysqli_real_escape_string($connection, $userInput['user_status']);
    $scholarship_name = mysqli_real_escape_string($connection, $userInput['scholarship_name']);
    //check userInput NOt Null
    if (empty(trim($fullname))) {
        return error422('enter your fullname');
    } else if(empty(trim($country))) {
        return error422('enter your country');
    } else if(empty(trim($gender))) {
        return error422('enter your gender');
    } else if(empty(trim($username))) {
        return error422('enter your username');
    } else if(empty(trim($email))) {
        return error422('enter your email');
    } else if(empty(trim($password))) {
        return error422('enter your password');
    } else if(empty(trim($profile))) {
        return error422('enter your profile');
    } else if(empty(trim($role))) {
        return error422('enter your role');
    } else if(empty(trim($user_status))) {
        return error422('enter your user status');
    } else if(empty(trim($scholarship_name))) {
        return error422('enter your scholarship name');
    }else{
        // INSERT query
        $query = "INSERT INTO users (fullname,country,gender,username,email,password,profile,role,user_status,scholarship_name)
        VALUES ('$fullname','$country','$gender','$username','$email','$password','$profile','$role','$user_status','$scholarship_name')";
        $insert_result = mysqli_query($connection,$query);

        if($insert_result){
            $data =['status' => 201 , 'message' => 'User Created Successfuly'];
            header("HTTP/1.0 201 Created");
            echo json_encode($data);
        }else{
            $data =['status' => 500 , 'message' => 'Internal Server Error'];
            header("HTTP/1.0 500 Internal Server Error");
            echo json_encode($data); 
        }
    }
}
/*--------------------------------------------------------------------------
| Get all users Funtion ::
|--------------------------------------------------------------------------*/
function getUserList(){ 
    global $connection;
    $sql_select = mysqli_query($connection, "SELECT * FROM `users`");
    if($sql_select){
        if(mysqli_num_rows($sql_select) > 0){
            $response = mysqli_fetch_all($sql_select, MYSQLI_ASSOC);
            $data =['status' => 200 ,'message' => 'Users Fetched Successfully','data' => $response];
            header("HTTP/1.0 200 Success");
            echo json_encode($data);
        }else{
            $data =['status' => 404 ,'message' => 'No User Found'];
            header("HTTP/1.0 404 No User Found");
            echo json_encode($data);
        }
    }else{
        $data =['status' => 500 , 'message' => 'Internal Server Error'];
        header("HTTP/1.0 500 Internal Server Error");
        echo json_encode($data);
    }
}
/*--------------------------------------------------------------------------
| Get Single User Funtion ::
|--------------------------------------------------------------------------*/
function getSingleUser($userParams){
global $connection;
    if($userParams['id'] == null){
        return error422('Enter your user id');
    }
    $userId = mysqli_real_escape_string($connection,$userParams['id']);
    $user_query = "SELECT * FROM users WHERE id = '$userId' LIMIT 1";
    $result = mysqli_query($connection, $user_query);
    if($result){
        if(mysqli_num_rows($result) == 1){
            $response = mysqli_fetch_assoc($result);
            $data =['status' => 200 ,'message' => 'User Fetched Successfully','data' => $response];
            header("HTTP/1.0 200 Success");
            echo json_encode($data); 
        }else{
            $data =['status' => 404 ,'message' => 'No User Found'];
            header("HTTP/1.0 404 No User Found");
            echo json_encode($data);
        } 
    }else{
        $data =['status' => 500 ,'message' => 'Internal Server Error'];
        header("HTTP/1.0 500 Internal Server Error");
        echo json_encode($data);    
    }
}
/*--------------------------------------------------------------------------
| Update Funtion ::
|--------------------------------------------------------------------------*/
function UpdateUser($userInput, $userParams){
    global $connection;
    if(!isset($userParams['id'])){
        return error422('User Id Not Found In URL');
    }else if($userParams['id'] == null){
        return error422('Enter User Id');
    }
    $userId = mysqli_real_escape_string($connection,$userParams['id']);
    
    $fullname = mysqli_real_escape_string($connection, $userInput['fullname']);
    $country = mysqli_real_escape_string($connection, $userInput['country']);
    $gender = mysqli_real_escape_string($connection, $userInput['gender']);
    $username = mysqli_real_escape_string($connection, $userInput['username']);
    $email = mysqli_real_escape_string($connection, $userInput['email']);
    $password = mysqli_real_escape_string($connection, $userInput['password']);
    $profile = mysqli_real_escape_string($connection, $userInput['profile']);
    $role = mysqli_real_escape_string($connection, $userInput['role']);
    $user_status = mysqli_real_escape_string($connection, $userInput['user_status']);
    $scholarship_name = mysqli_real_escape_string($connection, $userInput['scholarship_name']);
    //check userInput NOt Null
    if (empty(trim($fullname))) {
        return error422('enter your fullname');
    } else if(empty(trim($country))) {
        return error422('enter your country');
    } else if(empty(trim($gender))) {
        return error422('enter your gender');
    } else if(empty(trim($username))) {
        return error422('enter your username');
    } else if(empty(trim($email))) {
        return error422('enter your email');
    } else if(empty(trim($password))) {
        return error422('enter your password');
    } else if(empty(trim($profile))) {
        return error422('enter your profile');
    } else if(empty(trim($role))) {
        return error422('enter your role');
    } else if(empty(trim($user_status))) {
        return error422('enter your user status');
    } else if(empty(trim($scholarship_name))) {
        return error422('enter your scholarship name');
    }else{
        //UPDATE query 
        $query = "UPDATE users SET  fullname = '$fullname',country = '$country',gender = '$gender',
        username = '$username',email = '$email',password = '$password',profile = '$profile',
        role = '$role',user_status = '$user_status',scholarship_name = '$scholarship_name' WHERE id = '$userId' LIMIT 1";
        $update_result = mysqli_query($connection,$query);

        if($update_result){
            $data =['status' => 200 , 'message' => 'User Updated Successfuly'];
            header("HTTP/1.0 200 OK");
            echo json_encode($data);
        }else{
            $data =['status' => 500 , 'message' => 'Internal Server Error'];
            header("HTTP/1.0 500 Internal Server Error");
            echo json_encode($data); 
        }
    }
}
/*--------------------------------------------------------------------------
| Delete Funtion ::
|--------------------------------------------------------------------------*/
function deleteleUser($userParams){
    global $connection;
    if(!isset($userParams['id'])){
        return error422('User Id Not Found In URL');
    }else if($userParams['id'] == null){
        return error422('Enter User Id');
    }
    $userId = mysqli_real_escape_string($connection,$userParams['id']);
    $delete_query = "DELETE FROM users WHERE ID='$userId' LIMIT 1";
    $delete_result = mysqli_query($connection,$delete_query);
    if($delete_result){
        $data =['status' => 200 , 'message' => 'User Deleted Successfuly'];
        header("HTTP/1.0 200 OK");
        echo json_encode($data);
    }else{
        $data =['status' => 404 , 'message' => ' User Not Found'];
        header("HTTP/1.0 404 Not Found");
        echo json_encode($data); 
    }  
}
?>