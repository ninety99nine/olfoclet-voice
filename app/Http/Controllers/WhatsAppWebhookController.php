<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OmniChannelMessage;
use Illuminate\Support\Facades\Log;
use App\Events\NewOmniChannelMessage;
use App\Http\Controllers\BaseController;

class WhatsAppWebhookController extends BaseController
{
    /**
     * Handle WhatsApp webhook events.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request)
    {
        $payload = $request->all();

        Log::info('WhatsAppWebhookController: Received webhook event', [
            'payload' => $payload,
        ]);

        // Verify webhook subscription (for initial setup)
        if ($request->query('hub_mode') === 'subscribe') {
            return $this->verifyWebhook($request);
        }

        // Process inbound messages
        $this->processMessages($payload);

        return response()->json(['status' => 'received']);
    }

    /**
     * Verify the webhook subscription.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function verifyWebhook(Request $request)
    {
        $hubMode = $request->query('hub_mode');
        $hubToken = $request->query('hub_verify_token');
        $hubChallenge = $request->query('hub_challenge');

        // Verify the token (set this in your .env file or config)
        $verifyToken = config('services.whatsapp.webhook_verify_token');

        if ($hubMode === 'subscribe' && $hubToken === $verifyToken) {
            Log::info('WhatsAppWebhookController: Webhook verified successfully', [
                'challenge' => $hubChallenge,
            ]);
            return response($hubChallenge, 200);
        }

        Log::error('WhatsAppWebhookController: Webhook verification failed', [
            'hub_mode' => $hubMode,
            'hub_verify_token' => $hubToken,
        ]);

        return response()->json(['error' => 'Invalid verify token'], 403);
    }

    /**
     * Process inbound messages from the webhook payload.
     *
     * @param array $payload
     * @return void
     */
    protected function processMessages(array $payload)
    {
        $entries = $payload['entry'] ?? [];

        foreach ($entries as $entry) {
            $changes = $entry['changes'] ?? [];

            foreach ($changes as $change) {
                $value = $change['value'] ?? [];
                $messages = $value['messages'] ?? [];

                foreach ($messages as $messageData) {
                    if ($messageData['type'] === 'text') {
                        $this->handleTextMessage($messageData, $value);
                    }
                    // Add support for other message types (e.g., image, audio) as needed
                }
            }
        }
    }

    /**
     * Handle an inbound text message.
     *
     * @param array $messageData
     * @param array $value
     * @return void
     */
    protected function handleTextMessage(array $messageData, array $value)
    {
        $from = $messageData['from'] ?? null;
        $content = $messageData['text']['body'] ?? null;
        $messageId = $messageData['id'] ?? null;
        $timestamp = $messageData['timestamp'] ?? null;

        if (!$from || !$content || !$messageId) {
            Log::error('WhatsAppWebhookController: Invalid message data', [
                'messageData' => $messageData,
            ]);
            return;
        }

        // Determine the organization ID (you may need to map this based on your setup)
        $organizationId = $this->resolveOrganizationId($value);
        if (!$organizationId) {
            Log::error('WhatsAppWebhookController: Could not determine organization ID', [
                'value' => $value,
            ]);
            return;
        }

        // Create the OmniChannelMessage record
        $message = OmniChannelMessage::create([
            'organization_id' => $organizationId,
            'channel' => 'whatsapp',
            'direction' => 'inbound',
            'status' => 'delivered',
            'from' => $from,
            'to' => 'telcoflo', // Replace with your WhatsApp business number or identifier
            'content' => $content,
            'message_type' => 'text',
            'external_message_id' => $messageId,
            'metadata' => $messageData,
        ]);

        Log::info('WhatsAppWebhookController: Created inbound message', [
            'messageId' => $messageId,
            'from' => $from,
            'content' => $content,
            'organizationId' => $organizationId,
        ]);

        // Broadcast the new message event
        event(new NewOmniChannelMessage($message));
    }

    /**
     * Resolve the organization ID from the webhook payload.
     *
     * @param array $value
     * @return string|null
     */
    protected function resolveOrganizationId(array $value): ?string
    {
        // In a real application, you might map the WhatsApp business number to an organization
        // For now, we'll use the organization ID from the chat context
        // You might need to adjust this based on your setup
        return '2ab65459-5232-308f-8f36-e6a95187dd11';
    }
}
