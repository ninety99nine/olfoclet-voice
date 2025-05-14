<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Models\OmniChannelMessage;
use App\Http\Controllers\BaseController;
use App\Services\OmniChannelMessageService;
use App\Http\Resources\OmniChannelMessageResources;
use App\Http\Requests\OmniChannelMessage\ShowOmniChannelMessageRequest;
use App\Http\Requests\OmniChannelMessage\ShowOmniChannelMessagesRequest;
use App\Http\Requests\OmniChannelMessage\CreateOmniChannelMessageRequest;
use App\Http\Requests\OmniChannelMessage\UpdateOmniChannelMessageRequest;
use App\Http\Requests\OmniChannelMessage\DeleteOmniChannelMessageRequest;
use App\Http\Requests\OmniChannelMessage\DeleteOmniChannelMessagesRequest;

class OmniChannelMessageController extends BaseController
{
    /**
     * @var OmniChannelMessageService
     */
    protected $service;

    /**
     * OmniChannelMessageController constructor.
     *
     * @param OmniChannelMessageService $service
     */
    public function __construct(OmniChannelMessageService $service)
    {
        $this->service = $service;
    }

    /**
     * Show omni channel messages.
     *
     * @param ShowOmniChannelMessagesRequest $request
     * @return OmniChannelMessageResources|JsonResponse
     */
    public function showOmniChannelMessages(ShowOmniChannelMessagesRequest $request): OmniChannelMessageResources|JsonResponse
    {
        return $this->prepareOutput($this->service->showOmniChannelMessages(request('organization_id')));
    }

    /**
     * Create omni channel message.
     *
     * @param CreateOmniChannelMessageRequest $request
     * @return JsonResponse
     */
    public function createOmniChannelMessage(CreateOmniChannelMessageRequest $request): JsonResponse
    {
        return $this->prepareOutput($this->service->createOmniChannelMessage(request('organization_id'), $request->validated()), 201);
    }

    /**
     * Delete multiple omni channel messages.
     *
     * @param DeleteOmniChannelMessagesRequest $request
     * @return JsonResponse
     */
    public function deleteOmniChannelMessages(DeleteOmniChannelMessagesRequest $request): JsonResponse
    {
        $omniChannelMessageIds = request()->input('omni_channel_message_ids', []);
        return $this->prepareOutput($this->service->deleteOmniChannelMessages(request('organization_id'), $omniChannelMessageIds));
    }

    /**
     * Show single omni channel message.
     *
     * @param ShowOmniChannelMessageRequest $request
     * @param OmniChannelMessage $omniChannelMessage
     * @return JsonResponse
     */
    public function showOmniChannelMessage(ShowOmniChannelMessageRequest $request, OmniChannelMessage $omniChannelMessage): JsonResponse
    {
        return $this->prepareOutput($this->service->showOmniChannelMessage($omniChannelMessage->id));
    }

    /**
     * Update omni channel message.
     *
     * @param UpdateOmniChannelMessageRequest $request
     * @param OmniChannelMessage $omniChannelMessage
     * @return JsonResponse
     */
    public function updateOmniChannelMessage(UpdateOmniChannelMessageRequest $request, OmniChannelMessage $omniChannelMessage): JsonResponse
    {
        return $this->prepareOutput($this->service->updateOmniChannelMessage($omniChannelMessage->id, $request->validated()));
    }

    /**
     * Delete omni channel message.
     *
     * @param DeleteOmniChannelMessageRequest $request
     * @param OmniChannelMessage $omniChannelMessage
     * @return JsonResponse
     */
    public function deleteOmniChannelMessage(DeleteOmniChannelMessageRequest $request, OmniChannelMessage $omniChannelMessage): JsonResponse
    {
        return $this->prepareOutput($this->service->deleteOmniChannelMessage($omniChannelMessage->id));
    }
}
