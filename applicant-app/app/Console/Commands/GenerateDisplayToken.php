<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;

class GenerateDisplayToken extends Command
{
    protected $signature = 'display:token';
    protected $description = 'Generate an encrypted display token';

    public function handle()
    {
        $token = config('display.token');

        if (empty($token)) {
            $this->error('Display token not configured in .env file');
            return 1;
        }

        $encryptedToken = Crypt::encryptString($token);

        $this->info('Encrypted Display Token:');
        $this->line($encryptedToken);

        $this->info('Use this token in your display URL:');
        $this->line(route('display.index') . '?token=' . urlencode($encryptedToken));

        return 0;
    }
}
