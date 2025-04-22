<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Queue;

class QueuePolicy extends BasePolicy
{
    /**
     * Grant all permissions to super admins who have roles not tied to any organization.
     *
     * @param User $user
     * @param string $ability
     * @return bool|null
     */
    public function before(User $user, string $ability): bool|null
    {
        return $this->authService->isSuperAdmin($user);
    }

    /**
     * Determine whether the user can view any queues.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        $organizationId = request('organization_id');
        return $organizationId ? $this->isOrgUserWithPermission($user, 'view queues', $organizationId) : false;
    }

    /**
     * Determine whether the user can view the queue.
     *
     * @param User $user
     * @param Queue $queue
     * @return bool
     */
    public function view(User $user, Queue $queue): bool
    {
        return $this->isOrgUserWithPermission($user, 'view queues', $queue->organization_id);
    }

    /**
     * Determine whether the user can create queues.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        $organizationId = request('organization_id');
        return $organizationId ? $this->isOrgUserWithPermission($user, 'create queues', $organizationId) : false;
    }

    /**
     * Determine whether the user can update the queue.
     *
     * @param User $user
     * @param Queue $queue
     * @return bool
     */
    public function update(User $user, Queue $queue): bool
    {
        return $this->isOrgUserWithPermission($user, 'edit queues', $queue->organization_id);
    }

    /**
     * Determine whether the user can delete the queue.
     *
     * @param User $user
     * @param Queue $queue
     * @return bool
     */
    public function delete(User $user, Queue $queue): bool
    {
        return $this->isOrgUserWithPermission($user, 'edit queues', $queue->organization_id);
    }

    /**
     * Determine whether the user can delete any queues.
     *
     * @param User $user
     * @return bool
     */
    public function deleteAny(User $user): bool
    {
        $organizationId = request('organization_id');
        return $organizationId ? $this->isOrgUserWithPermission($user, 'edit queues', $organizationId) : false;
    }
}
