<?php


namespace App\Http\Session;


use Illuminate\Support\Facades\Log;

class SessionHandler
{
    public static function getCredentialsValuesFromSession()
    {
        return json_encode(session()->get('chatbot.credentials'));
    }

    public static function getSessionFromStorage(): Session
    {
        $values = json_decode(SessionHandler::getSessionValues());
        return new Session($values->sessionToken, $values->sessionExpiration);
    }

    public static function getSessionValues()
    {
        $sessionValues = session()->get('chatbot.conversation');
        return is_null($sessionValues) ? null: json_encode($sessionValues);
    }

    public static function saveConversationSession(Session $session){
        session(['chatbot.conversation.sessionToken' => $session->getSessionToken(),
            'chatbot.conversation.sessionExpiration' => $session->getSessionExpiration()] );

        Log::debug("Session data saved");
    }

}
