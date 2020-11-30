<?php
namespace App\Http\Services;

use App\Http\Authentication\ChatBotApiAuthentication;
use App\Http\Session\ConversationSession;
use App\Http\Session\SessionHandler;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatBotApiService
{
    public function __construct(){}

    public function sendMessageAndGetAnswer(String $message){

        $chatBotApiAuthentication = new ChatBotApiAuthentication();
        $authCredentials = $chatBotApiAuthentication->createOrGetAuthCredentials();

        $conversationSession = new ConversationSession($authCredentials);
        $messageSession = $conversationSession->createOrGetSession();

        if ($authCredentials == null){
            return response()->json(['error' => 'Not posible to authenticate to Chat Bot Api'], 401);
        }

        if ($messageSession != null){

            $headers = ['x-inbenta-key' => $chatBotApiAuthentication->getApiKey(),
                'Authorization' => 'Bearer '.$authCredentials->getAccessToken(), 'x-inbenta-session' => 'Bearer '.$messageSession->getSessionToken()];

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
                $response = json_decode($response);
                return response()->json(['answer' => $response->answers[0]->message], 200);
            }
        }

        return response()->json(['error' => 'Not posible to get and answer.'], 400);

    }

    public function getHistory(){

        $messageSession = SessionHandler::checkIfSessionIsValidAndGet();

        if ($messageSession != null){
            $chatBotApiAuthentication = new ChatBotApiAuthentication();
            $authCredentials = $chatBotApiAuthentication->createOrGetAuthCredentials();
            $headers = ['x-inbenta-key' => $chatBotApiAuthentication->getApiKey(),
                'Authorization' => 'Bearer '.$authCredentials->getAccessToken(), 'x-inbenta-session' => 'Bearer '.$messageSession->getSessionToken()];


            $apiHistoryEndPoint = config('services.inbenta.conversation_history_endpoint');
            $urlRequest = $authCredentials->getChatBotApiUrl().''.$apiHistoryEndPoint;

            Log::debug("Trying to get history of session to Inbenta ChatBot API to: ".$urlRequest);

            $response = Http::withHeaders($headers)->get($urlRequest);

            Log::debug("Response from Inbenta Chat Bot API: ".$response);

            if ($response->ok()){
                return $response;
            }
        }

        return response()->json(['error' => 'Not history found for session token'], 404);

    }



}
