<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $client;
    protected $apiUrl;
    protected $accessToken;

    /**
     * WhatsAppService constructor.
     */
    public function __construct()
    {
        $this->client = new Client();
        $this->apiUrl = config('services.whatsapp.api_url');
        $this->accessToken = config('services.whatsapp.access_token');
    }

    /**
     * Send a WhatsApp message.
     *
     * @param string $to
     * @param string $messageType
     * @param string $content
     * @param string|null $templateName
     * @param string $languageCode
     * @return array
     */
    public function sendMessage(string $to, string $messageType, string $content, ?string $templateName = null, string $languageCode = 'en_US'): array
    {
        if (empty($this->accessToken)) {
            Log::error('WhatsAppService: Access token is not configured.');
            return [
                'success' => false,
                'message' => 'Access token is not configured. Please update the WhatsApp API token in the environment settings.',
            ];
        }

        try {
            $payload = [
                'messaging_product' => 'whatsapp',
                'to' => $to,
            ];

            if ($messageType === 'template') {
                $payload['type'] = 'template';
                $payload['template'] = [
                    'name' => $templateName ?? 'hello_world',
                    'language' => [
                        'code' => $languageCode,
                    ],
                ];
            } else {
                $payload['type'] = 'text';
                $payload['text'] = [
                    'body' => $content,
                ];
            }

            $response = $this->client->post($this->apiUrl, [
                'headers' => [
                    'Authorization' => "Bearer {$this->accessToken}",
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload,
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            Log::info($body);

            return [
                'success' => true,
                'message' => 'Message sent successfully',
                'messageId' => $body['messages'][0]['id'] ?? null,
                'messageStatus' => $body['messages'][0]['message_status'] ?? null,
                'contactWaId' => $body['contacts'][0]['wa_id'] ?? null,
            ];
        } catch (RequestException $e) {
            $errorResponse = $e->hasResponse() ? json_decode($e->getResponse()->getBody()->getContents(), true) : null;
            $errorMessage = $errorResponse['error']['message'] ?? $e->getMessage();
            $errorCode = $errorResponse['error']['code'] ?? null;
            $errorSubcode = $errorResponse['error']['error_subcode'] ?? null;

            Log::error('WhatsAppService: Failed to send message', [
                'to' => $to,
                'messageType' => $messageType,
                'content' => $content,
                'error' => $errorMessage,
                'code' => $errorCode,
                'subcode' => $errorSubcode,
            ]);

            if ($errorCode === 190 && $errorSubcode === 463) {
                return [
                    'success' => false,
                    'message' => 'Access token has expired. Please refresh the WhatsApp API token.',
                    'errorDetails' => $errorMessage,
                ];
            }

            return [
                'success' => false,
                'message' => $errorMessage,
            ];
        }
    }
}
