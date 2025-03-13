<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class M360SmsService
{
    protected $apiKey;
    protected $apiSecret;
    protected $senderId;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.m360.api_key');
        $this->apiSecret = config('services.m360.api_secret');
        $this->senderId = config('services.m360.sender_id');
        $this->baseUrl = config('services.m360.base_url');
    }

    public function send($phoneNumber, $message)
    {
        try {
            // Format phone number if needed
            $phoneNumber = $this->formatPhoneNumber($phoneNumber);

            // Use query parameters for authentication instead of Basic Auth
            $response = Http::withoutVerifying()->post($this->baseUrl . '/broadcast', [
                'app_key' => $this->apiKey,
                'app_secret' => $this->apiSecret,
                'msisdn' => $phoneNumber,
                'content' => $message,
                'shortcode_mask' => $this->senderId
            ]);

            $result = $response->json();

            Log::info('M360 API Response', [
                'status' => $response->status(),
                'body' => $result,
                'request' => [
                    'url' => $this->baseUrl . '/broadcast',
                    'msisdn' => $phoneNumber,
                    'content' => $message,
                    'shortcode_mask' => $this->senderId
                ]
            ]);

            if ($response->successful()) {
                Log::info('SMS sent successfully', [
                    'phone' => $phoneNumber,
                    'response' => $result
                ]);
                return true;
            } else {
                Log::error('Failed to send SMS', [
                    'phone' => $phoneNumber,
                    'error' => $result,
                    'status' => $response->status()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('SMS sending exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'phone' => $phoneNumber
            ]);
            return false;
        }
    }

    protected function formatPhoneNumber($phoneNumber)
    {
        // Remove any non-numeric characters
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

        // Add Philippines country code if not present
        if (substr($phoneNumber, 0, 2) == '09') {
            $phoneNumber = '63' . substr($phoneNumber, 1);
        }

        return $phoneNumber;
    }
}
