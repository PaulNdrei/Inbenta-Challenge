<?php

namespace App\Http\Controllers;

use App\Http\Services\IbentaApiService;
use App\Http\Services\SWApiService;
use Illuminate\Http\Request;

class ChatBotApiController extends Controller
{
    private $ibentaApiService;
    private $swApiService;

    public function __construct(IbentaApiService $ibentaApiService, SWApiService $swApiService)
    {
        $this->ibentaApiService = $ibentaApiService;
        $this->swApiService = $swApiService;
    }

    public function sendMessage(Request $request){
        if ($request->has('message')){
            $message = $request->message;

            if (str_contains($message, config('messages.keywords.force'))){
                return $this->swApiService->getFirstSixStarWarsFilms();
            }
            return $this->ibentaApiService->sendMessageAndGetAnswer($message);
        }
        return response()->json(['error' => 'Message field is required'], 400);
    }

    public function getHistory(){
        return $this->ibentaApiService->getHistory();
    }


}
