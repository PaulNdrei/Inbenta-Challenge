<?php


namespace App\Http\Session;


class Session
{
    private $sessionToken;
    private $sessionExpiration;

    public function __construct($sessionToken, $sessionExpiration)
    {
        $this->sessionToken = $sessionToken;
        $this->sessionExpiration = $sessionExpiration;
    }

    public function getSessionToken()
    {
        return $this->sessionToken;
    }

    public function getSessionExpiration()
    {
        return $this->sessionExpiration;
    }

    public function __toString()
    {
        return "{sessionToken: ".$this->sessionToken.", sessionExpiration: ".$this->sessionExpiration."}";
    }


}
