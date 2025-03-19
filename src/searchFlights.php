<?php
require __DIR__ . '/../vendor/autoload.php';

use WpOrg\Requests\Requests;

function searchFlights($access_token, $origin, $destination, $date, $adults = 1) {
    $url = "https://test.api.amadeus.com/v2/shopping/flight-offers?" . http_build_query([
        "originLocationCode" => $origin,
        "destinationLocationCode" => $destination,
        "departureDate" => $date,
        "adults" => $adults,
    ]);

    $headers = [
        "Authorization" => "Bearer " . $access_token,
        "Content-Type"  => "application/json"
    ];

    try {
        $response = Requests::get($url, $headers);
        $data = json_decode($response->body, true);

        if (isset($data['errors'])) {
            throw new Exception("API Error: " . json_encode($data['errors']));
        }

        return $data['data']; // Trả về danh sách chuyến bay

    } catch (Exception $e) {
        return "❌ Lỗi: " . $e->getMessage();
    }
}
?>
