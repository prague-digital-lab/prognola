<?php

namespace App\Http\Controllers\Api\CRM;

use App\Http\Controllers\Controller;
use App\Models\TicketMessage;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GetTwilioCallRecording extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(TicketMessage $ticketMessage, Request $request)
    {
        $res = \Http::withBasicAuth(config('services.twilio.account_sid'), config('services.twilio.auth_token'))
            ->accept('audio/mpeg')
            ->get($ticketMessage->recording_url.'.mp3');

        return response()->stream(function () use ($res) {
            echo $res;
        }, Response::HTTP_OK, [
            'Content-Type' => 'audio/mpeg',
            'Content-Length', 12322424,
        ]);

    }
}
