<?php
// post_geo.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$raw = file_get_contents("php://input");
$data = json_decode($raw,true);

if(!$data){
    echo json_encode(['ok'=>false,'msg'=>'invalid json']);
    exit;
}

$lat = isset($data['lat']) ? floatval($data['lat']) : '';
$lon = isset($data['lon']) ? floatval($data['lon']) : '';
$acc = isset($data['accuracy_m']) ? floatval($data['accuracy_m']) : '';
$ts = isset($data['timestamp']) ? intval($data['timestamp']) : time();
$ua = isset($data['userAgent']) ? str_replace(["\n","\r"],' ',substr($data['userAgent'],0,200)) : '';
$origin = isset($data['origin']) ? $data['origin'] : 'unknown';

// Générer le lien Google Maps côté serveur
$maps_link = "https://www.google.com/maps?q=$lat,$lon";

$dir = __DIR__;
$file = $dir . '/locations.csv';

// Créer le fichier avec en-tête si inexistant
if(!file_exists($file)){
    file_put_contents($file, "datetime,lat,lon,accuracy,ua,origin,maps_link\n", FILE_APPEND | LOCK_EX);
}

// Ajouter la ligne avec le lien dans le CSV
$line = sprintf(
    "%s,%s,%s,%s,\"%s\",\"%s\",\"%s\"\n",
    date('c',$ts), $lat, $lon, $acc, addslashes($ua), addslashes($origin), $maps_link
);
file_put_contents($file, $line, FILE_APPEND | LOCK_EX);

// Réponse JSON simple (peut être ignorée côté client)
echo json_encode(['ok'=>true,'saved'=>true]);
