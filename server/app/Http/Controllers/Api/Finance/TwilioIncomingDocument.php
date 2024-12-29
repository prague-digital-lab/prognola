<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Scan;
use App\Models\Workspace;
use App\Notifications\ReceivedInvoice\DiscordExpenseUploadedByEmail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TwilioIncomingDocument extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        Log::debug('Processing new incoming email.');

        $sender_email = $this->parseSenderEmail($request->input('from'));
        $to_email = $this->parseSenderEmail($request->input('to'));

        $workspace = $this->parseWorkspace($to_email);

        if ((bool) $workspace->enable_email_inbox === false) {
            Log::debug('Workspace email inbox disabled. Do nothing.');

            return ['message' => 'Workspace email inbox disabled.'];
        }

        // Create received invoice draft
        $expense = new Expense;
        $expense->workspace_id = $workspace->id;
        $expense->uuid = Str::uuid();
        $expense->payment_status = Expense::PAYMENT_STATUS_DRAFT;
        $expense->received_at = Carbon::now();
        $expense->paid_at = Carbon::now();
        $expense->description = 'Výdaj z emailu '.$sender_email;
        $expense->internal_note = 'Přijato prostřednictvím emailu '.$sender_email;
        $expense->save();

        /** @var UploadedFile $file */
        foreach ($request->allFiles() as $file) {

            // Create scan
            if (App::isProduction()) {
                $path = '/prognola/production/scans';
            } else {
                $path = '/prognola/dev/scans';
            }

            $saved_path = Storage::disk('do')->putFile($path, $file);

            $scan = new Scan;
            $scan->uuid = Str::uuid();
            $scan->workspace_id = $workspace->id;
            $scan->title = $file->getClientOriginalName();
            $scan->file_path = $saved_path;
            $scan->is_processed = false;
            $scan->sender_email = $sender_email;
            $scan->expense_id = $expense->id;
            $scan->save();
        }

        // Discord notification
        if (App::environment() == 'production') {
            $route = '1156913288945348689';

        } else {
            $route = '1111963282018947092';
        }

        if ($workspace->discord_channel_id) {
            $workspace->notify(new DiscordExpenseUploadedByEmail($expense, $sender_email));
        }

        Log::debug('Email processed successfully.');

        return ['message' => 'New document received.'];
    }

    public function parseSenderEmail(string $sender_string): string
    {
        Log::debug('Sender from Twilio: '.$sender_string);
        preg_match('#<(.*?)>#', $sender_string, $sender);

        if (array_key_exists(1, $sender)) {
            return $sender[1];
        }

        return $sender_string;
    }

    private function parseWorkspace(string $email)
    {
        Log::debug("Parsing workspace from email $email.");

        $e = explode('@', $email);
        $e = explode('-', $e[0]);
        $hash = last($e);

        $url_slug = Str::before($email, $hash);
        $url_slug = Str::replaceEnd('-', '', $url_slug);

        Log::debug('Parsed hash: '.$hash);
        Log::debug('Parsed workspace URL slug: '.$url_slug);

        $workspace = Workspace::where('inbox_email_hash', $hash)
            ->where('url_slug', $url_slug)
            ->firstOrFail();

        if ($workspace !== null) {
            Log::debug('Workspace found: '.$workspace->name, [
                'workspace' => $workspace,
            ]);

            return $workspace;
        } else {
            Log::notice('Workspace not found. Aborting inbound email request.');
            abort(404);
        }
    }
}
