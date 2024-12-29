<?php

namespace App\Http\Controllers\Api\CRM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Twilio\TwiML\VoiceResponse;

class ConnectAgent extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $response = new VoiceResponse;

        $response->say('Příchozí hovor od zákazníka . Nyní budete spojeni.',
            ['voice' => 'Google.cs-CZ-Wavenet-A', 'language' => 'cs-CZ']);

        $dial = $response->dial();
        $dial->queue('support');

        echo $response;
    }
}
