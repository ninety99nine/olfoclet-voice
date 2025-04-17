<?php

namespace App\Services;

use App\Models\Call;
use App\Models\CallActivity;
use App\Http\Resources\CallActivityResource;
use App\Http\Resources\CallActivityResources;

class CallActivityService extends BaseService
{
    /**
     * Show call activities.
     *
     * @param string|null $organizationId
     * @return CallActivityResources|array
     */
    public function showCallActivities(?string $organizationId = null): CallActivityResources|array
    {
        $query = CallActivity::query()
            ->when($organizationId, fn($query) => $query->whereHas('call', fn($q) => $q->where('organization_id', $organizationId)))
            ->when(!request()->has('_sort'), fn($query) => $query->latest());

        return $this->setQuery($query)->getOutput();
    }

    /**
     * Create call activity.
     *
     * @param string $organizationId
     * @param array $data
     * @return array
     */
    public function createCallActivity(string $organizationId, array $data): array
    {
        // Ensure the call belongs to the organization
        Call::where('id', $data['call_id'])
            ->where('organization_id', $organizationId)
            ->firstOrFail();

        $callActivity = CallActivity::create($data);

        return $this->showCreatedResource($callActivity);
    }

    /**
     * Show call activity.
     *
     * @param string $callActivityId
     * @return CallActivityResource
     */
    public function showCallActivity(string $callActivityId): CallActivityResource
    {
        $callActivity = CallActivity::query()
            ->findOrFail($callActivityId);

        return $this->showResource($callActivity);
    }

    /**
     * Update call activity.
     *
     * @param string $callActivityId
     * @param array $data
     * @return array
     */
    public function updateCallActivity(string $callActivityId, array $data): array
    {
        $callActivity = CallActivity::findOrFail($callActivityId);
        $callActivity->update($data);

        return $this->showUpdatedResource($callActivity);
    }

    /**
     * Delete call activity.
     *
     * @param string $callActivityId
     * @return array
     */
    public function deleteCallActivity(string $callActivityId): array
    {
        $callActivity = CallActivity::findOrFail($callActivityId);

        $deleted = $callActivity->delete();

        if ($deleted) {
            return ['deleted' => true, 'message' => 'Call activity deleted'];
        }

        return ['deleted' => false, 'message' => 'Call activity delete unsuccessful'];
    }

    /**
     * Delete call activities.
     *
     * @param string|null $organizationId
     * @param array $callActivityIds
     * @return array
     */
    public function deleteCallActivities(?string $organizationId, array $callActivityIds): array
    {
        $query = CallActivity::query()
            ->when($organizationId, fn($query) => $query->whereHas('call', fn($q) => $q->where('organization_id', $organizationId)))
            ->whereIn('id', $callActivityIds);

        $callActivities = $query->get();

        if ($totalCallActivities = $callActivities->count()) {
            $callActivities->each->delete();
            return ['deleted' => true, 'message' => "$totalCallActivities call activity(s) deleted"];
        }

        return ['deleted' => false, 'message' => 'No call activities deleted'];
    }
}
