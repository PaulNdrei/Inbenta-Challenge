<?php
namespace App\Http\Services;

use App\Http\ChatBotAuthApiClient;
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

    public function sendMessage(String $message){
        return "";
    }
    public function getHistory(){

        Log::debug("ChatBotApiService: Getting History ");
        if (!$this->isValidCredentials()){
            Log::debug("ChatBotApiService: Credentials not valid, trying to get new credentials");

            $this->chatBotAuthApiClient->auth();
        }

        Log::debug($this->getCredentialsData());
        
        return "test";

    }

    public function isValidCredentials(){
        if ($this->chatBotAuthApiClient->isExpired() || !$this->getCredentialsData()){
            return false;
        }
        return true;
    }
    public function getCredentialsData () {
        return session()->get('chatbot.credentials');
    }
}
