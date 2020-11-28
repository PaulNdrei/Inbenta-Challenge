<?php


namespace App\Http\Session;


class Session
{
    private $sessionToken;
    private $sessionExpiration;

    /**
     * Session constructor.
     * @param $sessionToken
     * @param $sessionExpiration
     */
    public function __construct($sessionToken, $sessionExpiration)
    {
        $this->sessionToken = $sessionToken;
        $this->sessionExpiration = $sessionExpiration;
    }

    /**
     * @return mixed
     */
    public function getSessionToken()
    {
        return $this->sessionToken;
    }

    /**
     * @return mixed
     */
    public function getSessionExpiration()
    {
        return $this->sessionExpiration;
    }

    public function __toString()
    {
        return "{sessionToken: ".$this->sessionToken.", sessionExpiration: ".$this->sessionExpiration."}";
    }


}
