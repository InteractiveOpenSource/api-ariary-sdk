<?php namespace ApiAriary;


class TokenStorage {

    protected $storage;
    protected $filename;
    protected $key = 'ChangeThatKey';

    public function __construct($storage = null){

        $this->storage = !is_null($storage) ? $storage : '';

        if(!is_writable($this->storage))
            throw new \ErrorException('Storage is not writable');

    }

    public function store($clientId, $tokenValue){
        $clientId = md5($clientId);
        $handle = fopen($this->storage . "/$clientId", "w");
        fwrite($handle, $this->hash($tokenValue));
        fclose($handle);
    }

    public function retrieve($clientId, $default = null){
        $clientId = md5($clientId);
        $filename = $this->storage . "/$clientId";
        if(!is_file($filename))
            return $default;

        $content = trim(file_get_contents($filename));
        return $this->unHash($content);
    }

    public function hash($string){
        return base64_encode($this->key . $string);
    }

    public function unHash($string){
        $decoded = base64_decode($string);
        return str_replace($this->key, '', $decoded);
    }

} 