<?php
if (!isset($_GET['lat']) || !isset($_GET['lon'])) {
    echo json_encode(["error" => "Missing parameters"]);
    exit;
}

$lat = $_GET['lat'];
$lon = $_GET['lon'];

// Call Nominatim API
$url = "https://nominatim.openstreetmap.org/reverse?format=json&lat=$lat&lon=$lon&zoom=18&addressdetails=1";

$opts = [
    "http" => [
        "header" => "User-Agent: LakbayanApp/1.0\r\n"
    ]
];
$context = stream_context_create($opts);
$response = file_get_contents($url, false, $context);

if ($response === FALSE) {
    echo json_encode(["error" => "Failed to fetch"]);
} else {
    echo $response;
}
