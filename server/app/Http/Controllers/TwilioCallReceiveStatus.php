<?php

namespace App\Http\Controllers;

use App\Models\TicketMessage;
use App\Models\User;
use App\Notifications\Ticket\DiscordGenericMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Twilio\Rest\Client;

class TwilioCallReceiveStatus extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $discord_channel_id = '1111963282018947092';
        Notification::route('discord', $discord_channel_id)
            ->notify(new DiscordGenericMessage("Změna stavu hovoru {$request->input('CallSid')}: {$request->input('CallStatus')}"));

        $ticket_message = TicketMessage::where('twilio_call_sid', $request->input('CallSid'))->firstOrFail();

        $ticket_message->twilio_call_status = $request->input('CallStatus');

        if ($request->has('RecordingUrl')) {
            $ticket_message->recording_url = $request->input('RecordingUrl');
        }

        $ticket_message->save();

        // Attempt to call another agent if the previous one failed.
        if ($ticket_message->type == TicketMessage::TYPE_CONNECT_AGENT_TO_CALL) {
            $next_agent_user = User::where('is_on_call', '=', true)
                ->whereNotIn('id', $ticket_message->ticket->getAlreadyConnectedUserIds())
                ->orderBy('on_call_priority')
                ->first();

            if ($next_agent_user === null) {
                Notification::route('discord', $discord_channel_id)
                    ->notify(new DiscordGenericMessage('Žádný uživatel nebyl dostupný.'));

                return;
            }

            Notification::route('discord', $discord_channel_id)
                ->notify(new DiscordGenericMessage("Prozváním uživatele {$next_agent_user->getDiscordIdSignatureOrName()} na číslo {$next_agent_user->phone}."));

            $client = new Client(config('services.twilio.account_sid'), config('services.twilio.auth_token'));
            $call = $client->calls->create(
                $next_agent_user->phone,
                '+420570000057',
                [
                    'timeout' => 30,
                    'record' => true,
                    'statusCallback' => route('twilio.call.receive-call-status'),
                    'statusCallbackMethod' => 'POST',
                    'url' => route('twilio.call.connect-agent'),
                ]
            );

            $msg = new TicketMessage;
            $msg->ticket_id = $ticket_message->ticket->id;
            $msg->twilio_call_sid = $call->sid;
            $msg->twilio_call_status = $call->status;
            $msg->message = "Přesměrování na $next_agent_user->phone.";
            $msg->sender_user_id = $next_agent_user->id;
            $msg->type = TicketMessage::TYPE_CONNECT_AGENT_TO_CALL;
            $msg->save();
        }
    }
}
