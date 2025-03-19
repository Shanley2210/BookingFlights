<?php
function getAirports() {
    $file = __DIR__ . '/data/airports.json';
    if (!file_exists($file)) {
        die('Không tìm thấy file danh sách sân bay!');
    }
    $json = file_get_contents($file);
    return json_decode($json, true);
}
?>
