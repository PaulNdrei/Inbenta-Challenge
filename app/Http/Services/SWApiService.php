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
        return $this->doSWGetRequest(["query" => "{allFilms(first: 8) {films { title,}}}"]);
    }

    public function doSWGetRequest($queryBody): ?Response
    {
        return Http::get($this->apiSWUrl, $queryBody);
    }
}
