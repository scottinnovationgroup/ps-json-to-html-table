<?php

// Get the origin of the request
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

// Define allowed origins
$allowed_origins = [
    'https://portfoliostack.com',
    'http://localhost:8888'
];

// Check if the origin is in the allowed origins
if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
}

print $_SERVER['HTTP_REFERER'];
exit();

if(!$_GET['item']) {
    exit("Invalid item.");
}

function webhook_add_industry_json_file($query) {
    $url = 'https://hooks.zapier.com/hooks/catch/18108931/2svviql/';

    $ch = curl_init();

    $query_url = $url . '?query=' . urlencode($query);

    curl_setopt($ch, CURLOPT_URL, $query_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        exit("cURL error: " . curl_error($ch));
    }

    curl_close($ch);

    return $response;
}

$json_file_path = 'all_industry.json';

$json_data = file_get_contents($json_file_path);

$data = json_decode($json_data, true);

$item_key = $_GET['item'];

foreach ($data as $item) {
    if ($item_key && array_key_exists($item_key, $item)) {
        $result = json_encode($item);
        break;
    } else {
        $result = json_encode(array('result'=>'false'));
    }
}

if(json_decode($result, true)['result'] == 'false') {
    webhook_add_industry_json_file($_GET['item']);
    print $result;
} else {
    print $result;
}