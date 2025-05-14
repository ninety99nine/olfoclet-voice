<?php

namespace App\Services;

use App\Models\OmniChannelMessage;
use App\Http\Resources\OmniChannelMessageResource;
use App\Http\Resources\OmniChannelMessageResources;

class OmniChannelMessageService extends BaseService
{
    /**
     * @var WhatsAppService
     */
    protected $whatsAppService;

    /**
     * OmniChannelMessageService constructor.
     *
     * @param WhatsAppService $whatsAppService
     */
    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    /**
     * Show omni channel messages.
     *
     * @param string|null $organizationId
     * @return OmniChannelMessageResources|array
     */
    public function showOmniChannelMessages(?string $organizationId = null): OmniChannelMessageResources|array
    {
        $query = OmniChannelMessage::query()
            ->when($organizationId, fn($query) => $query->where('organization_id', $organizationId))
            ->when(!request()->has('_sort'), fn($query) => $query->latest());

        return $this->setQuery($query)->getOutput();
    }

    /**
     * Create omni channel message.
     *
     * @param string $organizationId
     * @param array $data
     * @return array
     */
    public function createOmniChannelMessage(string $organizationId, array $data): array
    {
        $data['organization_id'] = $organizationId;

        // For outbound WhatsApp messages, send via WhatsApp API
        if ($data['channel'] === 'whatsapp' && $data['direction'] === 'outbound') {
            $response = $this->whatsAppService->sendMessage(
                $data['to'],
                $data['message_type'] ?? 'text', // Default to 'text' if not specified
                $data['content'],
                $data['message_type'] === 'template' ? ($data['template_name'] ?? 'hello_world') : null,
                $data['language_code'] ?? 'en_US'
            );
            if (!$response['success']) {
                return ['created' => false, 'message' => $response['message']];
            }
            $data['external_message_id'] = $response['messageId'] ?? null;
            $data['status'] = $response['success'] ? 'sent' : 'failed';
        }

        $omniChannelMessage = OmniChannelMessage::create($data);

        // Broadcast the new message event
        event(new \App\Events\NewOmniChannelMessage($omniChannelMessage));

        // Return the created message in the same format as showOmniChannelMessages
        return [
            'created' => true,
            'message' => null,
            'data' => new OmniChannelMessageResource($omniChannelMessage),
        ];
    }

    /**
     * Show omni channel message.
     *
     * @param string $omniChannelMessageId
     * @return OmniChannelMessageResource
     */
    public function showOmniChannelMessage(string $omniChannelMessageId): OmniChannelMessageResource
    {
        $omniChannelMessage = OmniChannelMessage::query()
            ->with($this->getRequestRelationships())
            ->withCount($this->getRequestCountableRelationships())
            ->findOrFail($omniChannelMessageId);
        return $this->showResource($omniChannelMessage);
    }

    /**
     * Update omni channel message.
     *
     * @param string $omniChannelMessageId
     * @param array $data
     * @return array
     */
    public function updateOmniChannelMessage(string $omniChannelMessageId, array $data): array
    {
        $omniChannelMessage = OmniChannelMessage::findOrFail($omniChannelMessageId);
        $omniChannelMessage->update($data);

        return $this->showUpdatedResource($omniChannelMessage);
    }

    /**
     * Delete omni channel message.
     *
     * @param string $omniChannelMessageId
     * @return array
     */
    public function deleteOmniChannelMessage(string $omniChannelMessageId): array
    {
        $omniChannelMessage = OmniChannelMessage::findOrFail($omniChannelMessageId);

        $deleted = $omniChannelMessage->delete();

        if ($deleted) {
            return ['deleted' => true, 'message' => 'Omni channel message deleted'];
        }

        return ['deleted' => false, 'message' => 'Omni channel message delete unsuccessful'];
    }

    /**
     * Delete omni channel messages.
     *
     * @param string|null $organizationId
     * @param array $omniChannelMessageIds
     * @return array
     */
    public function deleteOmniChannelMessages(?string $organizationId, array $omniChannelMessageIds): array
    {
        $query = OmniChannelMessage::query()
            ->when($organizationId, fn($query) => $query->where('organization_id', $organizationId))
            ->whereIn('id', $omniChannelMessageIds);

        $omniChannelMessages = $query->get();

        if ($totalOmniChannelMessages = $omniChannelMessages->count()) {
            $omniChannelMessages->each->delete();
            return ['deleted' => true, 'message' => "$totalOmniChannelMessages omni channel message(s) deleted"];
        }

        return ['deleted' => false, 'message' => 'No omni channel messages deleted'];
    }
}
