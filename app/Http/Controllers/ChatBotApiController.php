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
        return $this->chatBotApiService->sendMessageAPI($request->message);
    }

    public function getHistory(){
        return $this->chatBotApiService->getHistoryAPI();
    }


}
