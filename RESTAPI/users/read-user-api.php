<?php
header('Access-Controle-Allow-Origin:*');
header('Content-Type: application/json');
header('Access-Controle-Allow-Method: GET');
header('Access-Controle-Allow-Headers: Content-Type, Access-Controle-Allow-Headers, Authorization, X-Request-With');
include("functions.php");
/*--------------------------------------------------------------------------
| API Link For Select All Users, Single User Based On Id Passed Through The GET Method  ::
|--------------------------------------------------------------------------*/
$RequestMethod = $_SERVER['REQUEST_METHOD'];
if ($RequestMethod == 'GET') {
    if(isset($_GET['id'])){
        $user = getSingleUser($_GET);
        echo $user;
    }else{
    $getUserList = getUserList();
    }
}else{
    $data =[ 
        'status' => 405 ,
        'message' => $RequestMethod. ' Method Not Allowed'
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data);
}
?>