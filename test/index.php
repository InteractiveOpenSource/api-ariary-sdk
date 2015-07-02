<?php

require_once dirname(dirname(__FILE__)) . '/vendor/autoload.php';

if(!session_id())
    session_start();

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

error_reporting(0);

$api = new ApiAriary\Client('hjrJ2NMHjwLgMuiU', 'P11k1pJllKUlbFqSr0PZRfFnywT4GEET', 'irzhy.me');
//$res = $api->get('http://api.ariary.dev/graph/json?currency=USD&frequency=m&order=desc', []);
$res = $api->get('http://api.ariary.dev/latest/json?currency=USD,HKD,MUR', []);
try{
    print_r($res->getBody()->getContents()) . "\n";
}catch (Exception $e){
    echo "\nCatched Error\n";
    echo "\n=============\n";
    echo $e->getMessage() . "\n";
}