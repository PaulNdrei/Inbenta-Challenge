<?php

namespace App\Http\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SWApiService
{
    private $apiSWUrl;

    public function __construct()
    {
        $this->apiSWUrl = config('services.inbentasw.api_url');

    }

    public function getFirstTenStarWarsCharacters()
    {
        return $this->doSWGetRequest(["query" => "{allPeople(first: 10) {people { name,}}}"]);
    }

    public function getFirstSixStarWarsFilms()
    {
        $swResponse = $this->doSWGetRequest(["query" => "{allFilms(first: 8) {films { title,}}}"]);

        if ($swResponse->successful()){
            $swResponse = json_decode($swResponse);
            return response()->json(['answer' => config("messages.chat.force"), 'filmOptions' => $swResponse->data->allFilms->films], 200);
        }
        return response()->json(['error' => 'Not posible to get film options.'], 400);

    }

    public function doSWGetRequest($queryBody): ?Response
    {
        return Http::get($this->apiSWUrl, $queryBody);
    }
}
