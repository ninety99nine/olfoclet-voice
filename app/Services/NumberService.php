<?php

namespace App\Services;

use App\Models\Call;
use App\Models\CallActivity;
use App\Http\Resources\CallResource;
use App\Http\Resources\CallResources;

class CallService extends BaseService
{
    /**
     * @var AfricasTalkingService
     */
    protected $africasTalkingService;

    /**
     * CallService constructor.
     *
     * @param AfricasTalkingService $africasTalkingService
     */
    public function __construct(AfricasTalkingService $africasTalkingService)
    {
        $this->africasTalkingService = $africasTalkingService;
    }

    /**
     * Show calls.
     *
     * @param string|null $organizationId
     * @return CallResources|array
     */
    public function showCalls(?string $organizationId = null): CallResources|array
    {
        $query = Call::query()
            ->when($organizationId, fn($query) => $query->where('organization_id', $organizationId))
            ->when(!request()->has('_sort'), fn($query) => $query->latest());

        return $this->setQuery($query)->getOutput();
    }

    /**
     * Create call.
     *
     * @param string $organizationId
     * @param array $data
     * @return array
     */
    public function createCall(string $organizationId, array $data): array
    {
        $data['organization_id'] = $organizationId;

        // For outbound calls, initiate via Africa's Talking API
        if ($data['direction'] === 'outbound') {
            $response = $this->africasTalkingService->initiateOutboundCall($data['from'], $data['to']);
            if (!$response['success']) {
                return ['created' => false, 'message' => $response['message']];
            }
            $data['session_id'] = $response['sessionId'] ?? null;
            $data['status'] = $response['status'] === 'Success' ? 'initiated' : 'failed';
        }

        $call = Call::create($data);

        // Log the call creation activity
        CallActivity::create([
            'call_id' => $call->id,
            'activity_type' => 'call started',
            'description' => "Call {$call->direction} initiated from {$call->from} to {$call->to}",
            'performed_by' => $data['agent_id'] ?? null,
            'metadata' => ['session_id' => $call->session_id],
        ]);

        return $this->showCreatedResource($call);
    }

    /**
     * Show call.
     *
     * @param string $callId
     * @return CallResource
     */
    public function showCall(string $callId): CallResource
    {
        $call = Call::query()
            ->with($this->getRequestRelationships())
            ->withCount($this->getRequestCountableRelationships())
            ->findOrFail($callId);
        return $this->showResource($call);
    }

    /**
     * Update call.
     *
     * @param string $callId
     * @param array $data
     * @return array
     */
    public function updateCall(string $callId, array $data): array
    {
        $call = Call::findOrFail($callId);

        // Log status change if updated
        if (isset($data['status']) && $data['status'] !== $call->status) {
            CallActivity::create([
                'call_id' => $call->id,
                'activity_type' => 'status changed',
                'description' => "Call status changed from {$call->status} to {$data['status']}",
                'performed_by' => request()->user()->id ?? null,
                'metadata' => ['old_status' => $call->status, 'new_status' => $data['status']],
            ]);
        }

        $call->update($data);

        return $this->showUpdatedResource($call);
    }

    /**
     * Delete call.
     *
     * @param string $callId
     * @return array
     */
    public function deleteCall(string $callId): array
    {
        $call = Call::findOrFail($callId);

        $deleted = $call->delete();

        if ($deleted) {
            return ['deleted' => true, 'message' => 'Call deleted'];
        }

        return ['deleted' => false, 'message' => 'Call delete unsuccessful'];
    }

    /**
     * Delete calls.
     *
     * @param string|null $organizationId
     * @param array $callIds
     * @return array
     */
    public function deleteCalls(?string $organizationId, array $callIds): array
    {
        $query = Call::query()
            ->when($organizationId, fn($query) => $query->where('organization_id', $organizationId))
            ->whereIn('id', $callIds);

        $calls = $query->get();

        if ($totalCalls = $calls->count()) {
            $calls->each->delete();
            return ['deleted' => true, 'message' => "$totalCalls call(s) deleted"];
        }

        return ['deleted' => false, 'message' => 'No calls deleted'];
    }
}
