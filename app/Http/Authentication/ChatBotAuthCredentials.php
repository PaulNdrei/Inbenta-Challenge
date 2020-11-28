<?php


namespace App\Http\Authentication;



use Illuminate\Support\Facades\Log;

class ChatBotAuthCredentials
{
    private $accessToken;
    private $chatBotApiUrl;
    private $expiration;

    /**
     * ChatBotAuthCredentials constructor.
     * @param $accessToken
     * @param $chatBotApiUrl
     * @param $expiration
     */
    public function __construct($accessToken, $chatBotApiUrl, $expiration)
    {
        $this->accessToken = $accessToken;
        $this->chatBotApiUrl = $chatBotApiUrl;
        $this->expiration = $expiration;

    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }

    public function getChatBotApiUrl()
    {
        return $this->chatBotApiUrl;
    }

    public function getExpiration()
    {
        return $this->expiration;
    }

    public function saveCredentialsToSession(): void
    {
        Log::debug("Credentials saved to session. ".$this->expiration);

        session(['chatbot.credentials.accessToken' => $this->accessToken, 'chatbot.credentials.chatBotApiUrl' => $this->chatBotApiUrl,
            'chatbot.credentials.expiration' => $this->expiration]);
    }

    public function isCredentialsExpired(){

        return time() >= json_decode($this->getValuesFromSession())->expiration;
    }


}
