<?php namespace ApiAriary;

use GuzzleHttp\Client as HttpClient;

/**
 * Class Client
 *
 * ApiAriary Client Object to perform HTTP request
 *
 * @package ApiAriary
 */
class Client {

    protected $clientId;

    protected $clientSecret;

    protected $domain;

    /**
     * Token value
     *
     * @var null
     */
    protected $token;
    protected $storage;
    protected $options = [];

    /**
     * HTTP Client
     *
     * @var HttpClient
     */
    private $http;

    public function __construct($client_id, $client_secret, $domain = null){

        $this->clientId = $client_id;
        $this->clientSecret = $client_secret;
        $this->domain = $domain;
        $this->storage = new TokenStorage(dirname(dirname(dirname(__FILE__))) . '/storage');

        $this->token = $this->getToken();

        $this->options = [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token
            ],
        ];

        $this->setClient(new HttpClient());
    }

    public function get($uri, $options = []){
        $options = array_merge($this->options, $options);
        return $this->http->get($uri, $options);
    }

    public function post($uri, $options = []){
        $options = array_merge($this->options, $options);
        return $this->http->post($uri, $options);
    }

    public function head($uri, $options){
        $options = array_merge($this->options, $options);
        return $this->http->head($uri, $options);
    }

    public function setClient(HttpClient $http){

        $sub = new RequestEvent();
        $sub->setClientOptions($this->clientId, $this->clientSecret, $this->getToken());

        $http->getEmitter()->attach($sub);

        $this->http = $http;
    }

    public function getToken(){
        return $this->storage->retrieve($this->clientId);
    }
} 