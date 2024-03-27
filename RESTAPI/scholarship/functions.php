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
| Create New cholarship Funtion ::
|--------------------------------------------------------------------------*/
function InserScholarship($scholarshipInput){
    global $connection;

    $scholarship_name = mysqli_real_escape_string($connection, $scholarshipInput['scholarship_name']);
    $scholarship_description = mysqli_real_escape_string($connection, $scholarshipInput['scholarship_description']);
    $amount = mysqli_real_escape_string($connection, $scholarshipInput['amount']);
    $date = mysqli_real_escape_string($connection, $scholarshipInput['date']);
    $scholarship_status = mysqli_real_escape_string($connection, $scholarshipInput['scholarship_status']);
    $added_by = mysqli_real_escape_string($connection, $scholarshipInput['added_by']);
    //check cholarship NOt Null
    if (empty(trim($scholarship_name))) {
        return error422('enter scholarship name');
    } else if(empty(trim($scholarship_description))) {
        return error422('enter scholarship description');
    } else if(empty(trim($amount))) {
        return error422('enter amount');
    } else if(empty(trim($date))) {
        return error422('enter date');
    } else if(empty(trim($scholarship_status))) {
        return error422('enter scholarship status');
    } else if(empty(trim($added_by))) {
        return error422('enter added_by');
    }else{
        // INSERT query
        $query = "INSERT INTO scholarships (scholarship_name,scholarship_description,amount,date,scholarship_status,added_by)
        VALUES ('$scholarship_name','$scholarship_description','$amount','$date','$scholarship_status','$added_by')";
        $insert_result = mysqli_query($connection,$query);

        if($insert_result){
            $data =['status' => 201 , 'message' => 'Created Successfuly'];
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
| Get all scholarships Funtion ::
|--------------------------------------------------------------------------*/
function getScholarshipList(){ 
    global $connection;
    $sql_select = mysqli_query($connection, "SELECT * FROM `scholarships`");
    if($sql_select){
        if(mysqli_num_rows($sql_select) > 0){
            $response = mysqli_fetch_all($sql_select, MYSQLI_ASSOC);
            $data =['status' => 200 ,'message' => 'Fetched Successfully','data' => $response];
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
| Get Single cholarship Funtion ::
|--------------------------------------------------------------------------*/
function getSingleScholarship($userParams){
global $connection;
    if($userParams['id'] == null){
        return error422('Enter cholarship id');
    }
    $scholarshipId = mysqli_real_escape_string($connection,$userParams['id']);
    $user_query = "SELECT * FROM scholarships WHERE id = '$scholarshipId' LIMIT 1";
    $result = mysqli_query($connection, $user_query);
    if($result){
        if(mysqli_num_rows($result) == 1){
            $response = mysqli_fetch_assoc($result);
            $data =['status' => 200 ,'message' => 'sholarship Fetched Successfully','data' => $response];
            header("HTTP/1.0 200 Success");
            echo json_encode($data); 
        }else{
            $data =['status' => 404 ,'message' => 'No cholarship Found'];
            header("HTTP/1.0 404 No cholarship Found");
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
function UpdateUser($scholarshipInput, $userParams){
    global $connection;
    if(!isset($userParams['id'])){
        return error422('Id Not Found In URL');
    }else if($userParams['id'] == null){
        return error422('Enter cholarship Id');
    }
    $scholarshipId = mysqli_real_escape_string($connection,$userParams['id']);
    
    $scholarship_name = mysqli_real_escape_string($connection, $scholarshipInput['scholarship_name']);
    $scholarship_description = mysqli_real_escape_string($connection, $scholarshipInput['scholarship_description']);
    $amount = mysqli_real_escape_string($connection, $scholarshipInput['amount']);
    $date = mysqli_real_escape_string($connection, $scholarshipInput['date']);
    $scholarship_status = mysqli_real_escape_string($connection, $scholarshipInput['scholarship_status']);
    $added_by = mysqli_real_escape_string($connection, $scholarshipInput['added_by']);
    //check scholarshipInput NOt Null
    if (empty(trim($scholarship_name))) {
        return error422('enter scholarship name');
    } else if(empty(trim($scholarship_description))) {
        return error422('enter scholarship description');
    } else if(empty(trim($amount))) {
        return error422('enter amount');
    } else if(empty(trim($date))) {
        return error422('enter date');
    } else if(empty(trim($scholarship_status))) {
        return error422('enter scholarship status');
    } else if(empty(trim($added_by))) {
        return error422('enter added_by');
    }else{
        //UPDATE query 
        $query = "UPDATE `scholarships` SET  `scholarship_name` = '$scholarship_name',`scholarship_description` = '$scholarship_description',
        `amount` = '$amount',`date` = '$date',`scholarship_status` = '$scholarship_status',`added_by` = '$added_by' WHERE id = '$scholarshipId' LIMIT 1";
        $update_result = mysqli_query($connection,$query);

        if($update_result){
            $data =['status' => 200 , 'message' => 'Updated Successfuly'];
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
        return error422('Id Not Found In URL');
    }else if($userParams['id'] == null){
        return error422('Enter Id');
    }
    $scholarshipId = mysqli_real_escape_string($connection,$userParams['id']);
    $delete_query = "DELETE FROM scholarships WHERE ID='$scholarshipId' LIMIT 1";
    $delete_result = mysqli_query($connection,$delete_query);
    if($delete_result){
        $data =['status' => 200 , 'message' => ' Deleted Successfuly'];
        header("HTTP/1.0 200 OK");
        echo json_encode($data);
    }else{
        $data =['status' => 404 , 'message' => ' Not Found'];
        header("HTTP/1.0 404 Not Found");
        echo json_encode($data); 
    }  
}
?>