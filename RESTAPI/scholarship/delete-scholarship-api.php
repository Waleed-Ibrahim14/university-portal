<?php
header('Access-Controle-Allow-Origin:*');
header('Content-Type: application/json');
header('Access-Controle-Allow-Method: DELETE');
header('Access-Controle-Allow-Headers: Content-Type, Access-Controle-Allow-Headers, Authorization, X-Request-With');
include("functions.php");
/*--------------------------------------------------------------------------
| API Link For Delete Based User On Id Passed Through The GET Method  ::
|--------------------------------------------------------------------------*/
$RequestMethod = $_SERVER['REQUEST_METHOD'];
if ($RequestMethod == 'DELETE') {
        $deleteuser = deleteleUser($_GET);
        echo $deleteuser;
}else{
    $data =[ 
        'status' => 405 ,
        'message' => $RequestMethod. ' Method Not Allowed'
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data);
}
?>