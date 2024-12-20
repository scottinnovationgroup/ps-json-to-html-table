<?php

$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

$allowed_origins = [
    'https://www.portfoliostack.com'
];

if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
}

if(!$_GET['item']) {
    exit("Invalid item.");
}

function webhook_add_industry_json_file($query) {
    if(!$query) {
        exit("No query");
    }
    
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

$json_file_path = 'https://portfoliostack-s3.s3.us-east-1.amazonaws.com/all_industry.json';

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
    if($_GET['check_only'] !== 'true') {
        webhook_add_industry_json_file($_GET['item']);
    }
    print $result;
} else {
    print $result;
}