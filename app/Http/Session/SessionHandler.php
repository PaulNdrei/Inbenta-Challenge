<?php


namespace App\Http\Session;


use App\Http\Authentication\IbentaAuthCredentials;
use Exception;
use Illuminate\Support\Facades\Log;
use phpDocumentor\Reflection\Types\Boolean;

class SessionHandler
{

    public static function checkIfSessionIsValidAndGet(): ?Session
    {
        $values = json_encode(session()->get('chatbot.conversation'));

        try{
            $values = json_decode($values);

            if ($values->sessionExpiration < time()){
                Log::debug("Conversation session check: Session expired");
                return null;
            }
            Log::debug("Conversation session check: Session is valid");
            return new Session($values->sessionToken, $values->sessionExpiration);
        }catch (Exception $e){
            Log::debug("Conversation session check: Not Valid or Null");
            return null;
        }

    }

    public static function checkIfCredentialsAreValidAndGet(): ?IbentaAuthCredentials
    {
        $values = json_encode(session()->get('chatbot.credentials'));

        try {
            $values = json_decode($values);

            if ($values->expiration < time()){
                Log::debug("Check Credentials: Credentials expired");
                return null;
            }
            Log::debug("Check Credentials: Valid");

            return IbentaAuthCredentials::create()
                ->withAccessToken($values->accessToken)
                ->withChatBotApiUrl($values->chatBotApiUrl)
                ->withExpiration($values->expiration);

        }catch (Exception $e){

            Log::debug("Check Credentials: Not Valid or Null");
            return null;
        }

    }


    public static function saveCredentialsToSession(IbentaAuthCredentials $authCredentials): void
    {

        session(['chatbot.credentials.accessToken' => $authCredentials->getAccessToken(), 'chatbot.credentials.chatBotApiUrl' => $authCredentials->getChatBotApiUrl(),
            'chatbot.credentials.expiration' => $authCredentials->getExpiration()]);

        Log::debug("Credentials saved to session. ");

    }

    public static function saveConversationSession(Session $session): void
    {
        session(['chatbot.conversation.sessionToken' => $session->getSessionToken(),
            'chatbot.conversation.sessionExpiration' => $session->getSessionExpiration()] );

        Log::debug("Session data saved");
    }

    public static function setLastFound(bool $lastFound)
    {
        session()->put('chatbot.lastFound', $lastFound);
    }

    public static function getLastFound()
    {
        if (session()->has('chatbot.lastFound')){
            return session()->get('chatbot.lastFound');
        }
        return true;
    }

}
