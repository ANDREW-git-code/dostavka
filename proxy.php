<?php
error_log(print_r($_SERVER, true)); 
error_log(file_get_contents('php://input'));
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$api_url = 'https://robot.dostavista.ru/public/api/courier-partner/1.0/couriers';
$api_token = 'FEC0143D7207AB50E062366312BBE64B0C46EDBD'; // Ваш ключ

$data = file_get_contents('php://input');

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $api_url,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $api_token, // Исправлено здесь
        'Content-Type: application/json'
    ],
    CURLOPT_POSTFIELDS => $data,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => true // Для продакшена оставьте true
]);

$response = curl_exec($ch);
$error = curl_error($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

if ($error) {
    http_response_code(500);
    echo json_encode(['error' => 'CURL Error: ' . $error]);
} else {
    http_response_code($http_code);
    echo $response;
}