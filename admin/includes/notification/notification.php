<?php 
session_start();
include_once(__DIR__ . '/Push.php');

$push = new Push();
$array = [];
$rows  = [];
$record = 0;

$username = $_SESSION['username'] ?? '';
if (empty($username)) {
    // Not logged in — return empty
    echo json_encode(['notif' => [], 'count' => 0, 'result' => false]);
    exit;
}

$notifList = $push->listNotificationUser($username);

foreach ($notifList as $key) {
    $data['title'] = $key['title'];
    $data['msg']   = $key['notif_msg'];
    $data['icon']  = '../assets/images/logo.png';
    $data['url']   = 'http://localhost/university-portal/';
    $rows[] = $data;

    $nextime = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s')) + ($key['notif_repeat'] * 60));
    $push->updateNotification($key['id'], $nextime);
    $record++;
}

$array['notif']  = $rows;
$array['count']  = $record;
$array['result'] = true;

header('Content-Type: application/json');
echo json_encode($array);
?>
