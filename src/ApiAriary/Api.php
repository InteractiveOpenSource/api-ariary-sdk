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

    const API_URL = 'http://api.ariary.dev/';

    /**
     * API Ariary client_id
     * @var string
     */
    protected $clientId;

    /**
     * API Ariary client_secret
     * @var string
     */
    protected $clientSecret;

    /**
     * API Ariary authorized domain
     * @var null
     */
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
    protected $httpOptions = [];

    /**
     * HTTP Client
     *
     * @var HttpClient
     */
    private $http;

    /**
     * API Options
     *
     * @var array
     */
    protected $options = [];

    public function __construct($client_id, $client_secret, $domain = null, $option = array()){

        $this->clientId = $client_id;
        $this->clientSecret = $client_secret;
        $this->domain = $domain;

        $option = array_merge(array(
            'storage' => dirname(dirname(dirname(__FILE__))) . '/storage',
            'errtime' => 5,
        ), $option);

        $this->options = $option;

        //Set storage Dependancy
        $this->setStorage(
            new TokenStorage($client_secret, $option['storage'])
        );

        //retrieve session token
        $this->token = $this->getToken();

        //default client options
        $this->httpOptions = [
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
        return $this->http->get(self::API_URL . $uri, array_merge($this->httpOptions,
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
        return $this->http->post(self::API_URL . $uri, array_merge($this->httpOptions,
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
        return $this->http->head(self::API_URL . $uri, array_merge($this->httpOptions,
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
        $oauth->setClientOptions(
            $this->storage,
            $this->clientId,
            $this->clientSecret,
            $this->getToken(),
            $this->getOption('errtime', 5)
        );

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

    /**
     * Retrieve option by key
     *
     * @param $key
     * @param null $default
     * @return null
     */
    public function getOption($key, $default = null){
        return array_key_exists($key, $this->options) ? $this->options[$key] : $default;
    }
} 