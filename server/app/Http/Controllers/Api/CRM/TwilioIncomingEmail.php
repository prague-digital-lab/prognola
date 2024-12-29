<?php

namespace App\Http\Controllers\Api\CRM;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Notifications\Ticket\DiscordGenericMessage;
use App\Services\CustomerService;
use App\Services\TicketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class TwilioIncomingEmail extends Controller
{
    public function __invoke(Request $request)
    {
        $sender_email = $this->parseSenderEmail($request->input('from'))['email'];

        $customer = (new CustomerService)->findOrCreateByEmail($sender_email);

        $ticket = (new TicketService)->findByEmailSubject($customer, $request->input('subject'));

        if ($ticket === null) {
            $ticket = new Ticket;
            $ticket->requester_customer_id = $customer->id;
            $ticket->name = $request->input('subject');
            $ticket->status = Ticket::STATUS_OPEN;
            $ticket->save();

            $discord_channel_id = app()->hasDebugModeEnabled() ? '1111963282018947092' : '1230566284345741478';
            Notification::route('discord', $discord_channel_id)
                ->notify(new DiscordGenericMessage('Zákazník vytvořil ticket '.$ticket->getDiscordLink().'.'));
        } else {
            $ticket->status = Ticket::STATUS_OPEN;
            $ticket->save();

            $discord_channel_id = app()->hasDebugModeEnabled() ? '1111963282018947092' : '1230566284345741478';
            Notification::route('discord', $discord_channel_id)
                ->notify(new DiscordGenericMessage('Zákazník odpověděl na ticket '.$ticket->getDiscordLink().'.'));
        }

        $message = new TicketMessage;
        $message->ticket_id = $ticket->id;
        $message->sender_customer_id = $customer->id;
        $message->type = TicketMessage::TYPE_INCOMING_EMAIL;
        $message->message = mb_convert_encoding($request->input('text'), 'UTF-8');
        $message->sender_email = $request->input('from');
        $message->save();

        return ['message' => 'Received.'];
    }

    public function parseSenderEmail(string $sender_string)
    {
        preg_match("#\"([\w\s]+)\"#", $sender_string, $matches_1);
        $name = @$matches_1[1];

        preg_match('/[^0-9<][A-z0-9_]+([.][A-z0-9_]+)*[@][A-z0-9_]+([.][A-z0-9_]+)*[.][A-z]{2,4}/i', $sender_string, $matches_2);
        $email = @$matches_2[0];

        return [
            'name' => $name,
            'email' => $email,
        ];
    }
}
