<?php

function remove_after_symbol($text) {

    $pattern[] = '/```【.*/';
    $pattern[] = '(```)';

    $result = preg_replace($pattern, '', $text);

    return $result;
}

$result = remove_after_symbol($_POST);

print $result['text'];
