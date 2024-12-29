<?php

namespace App\Http\ApiClient;

use Http;

class AresApi
{
    public function findIcInAres(string $ic)
    {
        return Http::get("https://ares.gov.cz/ekonomicke-subjekty-v-be/rest/ekonomicke-subjekty/$ic")->json();
    }
}
