<?php
error_reporting(0);

/*--------------------------------------------------------------------------
| CORS headers
| NOTE: fixed typos ("Access-Controle" → "Access-Control")
|--------------------------------------------------------------------------*/
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

include("functions.php");

/*--------------------------------------------------------------------------
| API Link For Create New Announcement ::
|--------------------------------------------------------------------------*/
$RequestMethod = $_SERVER['REQUEST_METHOD'];

if ($RequestMethod === 'POST') {
    $inputData = json_decode(file_get_contents("php://input"), true);

    if (empty($inputData)) {
        $insertnotifi = Insertnotifi($_POST);
    } else {
        $insertnotifi = Insertnotifi($inputData);
    }
    echo $insertnotifi;
} else {
    $data = [
        'status'  => 405,
        'message' => $RequestMethod . ' Method Not Allowed'
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data);
}
?>
