<?php namespace ApiAriary;


use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Event\BeforeEvent;
use GuzzleHttp\Event\CompleteEvent;
use GuzzleHttp\Event\ErrorEvent;
use GuzzleHttp\Event\SubscriberInterface;


/**
 * Class RequestEvent
 *
 * Event Subscriber for HTTP request
 *
 * @package ApiAriary
 */
class RequestEvent implements SubscriberInterface{

    protected $clientId;
    protected $clientSecret;
    protected $token;

    public function getEvents(){
        return [
            'before'   => ['onBefore', 100],
            'complete' => ['onComplete'],
            'error'    => ['onError']
        ];
    }

    public function onBefore(BeforeEvent $e, $name){
        //echo($e->getRequest());
    }

    public function onComplete(CompleteEvent $e, $name){
        //echo "\n";
    }

    public function onError(ErrorEvent $e, $name){
        //echo($e->getResponse());

        //Token Access Request
        if ($e->getResponse()->getStatusCode() == 401) {

            $http = new HttpClient();
            $storage = new TokenStorage(dirname(dirname(dirname(__FILE__))) . '/storage');

            $response = $http->post('http://api.ariary.dev/oauth/token', [
                'body' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                ]
            ]);

            $token = json_decode($response->getBody()->getContents(), true);

            $this->setTokenCookie($token);
            $storage->store($this->clientId, $token['access_token']);

            $e->getRequest()->setHeader('Authorization', 'Bearer ' . $token['access_token']);

            $newResponse = $e->getClient()->send($e->getRequest());
            $e->intercept($newResponse);
        }
    }

    public function setClientOptions($clientId, $clientSecret, $token){
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->token = $token;

        return $this;
    }

    public function setTokenCookie($token){
        $ck = base64_encode($this->clientSecret . json_encode($token));
        $_COOKIE['__toknAr'] = $ck;
        setcookie('__tokenAr', $ck, $token['expires_in']);
    }
}