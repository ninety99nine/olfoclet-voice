<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AfricasTalkingService
{
    /**
     * Initiate an outbound call using Africa's Talking API.
     *
     * @param string $from
     * @param string $to
     * @return array
     */
    public function initiateOutboundCall(string $from, string $to): array
    {
        $apiKey = config('services.africastalking.api_key');
        $username = config('services.africastalking.username');

        if (!$apiKey || !$username) {
            return [
                'success' => false,
                'message' => 'Africa\'s Talking API credentials not configured',
            ];
        }

        $response = Http::withHeaders([
            'apiKey' => $apiKey,
            'Accept' => 'application/json',
        ])->post("https://voice.africastalking.com/call", [
            'username' => $username,
            'from' => $from,
            'to' => $to,
        ]);

        if (!$response->successful()) {
            return [
                'success' => false,
                'message' => $response->json('errorMessage') ?? 'Failed to initiate call',
            ];
        }

        $data = $response->json();
        return [
            'success' => true,
            'status' => $data['status'] ?? 'Unknown',
            'sessionId' => $data['sessionId'] ?? null,
            'message' => 'Outbound call initiated successfully',
        ];
    }
}
