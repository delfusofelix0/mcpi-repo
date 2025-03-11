<?php

namespace App\Console\Commands;

use App\Services\M360SmsService;
use Illuminate\Console\Command;

class TestSmsService extends Command
{
    protected $signature = 'sms:test {phone} {message?}';
    protected $description = 'Test the SMS service with a phone number';

    public function handle()
    {
        $phone = $this->argument('phone');
        $message = $this->argument('message') ?? 'This is a test message from Maryknoll College of Panabo Inc.';

        $this->info("Sending test SMS to {$phone}...");

        $smsService = new M360SmsService();
        $result = $smsService->send($phone, $message);

        if ($result) {
            $this->info('SMS sent successfully!');
        } else {
            $this->error('Failed to send SMS. Check the logs for more details.');
        }

        return $result ? Command::SUCCESS : Command::FAILURE;
    }
}
