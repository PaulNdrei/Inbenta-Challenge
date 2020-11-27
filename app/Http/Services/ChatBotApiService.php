<?php
namespace App\Http\Services;

use App\Http\ChatBotAuthApiClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatBotApiService
{

    /**
     * ChatBotApiService constructor.
     */

    private $accessToken;
    private $chatBotApiUrl;

    private $chatBotAuthApiClient;

    public function __construct(ChatBotAuthApiClient $chatBotAuthApiClient)
    {
        $this->chatBotAuthApiClient = $chatBotAuthApiClient;
    }

    public function createConversationAndGetSessionToken(){
        if (!$this->isValidCredentials()){
            $this->chatBotAuthApiClient->createAuth();
        }

        $accessToken = session()->get("chatbot.credentials.accessToken");
        $chatBotApiUrl = session()->get("chatbot.credentials.chatBotApiUrl");
        $headers = ['x-inbenta-key' => $this->chatBotAuthApiClient->getApiKey(),
            'Authorization' => 'Bearer '.$accessToken];

        $response = Http::withHeaders($headers)->post($chatBotApiUrl);

        if ($response->ok()){
            $response = json_decode($response);
            $sessionToken = $response->sessionToken;

            session(['chatbot.credentials.sessionToken'], $sessionToken);
            /*Save session expire timestamp, according to Inbenta API session token expires after 30 minuts of inactivity */
            session(['chatbot.credentials.sessionExpiration', time() + 1800]);
            return $sessionToken;

        }

    }
    public function sendMessage(String $message){

        $accessToken = session()->get("chatbot.credentials.accessToken");
        $sessionToken = session()->get("chatbot.credentials.sessionToken");

        if (!$this->isValidConversationSession()){
            $sessionToken = $this->createConversationAndGetSessionToken();
        }

        $headers = ['x-inbenta-key' => $this->chatBotAuthApiClient->getApiKey(),
            'Authorization' => 'Bearer '.$accessToken, 'x-inbenta-session' => 'Bearer '.$sessionToken];

        $response = Http::withHeaders($headers)->post();



    }
    public function getHistory(){

        Log::debug("ChatBotApiService: Getting History ");
        if (!$this->isValidCredentials()){
            Log::debug("ChatBotApiService: Credentials not valid, trying to get new credentials");

            $this->chatBotAuthApiClient->createAuth();
        }

        Log::debug($this->getCredentialsData());

        return "test";

    }

    public function isValidCredentials(){
        return $this->chatBotAuthApiClient->isExpired() || !$this->getCredentialsData();
    }
    public function isValidConversationSession(){
        return session()->get('sessionExpiration') < time() && session()->get('sessionToken');
    }
    public function getCredentialsData () {
        return session()->get('chatbot.credentials');
    }
}
