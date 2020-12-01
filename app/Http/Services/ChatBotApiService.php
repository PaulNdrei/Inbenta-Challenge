<?php
namespace App\Http\Services;

use App\Http\Authentication\ChatBotApiAuthentication;
use App\Http\Session\ConversationSession;
use App\Http\Session\SessionHandler;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatBotApiService
{
    private $swApiService;

    public function __construct(SWApiService $swApiService){
        $this->swApiService = $swApiService;
    }

    public function sendMessageAndGetAnswer(String $message)
    {

        $chatBotApiAuthentication = new ChatBotApiAuthentication();
        $authCredentials = $chatBotApiAuthentication->createOrGetAuthCredentials();

        $conversationSession = new ConversationSession($authCredentials);
        $messageSession = $conversationSession->createOrGetSession();

        if ($authCredentials == null) {
            return response()->json(['error' => 'Not posible to authenticate to Chat Bot Api'], 401);
        }

        if ($messageSession != null) {

            $headers = ['x-inbenta-key' => $chatBotApiAuthentication->getApiKey(),
                'Authorization' => 'Bearer ' . $authCredentials->getAccessToken(), 'x-inbenta-session' => 'Bearer ' . $messageSession->getSessionToken()];

            $body = [
                'message' => $message
            ];

            Log::debug("Message: " . $message);


            $apiMessageEndPoint = config('services.inbenta.conversation_message_endpoint');
            $urlRequest = $authCredentials->getChatBotApiUrl() . '' . $apiMessageEndPoint;

            Log::debug("Trying to send message to Inbenta ChatBot API to: " . $urlRequest);


            $response = Http::withHeaders($headers)->post($urlRequest, $body);

            Log::debug("Response from Inbenta Chat Bot API: " . $response);


            if ($response->successful()) {

                $response = json_decode($response);
                $flags = $response->answers[0]->flags;

                $dataReturnResponse = ['answer' => $response->answers[0]->message];

                $notFound = $this->arrayKeyExists("no-results", $flags);
                if ($notFound) {
                    $lastFound = SessionHandler::getLastFound();
                    if (!$lastFound) {
                        $swApiService = new SWApiService();
                        $swResponse = $swApiService->getFirstTenStarWarsCharacters();
                        if ($swResponse->successful()) {
                            $swResponse = json_decode($swResponse);
                            SessionHandler::setLastFound(true);
                            $notFoundMessage = config("messages.chat.notfound");
                            $dataReturnResponse = ['answer' => $notFoundMessage, 'notFoundOptions' => $swResponse->data->allPeople->people];

                            return response()->json($dataReturnResponse, 200);
                        }
                    }
                    SessionHandler::setLastFound(false);
                }else{
                    SessionHandler::setLastFound(true);
                }
                return response()->json($dataReturnResponse, 200);
            }
            return response()->json(['error' => 'Not posible to get and answer.'], 400);
        }
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

            if ($response->successful()){
                return $response;
            }
        }

        return response()->json(['error' => 'Not history found for session token'], 404);

    }

    public function getSWFilms(){
        $swResponse = $this->swApiService->getFirstSixStarWarsFilms();

        if ($swResponse->successful()){
            $swResponse = json_decode($swResponse);
            return response()->json(['answer' => config("messages.chat.force"), 'filmOptions' => $swResponse->data->allFilms->films], 200);
        }
        return response()->json(['error' => 'Not posible to get film options.'], 400);

    }


    public function arrayKeyExists(String $key, $arrayResponse){
        for ($i = 0; $i<count($arrayResponse); $i++){
            if ($arrayResponse[$i] == $key){
                return true;
            }
        }
        return false;
    }





}
