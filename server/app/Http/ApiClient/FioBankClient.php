<?php

namespace App\Http\ApiClient;

use App\Models\BankAccount;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class FioBankClient
{
    public function getTransactions(BankAccount $bank_account, Carbon $date_from, Carbon $date_to)
    {
        $date_from = $date_from->format('Y-m-d');
        $date_to = $date_to->format('Y-m-d');

        $token = $bank_account->api_token;

        $response
            = Http::get("https://www.fio.cz/ib_api/rest/periods/{$token}/{$date_from}/{$date_to}/transactions.json");

        return json_decode($response);

    }
}
