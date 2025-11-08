<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$raw = file_get_contents("php://input");
if(!$raw){
    echo json_encode(['ok'=>false,'msg'=>'no body']); exit;
}
$data = json_decode($raw,true);
if(!$data){
    echo json_encode(['ok'=>false,'msg'=>'invalid json']); exit;
}

// Récupération des données
$lat = $data['lat'] ?? '';
$lon = $data['lon'] ?? '';
$acc = $data['accuracy_m'] ?? '';
$ts = $data['timestamp'] ?? time();
$ua = $data['userAgent'] ?? '';
$origin = $data['origin'] ?? 'unknown';
$mapsLink = $data['mapsLink'] ?? '';

$file = __DIR__ . '/locations.csv';

// Créer le fichier si inexistant
if(!file_exists($file)){
    file_put_contents($file,"datetime,lat,lon,accuracy,ua,origin,maps_link\n", FILE_APPEND | LOCK_EX);
}

// Ajouter une ligne
$line = sprintf("%s,%s,%s,%s,\"%s\",\"%s\",\"%s\"\n",
    date('c',$ts), $lat, $lon, $acc, addslashes($ua), addslashes($origin), $mapsLink);
file_put_contents($file, $line, FILE_APPEND | LOCK_EX);

echo json_encode(['ok'=>true,'saved'=>true]);
?>
