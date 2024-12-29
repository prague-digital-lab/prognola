<?php

namespace App\Http\Controllers\Api\CRM;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use App\Notifications\Ticket\DiscordGenericMessage;
use App\Notifications\Ticket\DiscordTicketUpdated;
use App\Services\CustomerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Twilio\Rest\Client;
use Twilio\TwiML\VoiceResponse;

class IncomingCallWebhook extends Controller
{
    public function __invoke(Request $request)
    {
        $sender_phone = $request->input('From');
        $customer = (new CustomerService)->findOrCreateByPhone($sender_phone);

        $ticket = new Ticket;
        $ticket->requester_customer_id = $customer->id;
        $ticket->name = 'Příchozí hovor z čísla '.$sender_phone;
        $ticket->status = Ticket::STATUS_OPEN;
        $ticket->save();

        $ticket_message = new TicketMessage;
        $ticket_message->ticket_id = $ticket->id;
        $ticket_message->sender_customer_id = $customer->id;
        $ticket_message->type = TicketMessage::TYPE_INCOMING_CALL;
        $ticket_message->twilio_call_sid = $request->input('CallSid');
        $ticket_message->twilio_call_status = $request->input('CallStatus');
        $ticket_message->message = "Příchozí hovor od $sender_phone.";
        $ticket_message->save();

        $discord_channel_id = app()->hasDebugModeEnabled() ? '1111963282018947092' : '1230566284345741478';
        $route = route('admin.tickets.show', $ticket);
        Notification::route('discord', $discord_channel_id)
            ->notify(new DiscordTicketUpdated($ticket, "Vytvořen ticket [#{$ticket->id} {$ticket->name}]($route)."));

        // Call first agent
        $first_agent_user = User::where('is_on_call', '=', true)
            ->orderBy('on_call_priority')
            ->first();

        if ($first_agent_user === null) {
            // Incoming call instructions
            $response = new VoiceResponse;

            $response->say('Dobrý den. Vítejte na infolince hry Valašská pevnost. Hovor může být nahráván.',
                ['voice' => 'Google.cs-CZ-Wavenet-A', 'language' => 'cs-CZ']);

            $response->say('Bohužel nyní není dostupný žádný člen týmu, který by mohl přijmout tento hovor. Zkuste to prosím později.',
                ['voice' => 'Google.cs-CZ-Wavenet-A', 'language' => 'cs-CZ']);

            $response->hangup();

            $msg = new TicketMessage;
            $msg->ticket_id = $ticket->id;
            $msg->message = 'Hovor byl odmítnut, protože nebyl nalezen žádný uživatel s aktivním příjmem infolinky.';
            $msg->type = TicketMessage::TYPE_INTERNAL_NOTE;
            $msg->save();

            return $response;
        }

        // Incoming call instructions
        $response = new VoiceResponse;

        $response->say('Dobrý den. Vítejte na infolince hry Valašská pevnost. Hovor může být nahráván.',
            ['voice' => 'Google.cs-CZ-Wavenet-A', 'language' => 'cs-CZ']);

        $response->say('Nyní se vás pokusíme spojit s členem našeho týmu.',
            ['voice' => 'Google.cs-CZ-Wavenet-A', 'language' => 'cs-CZ']);

        $response->enqueue('support', [
            'waitUrl' => route('twilio.call.wait'),
        ]);

        Notification::route('discord', $discord_channel_id)
            ->notify(new DiscordGenericMessage("Prozváním uživatele {$first_agent_user->getDiscordIdSignatureOrName()} na číslo {$first_agent_user->phone}."));

        $client = new Client(config('services.twilio.account_sid'), config('services.twilio.auth_token'));
        $call = $client->calls->create(
            $first_agent_user->phone,
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
        $msg->ticket_id = $ticket->id;
        $msg->twilio_call_sid = $call->sid;
        $msg->twilio_call_status = $call->status;
        $msg->message = "Přesměrování na $first_agent_user->phone.";
        $msg->sender_user_id = $first_agent_user->id;
        $msg->type = TicketMessage::TYPE_CONNECT_AGENT_TO_CALL;
        $msg->save();

        return $response;
    }
}
