<?php
/*--------------------------------------------------------------------------
| CORS headers
|--------------------------------------------------------------------------*/
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

include("functions.php");

/*--------------------------------------------------------------------------
| API Link For Select All Announcements, or Single Announcement (GET id)
|--------------------------------------------------------------------------*/
$RequestMethod = $_SERVER['REQUEST_METHOD'];

if ($RequestMethod === 'GET') {
    if (isset($_GET['id'])) {
        $user = getSingleNotifi($_GET);
        echo $user;
    } else {
        $getNotifiList = getNotifiList();
    }
} else {
    $data = [
        'status'  => 405,
        'message' => $RequestMethod . ' Method Not Allowed'
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data);
}
?>
