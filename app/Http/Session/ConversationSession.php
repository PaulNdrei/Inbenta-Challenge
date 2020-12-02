<?php


namespace App\Http\Session;


use App\Http\Authentication\IbentaApiAuthenticationService;
use App\Http\Authentication\IbentaAuthCredentials;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ConversationSession
{
    private $apiConversationEndpoint;
    private $apiKey;
    private $authCredentials;

    public function __construct(IbentaAuthCredentials $authCredentials)
    {
       $this->apiConversationEndpoint = config('services.inbenta.conversation_endpoint');
       $this->apiKey = config('services.inbenta.api_key');
       $this->authCredentials = $authCredentials;
    }

    public function createOrGetSession(): ?Session
    {
        Log::debug("CreateOrGetSession");

        $messageSession = SessionHandler::checkIfSessionIsValidAndGet();

        if (is_null($messageSession)){
            Log::debug("Creating new chatbot conversation session... Not valid");

            $chatBotApiUrl = $this->authCredentials->getChatBotApiUrl();
            $accessToken = $this->authCredentials->getAccessToken();

            $headers = ['x-inbenta-key' => $this->apiKey,
                'Authorization' => 'Bearer '.$accessToken];

            $response = Http::withHeaders($headers)->post($chatBotApiUrl.''.$this->apiConversationEndpoint);

            Log::debug("Session post resquest response: ".$response);

            if ($response->ok()){
                $response = json_decode($response);
                $sessionToken = $response->sessionToken;
                //Save session expire timestamp, according to Inbenta API session token expires after 30 minuts of inactivity
                $sessionExpiration =  time() + 1800;

                $messageSession = new Session($sessionToken, $sessionExpiration);
                SessionHandler::saveConversationSession($messageSession);

            }
        }
        return $messageSession;

    }

}
