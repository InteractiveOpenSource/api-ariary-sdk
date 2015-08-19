<?php

require_once dirname(dirname(__FILE__)) . '/vendor/autoload.php';

/*
$s = "UDExazFwSmxsS1VsYkZxU3IwUFpSZkZueXdUNEdFRVR7ImFjY2Vzc190b2tlbiI6IjIwNGU3ZTM3ODNhZjJiYjhmMDA3ZjRhMjUzOGU0YTc3MjRkNjIzY2IiLCJleHBpcmVzX2luIjozNjAwLCJ0b2tlbl90eXBlIjoiQmVhcmVyIiwic2NvcGUiOm51bGx9";
var_dump($s);
$s = base64_decode($s);
var_dump($s);
$s = str_replace('P11k1pJllKUlbFqSr0PZRfFnywT4GEET', '', $s);
var_dump($s);
$s = json_decode($s, true);
var_dump($s);
exit;
*/

error_reporting(1);

try{
    $api = new ApiAriary\Api('hjrJ2NMHjwLgMuiU', 'P11k1pJllKUlbFqSr0PZRfFnywT4GEET', 'irzhy.me');
    $res = $api->get('http://api.ariary.dev/latest/json?currency=USD,MUR', []);
    echo($res);
    echo "\n";
}catch (Exception $e){
    echo "\nCatched Error\n";
    echo "\n=============\n";
    echo $e->getMessage() . "\n";
}