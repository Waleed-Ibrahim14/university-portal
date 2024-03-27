<?php
error_reporting(0);
header('Access-Controle-Allow-Origin:*');
header('Content-Type: application/json');
header('Access-Controle-Allow-Method: PUT');
header('Access-Controle-Allow-Headers: Content-Type, Access-Controle-Allow-Headers, Authorization, X-Request-With');
include("functions.php");
/*--------------------------------------------------------------------------
| API Link For Update User Based On Id Passed Through The GET Method  ::
|--------------------------------------------------------------------------*/
$RequestMethod = $_SERVER['REQUEST_METHOD'];
if ($RequestMethod == 'PUT') {
    $inputData  = json_decode(file_get_contents("php://input"), true);
    $updateuser = UpdateUser($inputData,$_GET);
    echo $updateuser;
}else{
    $data =[ 
        'status' => 405 ,
        'message' => $RequestMethod. ' Method Not Allowed'
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data);
}
?>