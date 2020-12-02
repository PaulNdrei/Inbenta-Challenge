<?php
namespace App\Http\Services;

use App\Http\Authentication\IbentaAuthCredentials;
use App\Http\Session\ConversationSession;
use App\Http\Session\SessionHandler;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IbentaApiService
{
    private $swApiService;
    private $ibentaApiAuthenticationService;

    public function __construct(IbentaApiAuthenticationService $ibentaApiAuthenticationService, SWApiService $swApiService){
        $this->ibentaApiAuthenticationService = $ibentaApiAuthenticationService;
        $this->swApiService = $swApiService;
    }

    public function sendMessageAndGetAnswer(String $message)
    {
        $authCredentials = $this->getInbentaAuthCredentials();

        if ($authCredentials == null) {
            return response()->json(['error' => 'Not posible to authenticate to Chat Bot Api'], 401);
        }

        $conversationSession = new ConversationSession($authCredentials);
        $messageSession = $conversationSession->createOrGetSession();

        if ($messageSession != null) {

            $headers = ['x-inbenta-key' => $this->ibentaApiAuthenticationService->getApiKey(),
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
                        $swResponse = $this->swApiService->getFirstTenStarWarsCharacters();
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

            $authCredentials = $this->getInbentaAuthCredentials();
            $headers = ['x-inbenta-key' => $this->ibentaApiAuthenticationService->getApiKey(),
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



    public function getInbentaAuthCredentials (): ?IbentaAuthCredentials
    {
        return $this->ibentaApiAuthenticationService->createOrGetAuthCredentials();
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
