<?php


namespace App\Http\Session;


use App\Http\Authentication\ChatBotApiAuthentication;
use App\Http\Authentication\ChatBotAuthCredentials;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ConversationSession
{
    private $apiConversationEndpoint;
    private $apiKey;
    private $authCredentials;

    public function __construct(ChatBotAuthCredentials $authCredentials)
    {
       $this->apiConversationEndpoint = config('services.inbenta.conversation_endpoint');
       $this->apiKey = config('services.inbenta.api_key');
       $this->authCredentials = $authCredentials;
    }

    public function createOrGetSession(): ?Session{
        Log::debug("CreateOrGetSession");

        if (!$this->isSessionValid()){
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
                $session = new Session($sessionToken, $sessionExpiration);
                SessionHandler::saveConversationSession($session);
                return $session;

            }

        }else{
            return SessionHandler::getSessionFromStorage();
        }

        return null;
    }

    /*Checks if the session stored in localstorage is not expired or exists*/
    public function isSessionValid(): bool
    {
        $hasValues = SessionHandler::hasConversationSessionValues();

        if (!$hasValues){
            Log::debug("isSessionValidMethod: Values null");
            return false;
        }
        $values = json_decode(SessionHandler::getSessionValues());

        if ($values->sessionExpiration < time()){
            Log::debug("isSessionValidMethod: Session expired");
            return false;
        }
        Log::debug("Conversation session is valid");
        return true;
    }

}
