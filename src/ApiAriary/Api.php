<?php namespace ApiAriary;


use GuzzleHttp\Client as HttpClient;
use ApiAriary\Component\StorageInterface as Storage;

/**
 * Class Api
 *
 * Represents ApiAriary Client Object (ACO), object which perform
 * HTTP request. It performs the main way to deal with the API Ariary
 *
 * @package ApiAriary
 * @author Irzhy Ranaivoarivony
 * @email contact@irzhy.me
 */
class Api {

    protected $clientId;

    protected $clientSecret;

    protected $domain;

    /**
     * Token value
     *
     * @var null
     */
    protected $token;


    /**
     * Storage for session token
     *
     * @var Storage
     */
    protected $storage;

    /**
     * Default HttpClient options
     *
     * @var array
     */
    protected $options = [];

    /**
     * HTTP Client
     *
     * @var HttpClient
     */
    private $http;

    public function __construct($client_id, $client_secret, $domain = null, $option = array()){

        $this->clientId = $client_id;
        $this->clientSecret = $client_secret;
        $this->domain = $domain;

        $option = array_merge(array(
            'storage' => dirname(dirname(dirname(__FILE__))) . '/storage',
        ), $option);

        //Set storage Dependancy
        $this->setStorage(
            new TokenStorage($client_secret, $option['storage'])
        );

        //retrieve session token
        $this->token = $this->getToken();

        //default client options
        $this->options = [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token
            ],
        ];

        $this->setClient(new HttpClient());
    }

    /**
     * Perform a GET request
     *
     * @param $uri
     * @param array $options
     * @return \GuzzleHttp\Message\ResponseInterface
     */
    public function get($uri, $options = []){
        return $this->http->get($uri, array_merge($this->options,
            $options
        ));
    }

    /**
     * Send a POST request
     *
     * @param $uri
     * @param array $options
     * @return \GuzzleHttp\Message\ResponseInterface
     */
    public function post($uri, $options = []){
        return $this->http->post($uri, array_merge($this->options,
            $options
        ));
    }

    /**
     * Execute an HEAD request
     *
     * @param $uri
     * @param $options
     * @return \GuzzleHttp\Message\ResponseInterface
     */
    public function head($uri, $options){
        return $this->http->head($uri, array_merge($this->options,
            $options
        ));
    }

    /**
     * Set the HTTP client to perform all request
     *
     * @param HttpClient $http
     */
    public function setClient(HttpClient $http){

        $oauth = new OAuthExchange();
        $oauth->setClientOptions($this->storage, $this->clientId, $this->clientSecret, $this->getToken());

        $http->getEmitter()->attach($oauth);

        $this->http = $http;
    }

    /**
     * Set storage component
     *
     * @param Storage $storage
     */
    public function setStorage(Storage $storage){
        $this->storage = $storage;
    }

    /**
     * Retrieve session token
     *
     * @return mixed
     */
    public function getToken(){
        return $this->storage->retrieve($this->clientId);
    }
} 