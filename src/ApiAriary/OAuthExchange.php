<?php namespace ApiAriary;


use ApiAriary\Component\StorageInterface;
use GuzzleHttp\Event\BeforeEvent;
use GuzzleHttp\Event\CompleteEvent;
use GuzzleHttp\Event\ErrorEvent;
use GuzzleHttp\Event\EventInterface;
use GuzzleHttp\Event\SubscriberInterface;


/**
 * Class OAuthExchange
 *
 * Event Subscriber for HTTP request to deal with the
 * API Ariary OAuth System (AAOS)
 *
 * @package ApiAriary
 */
class OAuthExchange implements SubscriberInterface{

    protected $clientId;
    protected $clientSecret;
    protected $token;
    protected $storage;
    protected $errtime;
    private $errtimeReal = 0;

    public function getEvents(){
        return [
            'before'   => ['onBefore', 100],
            'complete' => ['onComplete'],
            'error'    => ['onError']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function onBefore(BeforeEvent $e, $name){
        //@todo : vérifie nullité des paramètres client (client_id, client_secret) => stop si null ou vide
        //echo($e->getRequest());
    }

    /**
     * {@inheritdoc}
     */
    public function onComplete(CompleteEvent $e, $name){
        //echo "\n";
    }

    /**
     * {@inheritdoc}
     */
    public function onError(ErrorEvent $e, $name){

        if(!is_object($e) || !is_object($e->getResponse()))
            throw new \ErrorException('Error System', 500);

        if($this->errtimeReal == $this->errtime){
            throw new \RuntimeException("Too much initiative. You've tried {$this->errtime} times to request but no success", 503);
        }

        //Intercept OAuth restriction : 401 OAuth Restriction, Unauthorized requaest
        if ($e->getResponse()->getStatusCode() == 401) {
            $this->errtimeReal++;
            //Token Access Request
            $this->performOAuth($e, [
                'grant_type' => 'client_credentials',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
            ]);

            //Intercept response
            $newResponse = $e->getClient()->send($e->getRequest());
            $e->intercept($newResponse);
        }
    }

    /**
    * Build url with sufix
    *
    * @param $e
    * @param $path string uri Sufix
    * @return string
    */
    protected function getUri($e, $path = null){
        $uri = sprintf("%s://%s",
            $e->getRequest()->getScheme(),
            $e->getRequest()->getHost()
        );
        return $uri . (is_null($path) ? '' : $path);
    }

    /**
     * Deal with OAuth mecanism / exchange user config to a token passport
     *
     * @param EventInterface $e
     * @param $input
     */
    protected function performOAuth(EventInterface $e, $input){
        //build request to api oauth
        $response = $e->getClient()->post($this->getUri($e, '/oauth/token'), [
            'body' => $input
        ]);

        //decode response to retrieve token array
        $token = json_decode($response->getBody()->getContents(), true);

        //store token
        $this->storage->store($this->clientId, $token['access_token']);

        //set token information to header
        $e->getRequest()->setHeader('Authorization', 'Bearer ' . $token['access_token']);
    }

    /**
     * Set client configurations
     *
     * @param StorageInterface $storage
     * @param $clientId
     * @param $clientSecret
     * @param $token
     * @param $errtime
     *
     * @return $this
     */
    public function setClientOptions(StorageInterface $storage, $clientId, $clientSecret, $token, $errtime = 5){
        $this->storage = $storage;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->token = $token;
        $this->errtime = $errtime;

        return $this;
    }
}