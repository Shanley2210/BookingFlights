<?php
require __DIR__ . '/../vendor/autoload.php';
use WpOrg\Requests\Requests;

function getAccessToken(){
    $client_id = 'noG7qevvzqqwfztSp9qMiXuRGadtJiIv';
    $client_secret = 'GwDiOVb54XfcNz0u';
    
    $url = 'https://test.api.amadeus.com/v1/security/oauth2/token';
    
    $auth_data = [
        'grant_type'    => 'client_credentials',
        'client_id'     => $client_id,
        'client_secret' => $client_secret
    ];
    
    $headers = ['Content-Type' => 'application/x-www-form-urlencoded'];
    
    try {
        $requests_response = Requests::post($url, $headers, $auth_data);
    
        $response_body = json_decode($requests_response->body);
    
        if (isset($response_body->error)) {
            throw new Exception("API Error: " . json_encode($response_body));
        }
 
        $access_token = $response_body->access_token;

        return $access_token;
    
    } catch (Exception $e) {
        return "❌ Lỗi: " . $e->getMessage();
    }
}

?>