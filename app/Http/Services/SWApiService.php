<?php


namespace App\Http\Services;


use http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SWApiService
{
    public function __construct()
    {

    }

    public function getFirstTenStarWarsCharacters()
    {
        $apiSWUrl = config('services.inbentasw.api_url');

        $body = [
            "query" => "{allPeople(first: 10) {people { name,}}}"
        ];

        return Http::get($apiSWUrl, $body);

    }

    public function getStarWarsFilms()
    {

    }
}
