<?php

function webhook_add_industry_json_file($query) {
    $url = 'https://hooks.zapier.com/hooks/catch/18108931/2svviql/';

    $ch = curl_init();

    $query_url = $url . '?query=' . urlencode($query);

    curl_setopt($ch, CURLOPT_URL, $query_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        die("cURL error: " . curl_error($ch));
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