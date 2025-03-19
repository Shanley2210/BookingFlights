<?php
include './src/loadAirports.php';
include './src/getAccessToken.php';
include './src/searchFlights.php';

// Đọc danh sách hãng hàng không từ file JSON
$airlinesData = json_decode(file_get_contents("./src/data/airlines.json"), true);

// Hàm lấy tên hãng hàng không từ carrierCode
function getAirlineName($carrierCode, $airlinesData) {
    foreach ($airlinesData as $airline) {
        if ($airline['carrierCode'] == $carrierCode) {
            return $airline['name'];
        }
    }
    return "Hãng không xác định";
}

$airports = getAirports();
$access_token = getAccessToken();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tìm chuyến bay</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2 class="mb-4">Tìm chuyến bay</h2>

    <form method="POST">
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="from" class="form-label">Sân bay đi</label>
                <select class="form-select" name="from" id="from" required>
                    <option value="">Chọn sân bay</option>
                    <?php foreach ($airports as $airport) { ?>
                        <option value="<?= $airport['iataCode'] ?>"><?= $airport['name'] ?> (<?= $airport['iataCode'] ?>)</option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="to" class="form-label">Sân bay đến</label>
                <select class="form-select" name="to" id="to" required>
                    <option value="">Chọn sân bay</option>
                    <?php foreach ($airports as $airport) { ?>
                        <option value="<?= $airport['iataCode'] ?>"><?= $airport['name'] ?> (<?= $airport['iataCode'] ?>)</option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="date" class="form-label">Ngày đi</label>
                <input type="date" class="form-control" name="date" id="date" required>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Tìm chuyến bay</button>
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $from = $_POST["from"];
        $to = $_POST["to"];
        $date = $_POST["date"];

        $flights = searchFlights($access_token, $from, $to, $date, 1, 5);
        
        if (!empty($flights)) {
            foreach ($flights as $flight) {
                $segment = $flight['itineraries'][0]['segments'][0];
                $departure = $segment['departure'];
                $arrival = $segment['arrival'];
                $price = $flight['price']['grandTotal'];
                $currency = $flight['price']['currency'];
                $carrierCode = $segment['carrierCode'];
                $flightNumber = $segment['number'];
                $duration = $segment['duration'];

                // Lấy tên hãng hàng không
                $airlineName = getAirlineName($carrierCode, $airlinesData);

                echo '
                <div class="card mt-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title">' . $airlineName . ' (' . $carrierCode . ' ' . $flightNumber . ')</h5>
                            <span class="fw-bold text-danger fs-5">' . number_format($price, 2) . ' ' . $currency . '</span>
                        </div>
                        <p class="card-text">
                            <strong>⏰ ' . date("H:i", strtotime($departure['at'])) . '</strong> - ' . $departure['iataCode'] . ' ✈ 
                            <strong>' . date("H:i", strtotime($arrival['at'])) . '</strong> - ' . $arrival['iataCode'] . '<br>
                            🕒 <em>' . str_replace("PT", "", $duration) . ' (Bay thẳng)</em>
                        </p>
                        <button class="btn btn-primary">Chọn</button>
                    </div>
                </div>';
            }
        } else {
            echo '<p class="text-danger mt-4">Không tìm thấy chuyến bay nào!</p>';
        }
    }
    ?>

</body>
</html>
