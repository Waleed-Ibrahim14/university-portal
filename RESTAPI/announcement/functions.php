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
function Insertnotifi($userInput){
    global $connection;

    $title = mysqli_real_escape_string($connection, $userInput['title']);
    $notifi_msg = mysqli_real_escape_string($connection, $userInput['notifi_msg']);
    $notifi_time = mysqli_real_escape_string($connection, $userInput['notifi_time']);
    $notifi_repeat = mysqli_real_escape_string($connection, $userInput['notifi_repeat']);
    $notifi_loop = mysqli_real_escape_string($connection, $userInput['notifi_loop']);
    $publish_date = mysqli_real_escape_string($connection, $userInput['publish_date']);
    $username = mysqli_real_escape_string($connection, $userInput['username']);
   //check userInput NOt Null
    if (empty(trim($title))) {
        return error422('enter your title');
    } else if(empty(trim($notifi_msg))) {
        return error422('enter your notifi msg');
    } else if(empty(trim($notifi_time))) {
        return error422('enter your notifi time');
    } else if(empty(trim($notifi_repeat))) {
        return error422('enter your notifi repeat');
    } else if(empty(trim($notifi_loop))) {
        return error422('enter your notifi loop');
    } else if(empty(trim($publish_date))) {
        return error422('enter your publish date');
    } else if(empty(trim($username))) {
        return error422('enter your username');
    }else{
        // INSERT query
        $query = "INSERT INTO announcements (title,notifi_msg,notifi_time,notifi_repeat,notifi_loop,publish_date,username,updated_at)
        VALUES ('$title','$notifi_msg','$notifi_time','$notifi_repeat','$notifi_loop','$publish_date','$username')";
        $insert_result = mysqli_query($connection,$query);

        if($insert_result){
            $data =['status' => 201 , 'message' => 'notifi Created Successfuly'];
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
| Get all announcements Funtion ::
|--------------------------------------------------------------------------*/
function getNotifiList(){ 
    global $connection;
    $sql_select = mysqli_query($connection, "SELECT * FROM `announcements`");
    if($sql_select){
        if(mysqli_num_rows($sql_select) > 0){
            $response = mysqli_fetch_all($sql_select, MYSQLI_ASSOC);
            $data =['status' => 200 ,'message' => 'announcements Fetched Successfully','data' => $response];
            header("HTTP/1.0 200 Success");
            echo json_encode($data);
        }else{
            $data =['status' => 404 ,'message' => 'No announcements Found'];
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
| Get Single Notifi Funtion ::
|--------------------------------------------------------------------------*/
function getSingleNotifi($userParams){
global $connection;
    if($userParams['id'] == null){
        return error422('Enter your user id');
    }
    $userId = mysqli_real_escape_string($connection,$userParams['id']);
    $user_query = "SELECT * FROM announcements WHERE id = '$userId' LIMIT 1";
    $result = mysqli_query($connection, $user_query);
    if($result){
        if(mysqli_num_rows($result) == 1){
            $response = mysqli_fetch_assoc($result);
            $data =['status' => 200 ,'message' => 'notifi Fetched Successfully','data' => $response];
            header("HTTP/1.0 200 Success");
            echo json_encode($data); 
        }else{
            $data =['status' => 404 ,'message' => 'No notifi Found'];
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
function updateNotifi($userInput, $userParams){
    global $connection;
    if(!isset($userParams['id'])){
        return error422('User Id Not Found In URL');
    }else if($userParams['id'] == null){
        return error422('Enter User Id');
    }
    $userId = mysqli_real_escape_string($connection,$userParams['id']);
    
    $title = mysqli_real_escape_string($connection, $userInput['title']);
    $notifi_msg = mysqli_real_escape_string($connection, $userInput['notifi_msg']);
    $notifi_time = mysqli_real_escape_string($connection, $userInput['notifi_time']);
    $username = mysqli_real_escape_string($connection, $userInput['username']);
    $notifi_repeat = mysqli_real_escape_string($connection, $userInput['notifi_repeat']);
    $notifi_loop = mysqli_real_escape_string($connection, $userInput['notifi_loop']);
    $publish_date = mysqli_real_escape_string($connection, $userInput['publish_date']);
    //check userInput NOt Null
    if (empty(trim($title))) {
        return error422('enter your title');
    } else if(empty(trim($notifi_msg))) {
        return error422('enter your notifi msg');
    } else if(empty(trim($notifi_time))) {
        return error422('enter your notifi time');
    } else if(empty(trim($username))) {
        return error422('enter your username');
    } else if(empty(trim($notifi_repeat))) {
        return error422('enter your notifi repeat');
    } else if(empty(trim($notifi_loop))) {
        return error422('enter your notifi loop');
    } else if(empty(trim($publish_date))) {
        return error422('enter your publish date');
   }else{
        //UPDATE query 
        $query = "UPDATE announcements SET  title = '$title',notifi_msg = '$notifi_msg',notifi_time = '$notifi_time',
        notifi_repeat = '$notifi_repeat',notifi_loop = '$notifi_loop',publish_date = '$publish_date',
        username = '$username' updated_at = date() WHERE id = '$userId' LIMIT 1";
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
function deleteleNotifi($userParams){
    global $connection;
    if(!isset($userParams['id'])){
        return error422('User Id Not Found In URL');
    }else if($userParams['id'] == null){
        return error422('Enter User Id');
    }
    $userId = mysqli_real_escape_string($connection,$userParams['id']);
    $delete_query = "DELETE FROM announcements WHERE ID='$userId' LIMIT 1";
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