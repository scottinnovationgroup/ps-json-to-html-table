<?php

$input = json_decode($_POST['data']);

$output['min_price'] = $input[1][7];
$output['max_price'] = $input[1][8];
$output['start_date'] = $input[1][4];
$output['end_date'] = $input[1][5];

print json_encode($output);