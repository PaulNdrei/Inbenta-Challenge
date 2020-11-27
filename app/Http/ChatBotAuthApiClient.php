<?php

namespace App\Http;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

class ChatBotAuthApiClient
{

    private $authUrl;
    private $apiKey;
    private $secret;

    public function __construct()
    {
        $this->authUrl = config('services.inbenta.auth_url');
        $this->apiKey = config('services.inbenta.api_key');
        $this->secret = config('services.inbenta.secret');
    }

    public function auth(){

        $headers = [
            'x-inbenta-key' => $this->apiKey,
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


            session(['chatbot.credentials.accessToken' => $accessToken, 'chatbot.credentials.chatBotApiUrl' => $chatBotApiUrl,
                'chatbot.credentials.expiration' => $expiration]);

            return true;

        }
        $response->throw();

        return false;
    }

    public function isExpired(){
        $expiration = session()->get('expirationToken');

        if (time() >= $expiration){
            return true;
        }
        return false;
    }


}
