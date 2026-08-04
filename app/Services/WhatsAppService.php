<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $apiUrl;
    protected ?string $apiKey;

    public function __construct()
    {
        $this->apiUrl = config('services.whatsapp.api_url', 'https://api.fonnte.com/send');
        $this->apiKey = config('services.whatsapp.api_key');
    }

    public function send(string $phone, string $message): bool
    {
        if (empty($this->apiKey)) {
            Log::warning('WhatsApp API key not configured. Skipping message.', compact('phone'));
            return false;
        }

        $phone = $this->normalizePhone($phone);

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->apiKey,
            ])->post($this->apiUrl, [
                'target' => $phone,
                'message' => $message,
                'countryCode' => '62',
            ]);

            if ($response->successful()) {
                Log::info("WhatsApp sent to {$phone}");
                return true;
            }

            Log::error("WhatsApp failed to {$phone}: " . $response->body());
            return false;

        } catch (\Exception $e) {
            Log::error("WhatsApp error to {$phone}: " . $e->getMessage());
            return false;
        }
    }

    protected function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (strlen($phone) <= 11 && !str_starts_with($phone, '0')) {
            $phone = '0' . $phone;
        }

        return $phone;
    }
}
