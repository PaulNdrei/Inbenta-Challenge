<?php

namespace App\Http\Authentication;

use App\Http\Session\SessionHandler;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;

class ChatBotApiAuthentication
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

    public function getApiKey()
    {
        return $this->apiKey;
    }

    public function createOrGetAuthCredentials(): ?ChatBotAuthCredentials
    {
        Log::debug("Authentication: Attemting to get or create new credentials...");

        $authCredentials = SessionHandler::checkIfCredentialsAreValidAndGet();

        if (is_null($authCredentials)){
            $authCredentials = $this->createAndGetAuthCredentialsFromKeyAndSecret();
        }

        return $authCredentials;
    }


    public function createAndGetAuthCredentialsFromKeyAndSecret(): ?ChatBotAuthCredentials
    {
        Log::debug("Authentication: Creating new credentials...");

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
            $authCredentials->saveCredentialsToSession();

            Log::debug("Authentication: New credentials created succesfully!");

            return $authCredentials;

        }
        return null;
    }


}
