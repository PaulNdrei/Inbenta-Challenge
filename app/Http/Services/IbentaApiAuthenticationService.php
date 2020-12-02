<?php

namespace App\Http\Services;

use App\Http\Authentication\IbentaAuthCredentials;
use App\Http\Session\SessionHandler;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;

class IbentaApiAuthenticationService
{

    private $authApiUrl;
    private $authEndpoint;
    private $authRefreshEndpoint;
    private $apiKey;
    private $secret;

    public function __construct()
    {
        $this->authApiUrl = config('services.inbenta-auth.url');
        $this->authEndpoint = config('services.inbenta-auth.auth_endpoint');
        $this->authRefreshEndpoint = config('services.inbenta-auth.refresh_token_endpoint');
        $this->apiKey = config('services.inbenta.api_key');
        $this->secret = config('services.inbenta.secret');
    }


    public function createOrGetAuthCredentials(): ?IbentaAuthCredentials
    {
        Log::debug("Authentication: Attemting to get or create new credentials...");

        $authCredentials = $this->refreshToken();

        if (is_null($authCredentials)){
            $authCredentials = $this->createAndGetAuthCredentialsFromKeyAndSecret();
        }

        return $authCredentials;
    }


    public function refreshToken (): ?IbentaAuthCredentials {
        Log::debug("Authentication: Refresh Token...");

        $savedCredentials = SessionHandler::checkIfCredentialsAreValidAndGet();

        if (!is_null($savedCredentials)){

            $headers = [
                'x-inbenta-key' => $this->getApiKey(),
                'Authorization' => 'Bearer '.$savedCredentials->getAccessToken(),
                'Content-Type' => 'application/json'
            ];

            $urlRequest = $this->authApiUrl.''.$this->authRefreshEndpoint;
            $response = Http::withHeaders($headers)->post($urlRequest);

            if ($response->successful()){

                $response = json_decode($response);

                $accessToken = $response->accessToken;
                $expiration = $response->expiration;

                $savedCredentials
                    ->withAccessToken($accessToken)
                    ->withExpiration($expiration);

                SessionHandler::saveCredentialsToSession($savedCredentials);

                Log::debug("Authentication: Token refreshed succesfully!");

                return $savedCredentials;
            }
        }
        return null;
    }

    public function createAndGetAuthCredentialsFromKeyAndSecret(): ?IbentaAuthCredentials
    {
        Log::debug("Authentication: Creating new credentials...");

        $headers = [
            'x-inbenta-key' => $this->getApiKey(),
            'Content-Type' => 'application/json'
        ];
        $body = [
            'secret' => $this->secret
        ];

        $urlRequest = $this->authApiUrl.''.$this->authEndpoint;
        $response = Http::withHeaders($headers)->post($urlRequest, $body);

        if ($response->ok()){
            $response = json_decode($response);

            $accessToken = $response->accessToken;
            $chatBotApiUrl = $response->apis->chatbot;
            $expiration = $response->expiration;

            $authCredentials = IbentaAuthCredentials::create()
                ->withAccessToken($accessToken)
                ->withChatBotApiUrl($chatBotApiUrl)
                ->withExpiration($expiration);

            SessionHandler::saveCredentialsToSession($authCredentials);

            Log::debug("Authentication: New credentials created succesfully!");

            return $authCredentials;

        }
        return null;
    }

    public function getApiKey()
    {
        return $this->apiKey;
    }



}
