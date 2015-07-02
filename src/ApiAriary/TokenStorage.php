<?php namespace ApiAriary;


use ApiAriary\Component\StorageInterface;

/**
 * Class TokenStorage
 *
 * The native storage system used by ApiAriary Client Object (ACO)
 * to store an retrieve session token between the that client
 * and API Ariary OAuth System (AAOS)
 *
 * @package ApiAriary
 */
class TokenStorage implements StorageInterface{

    /**
     * Storage directory
     *
     * @var null|string
     */
    protected $storage;

    /**
     * Filename for unique client
     *
     * @var String
     */
    protected $filename;

    /**
     * Key to hash the session token
     *
     * @var String
     */
    protected $key;

    /**
     * {@inheritdoc}
     */
    public function store($clientId, $tokenValue){
        $clientId = md5($clientId);
        $handle = fopen($this->storage . "/$clientId", "w");
        fwrite($handle, $this->hash($tokenValue));
        fclose($handle);
    }

    /**
     * {@inheritdoc}
     */
    public function retrieve($clientId, $default = null){
        $clientId = md5($clientId);
        $filename = $this->storage . "/$clientId";
        if(!is_file($filename))
            return $default;

        $content = trim(file_get_contents($filename));
        return $this->unHash($content);
    }

    /**
     * Encode the string value of the session token
     *
     * @param $string
     * @return string
     */
    public function hash($string){
        return base64_encode($this->key . $string);
    }

    /**
     * Decode the string value of the session token
     *
     * @param $string
     * @return mixed
     */
    public function unHash($string){
        $decoded = base64_decode($string);
        return str_replace($this->key, '', $decoded);
    }

    public function __construct($key, $storage = null){

        $this->key = $key;

        $this->storage = !is_null($storage) ? $storage : '';

        if(!is_writable($this->storage))
            throw new \ErrorException('Storage is not writable');

    }

} 