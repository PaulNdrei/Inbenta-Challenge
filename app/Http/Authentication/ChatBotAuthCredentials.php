<?php


namespace App\Http\Authentication;

use Illuminate\Support\Facades\Log;

class ChatBotAuthCredentials
{
    private $accessToken;
    private $chatBotApiUrl;
    private $expiration;

    public function __construct($accessToken, $chatBotApiUrl, $expiration)
    {
        $this->accessToken = $accessToken;
        $this->chatBotApiUrl = $chatBotApiUrl;
        $this->expiration = $expiration;

    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function getChatBotApiUrl(): ?string
    {
        return $this->chatBotApiUrl;
    }

    public function getExpiration(): ?int
    {
        return $this->expiration;
    }


    public function __toString(): ?string
    {
        return "{".$this->accessToken.", ".$this->chatBotApiUrl.", ".$this->expiration."}";
    }


}
