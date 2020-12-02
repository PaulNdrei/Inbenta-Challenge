<?php


namespace App\Http\Authentication;

use Illuminate\Support\Facades\Log;

class IbentaAuthCredentials
{
    private $accessToken;
    private $chatBotApiUrl;
    private $expiration;

    public function __construct()
    {
    }

    public static function create() {
        $instance = new self();
        return $instance;
    }


    public function withAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
        return $this;

    }

    public function withChatBotApiUrl($chatBotApiUrl)
    {
        $this->chatBotApiUrl = $chatBotApiUrl;
        return $this;

    }

    public function withExpiration($expiration)
    {
        $this->expiration = $expiration;
        return $this;
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
