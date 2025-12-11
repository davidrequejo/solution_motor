<?php 
require_once "../sunat/ConfigSunat.php";

// Crear la solicitud para obtener el token
$client_id = '665e6380-9a07-4226-8147-51513df42dc4';
$client_secret = 'mgnV41fwGqzNMNHs6WFDRQ==';

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://api-seguridad.sunat.gob.pe/v1/authorize');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'client_id' => $client_id,
    'client_secret' => $client_secret,
    'grant_type' => 'client_credentials',
]));

$response = curl_exec($ch);
curl_close($ch);

// Decodificar la respuesta JSON
$data = json_decode($response, true);

// Verificar si se obtuvo el token
if (isset($data['access_token'])) {
    $access_token = $data['access_token'];
    echo 'Token obtenido exitosamente: ' . $access_token . PHP_EOL;
} else {
    echo 'Error al obtener el token' . PHP_EOL;
    var_dump($data);
}


?>