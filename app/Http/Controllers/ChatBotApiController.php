<?php

namespace App\Http\Controllers;

use App\Http\Services\ChatBotApiService;
use Illuminate\Http\Request;

class ChatBotApiController extends Controller
{
    private $chatBotApiService;

    public function __construct(ChatBotApiService $chatBotApiService)
    {
        $this->chatBotApiService = $chatBotApiService;

    }

    public function sendMessage(Request $request){
        if ($request->has('message')){
            return $this->chatBotApiService->sendMessageAndGetAnswer($request->message);
        }
        return response()->json(['error' => 'Message field is required'], 400);

    }

    public function getHistory(){
        return $this->chatBotApiService->getHistory();
    }


}
