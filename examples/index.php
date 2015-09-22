<?php

require_once dirname(dirname(__FILE__)) . '/vendor/autoload.php';

error_reporting(1);

try{
    $api = new ApiAriary\Api('YOUR_CLIENT_ID', 'YOUR_CLIENT_SECRET', 'yourdomain.com');
    $res = $api->get('latest/json?currency=USD,MUR', []);
    echo($res);
    echo "\n";
}catch (Exception $e){
    echo "\nCatched Error\n";
    echo "\n=============\n";
    echo $e->getMessage() . "\n";
    echo $e->getCode() . "\n";
}