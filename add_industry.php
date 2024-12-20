<?php

if($_POST['source'] !== 'Zapier') {
    exit("Invalid source.");
}

$json_file_path = 'https://portfoliostack-s3.s3.us-east-1.amazonaws.com/all_industry.json';

$current_json = file_get_contents($json_file_path);
$current_array = json_decode($current_json, true);
$json_to_add = $_POST['json'];
$object_to_add = json_decode($json_to_add, true);
$current_array[] = $object_to_add;

$updated_json = json_encode($current_array, JSON_PRETTY_PRINT);

print $updated_json;