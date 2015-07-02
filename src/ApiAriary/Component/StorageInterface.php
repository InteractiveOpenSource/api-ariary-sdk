<?php namespace ApiAriary\Component;


interface StorageInterface {

    public function __construct($key, $options = null);

    /**
     * Store the session token value for an unique client
     * identified by @clientId
     *
     * @param $clientId
     * @param $tokenValue
     * @return mixed
     */
    public function store($clientId, $tokenValue);

    /**
     * Retrieve the session token for an unique client
     * identified by @clientId
     *
     * @param $clientId
     * @param null $default
     * @return mixed
     */
    public function retrieve($clientId, $default = null);

} 