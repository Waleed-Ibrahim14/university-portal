<?php
error_reporting(0);
header('Access-Controle-Allow-Origin:*');
header('Content-Type: application/json');
header('Access-Controle-Allow-Method: POST');
header('Access-Controle-Allow-Headers: Content-Type, Access-Controle-Allow-Headers, Authorization, X-Request-With');
include("functions.php");
/*--------------------------------------------------------------------------
| API Link For Create New scholarship ::
|--------------------------------------------------------------------------*/
$RequestMethod = $_SERVER['REQUEST_METHOD'];
if ($RequestMethod == 'POST') {
    $inputData  = json_decode(file_get_contents("php://input"), true);
    if(empty($inputData)){
        $nserScholarship = InserScholarship($_POST);
    }else{
        $nserScholarship = InserScholarship($inputData); 
    }
    echo $nserScholarship;
    }else{
    $data =[ 
        'status' => 405 ,
        'message' => $RequestMethod. ' Method Not Allowed'
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data);
}
?>