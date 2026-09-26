<?php
error_reporting(0);

/*--------------------------------------------------------------------------
| CORS headers
|--------------------------------------------------------------------------*/
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: PUT');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

include("functions.php");

/*--------------------------------------------------------------------------
| API Link For Update User Based On Id Passed Through The GET Method ::
|--------------------------------------------------------------------------*/
$RequestMethod = $_SERVER['REQUEST_METHOD'];

if ($RequestMethod === 'PUT') {
    $inputData  = json_decode(file_get_contents("php://input"), true);
    $updateuser = UpdateUser($inputData, $_GET);
    echo $updateuser;
} else {
    $data = [
        'status'  => 405,
        'message' => $RequestMethod . ' Method Not Allowed'
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data);
}
?>
