<?php

$input = json_decode($_POST['data']);

$output['min_price'] = '$'.number_format($input[1][8], 0, '.', ',');;
$output['max_price'] = '$'.number_format($input[1][9], 0, '.', ',');;
$output['start_date'] = $input[1][4];
$output['end_date'] = $input[1][5];

print json_encode($output);