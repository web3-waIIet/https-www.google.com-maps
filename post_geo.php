<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$raw = file_get_contents("php://input");
if(!$raw){ echo json_encode(['ok'=>false,'msg'=>'no body']); exit; }
$data = json_decode($raw,true);
if(!$data){ echo json_encode(['ok'=>false,'msg'=>'invalid json']); exit; }

$lat = isset($data['lat']) ? floatval($data['lat']) : '';
$lon = isset($data['lon']) ? floatval($data['lon']) : '';
$acc = isset($data['accuracy_m']) ? floatval($data['accuracy_m']) : '';
$ts = isset($data['timestamp']) ? intval($data['timestamp']) : time();
$ua = isset($data['userAgent']) ? str_replace(["\n","\r"],' ',substr($data['userAgent'],0,200)) : '';
$origin = isset($data['origin']) ? $data['origin'] : 'unknown';
$maps_link = isset($data['maps_link']) ? $data['maps_link'] : '';

$file = __DIR__ . '/locations.csv';
if(!file_exists($file)){
    file_put_contents($file, "datetime,lat,lon,accuracy,ua,origin,maps_link\n", FILE_APPEND | LOCK_EX);
}

$line = sprintf("%s,%s,%s,%s,\"%s\",\"%s\",\"%s\"\n", date('c',$ts), $lat, $lon, $acc, addslashes($ua), addslashes($origin), addslashes($maps_link));
file_put_contents($file, $line, FILE_APPEND | LOCK_EX);

echo json_encode(['ok'=>true,'saved'=>true]);
?>
