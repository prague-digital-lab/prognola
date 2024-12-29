<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateTokenForUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:token {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $user = User::where('email', $this->argument('email'))->firstOrFail();

        $token = $user->createToken('api_token_'.rand(10000, 99999));

        $this->info('Token: '.$token->plainTextToken);
    }
}
