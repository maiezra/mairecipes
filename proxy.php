<?php
// Include database and functions
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/functions.php';

$query = isset($_GET['s']) ? urlencode($_GET['s']) : '';
$url = "https://www.themealdb.com/api/json/v1/1/search.php?s=" . $query;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($httpcode >= 200 && $httpcode < 300) {
    header('Content-Type: application/json');
    echo $response;
} else {
    http_response_code($httpcode);
    $errorMessage = [
        'error' => 'Unable to fetch data from TheMealDB API',
        'httpcode' => $httpcode,
        'curl_error' => $error,
        'response' => $response,
        'url' => $url
    ];
    echo json_encode($errorMessage);
    error_log(json_encode($errorMessage));  // Log the error for further investigation
}
?>
