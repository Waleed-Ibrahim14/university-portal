<?php
/*--------------------------------------------------------------------------
| CORS headers
|--------------------------------------------------------------------------*/
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: DELETE');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

include("functions.php");

/*--------------------------------------------------------------------------
| API Link For Delete User Based On Id Passed Through The GET Method ::
|--------------------------------------------------------------------------*/
$RequestMethod = $_SERVER['REQUEST_METHOD'];

if ($RequestMethod === 'DELETE') {
    $deleteuser = deleteleUser($_GET);
    echo $deleteuser;
} else {
    $data = [
        'status'  => 405,
        'message' => $RequestMethod . ' Method Not Allowed'
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data);
}
?>
