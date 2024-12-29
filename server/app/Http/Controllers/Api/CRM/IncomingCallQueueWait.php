<?php

namespace App\Http\Controllers\Api\CRM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Twilio\TwiML\VoiceResponse;

class IncomingCallQueueWait extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $response = new VoiceResponse;
        $response->play(asset('call/billinghurst.mp3'));

        echo $response;
    }
}
