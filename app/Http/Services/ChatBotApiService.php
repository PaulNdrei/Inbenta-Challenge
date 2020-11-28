<?php
namespace App\Http\Services;

use App\Http\Authentication\ChatBotApiAuthentication;
use App\Http\Session\ConversationSession;
use App\Http\Session\SessionHandler;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatBotApiService
{

    /**
     * ChatBotApiService constructor.
     */


    public function __construct()
    {

    }

    public function sendMessageAndGetAnswer(String $message){
        $session = null;

        $chatBotApiAuthentication = new ChatBotApiAuthentication();
        $authCredentials = $chatBotApiAuthentication->createOrGetAuthCredentials();

        $conversationSession = new ConversationSession($authCredentials);
        $messageSession = $conversationSession->createOrGetSession();

        if ($messageSession != null){

            $headers = ['x-inbenta-key' => $chatBotApiAuthentication->getApiKey(),
                'Authorization' => 'Bearer '.$authCredentials->getAccessToken(), 'x-inbenta-session' => 'Bearer '.$messageSession->getSessionToken()];

            Log::debug("x-inbenta-key: ".$chatBotApiAuthentication->getApiKey());
            Log::debug("Authorization: ".$authCredentials->getAccessToken());
            Log::debug("x-inbenta-session: ".$messageSession->getSessionToken());


            $body = [
                'message' => $message
            ];

            Log::debug("Message: ".$message);


            $apiMessageEndPoint = config('services.inbenta.conversation_message_endpoint');
            $urlRequest = $authCredentials->getChatBotApiUrl().''.$apiMessageEndPoint;

            Log::debug("Trying to send message to Inbenta ChatBot API to: ".$urlRequest);


            $response = Http::withHeaders($headers)->post($urlRequest, $body);

            Log::debug("Response from Inbenta Chat Bot API: ".$response);

            if ($response->ok()){
                //$response = json_decode($response);

                return $response;
            }
            return "error";
        }

        return "error2";

    }

    public function getHistory(){

        return "test";

    }



}
