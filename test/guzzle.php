<?php

require_once dirname(dirname(__FILE__)) . '/vendor/autoload.php';

$client = new GuzzleHttp\Client();
$res = $client->get('http://api.ariary.dev', ['auth' =>  ['user', 'pass']]);

print_r($res->getBody()->getContents());
