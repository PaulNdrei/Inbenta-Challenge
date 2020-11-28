<?php

namespace App\Http\Authentication;

use App\Http\Session\SessionHandler;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;

class ChatBotApiAuthentication
{

    private $authCredentials;
    private $authUrl;
    private $apiKey;
    private $secret;

    public function __construct()
    {
        $this->authUrl = config('services.inbenta.auth_url');
        $this->apiKey = config('services.inbenta.api_key');
        $this->secret = config('services.inbenta.secret');
    }


    public function createOrGetAuthCredentials(): ?ChatBotAuthCredentials
    {
        Log::debug("createOrGetAuthCredentials()");

        $authCredentials = $this->getCredentialsObjectFromSession();
        Log::debug($authCredentials);

        if (is_null($authCredentials)){
            Log::debug("Create new credentials... IS NULL");
            $authCredentials = $this->createAndGetAuthCredentialsFromKeyAndSecret();
        }

        return $authCredentials;
    }

    public function getCredentialsObjectFromSession():?ChatBotAuthCredentials
    {
        $authCredentials = json_decode(SessionHandler::getCredentialsValuesFromSession());
        if ($authCredentials) return new ChatBotAuthCredentials($authCredentials->accessToken, $authCredentials->chatBotApiUrl, $authCredentials->expiration);

        return null;

    }



    public function getApiKey()
    {
        return $this->apiKey;
    }


    public function createAndGetAuthCredentialsFromKeyAndSecret(): ?ChatBotAuthCredentials
    {

        Log::debug("Apikey: ".$this->getApiKey());
        Log::debug("Secret: ".$this->secret);
        Log::debug("Authurl: ".$this->authUrl);

        $headers = [
            'x-inbenta-key' => $this->getApiKey(),
            'Content-Type' => 'application/json'
        ];
        $body = [
            'secret' => $this->secret
        ];

        $response = Http::withHeaders($headers)->post($this->authUrl, $body);

        if ($response->ok()){
            $response = json_decode($response);

            $accessToken = $response->accessToken;
            $chatBotApiUrl = $response->apis->chatbot;
            $expiration = $response->expiration;

            $authCredentials = new ChatBotAuthCredentials($accessToken, $chatBotApiUrl, $expiration);


            Log::debug("Auth succesfully");

            return $authCredentials;

        }
        $response->throw();

        return null;
    }

}