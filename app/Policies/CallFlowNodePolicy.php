<?php

namespace App\Policies;

use App\Models\User;
use App\Models\CallFlow;
use App\Models\CallFlowNode;

class CallFlowNodePolicy extends BasePolicy
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
     * Determine whether the user can view any call flow nodes.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        $callFlowId = request('call_flow_id');
        if (!$callFlowId) {
            return false;
        }
        $callFlow = CallFlow::findOrFail($callFlowId);
        return $this->isOrgUserWithPermission($user, 'view call flows', $callFlow->organization_id);
    }

    /**
     * Determine whether the user can view the call flow node.
     *
     * @param User $user
     * @param CallFlowNode $callFlowNode
     * @return bool
     */
    public function view(User $user, CallFlowNode $callFlowNode): bool
    {
        return $this->isOrgUserWithPermission($user, 'view call flows', $callFlowNode->callFlow->organization_id);
    }

    /**
     * Determine whether the user can create call flow nodes.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        $callFlowId = request('call_flow_id');
        if (!$callFlowId) {
            return false;
        }
        $callFlow = CallFlow::findOrFail($callFlowId);
        return $this->isOrgUserWithPermission($user, 'edit call flows', $callFlow->organization_id);
    }

    /**
     * Determine whether the user can update the call flow node.
     *
     * @param User $user
     * @param CallFlowNode $callFlowNode
     * @return bool
     */
    public function update(User $user, CallFlowNode $callFlowNode): bool
    {
        return $this->isOrgUserWithPermission($user, 'edit call flows', $callFlowNode->callFlow->organization_id);
    }

    /**
     * Determine whether the user can delete the call flow node.
     *
     * @param User $user
     * @param CallFlowNode $callFlowNode
     * @return bool
     */
    public function delete(User $user, CallFlowNode $callFlowNode): bool
    {
        return $this->isOrgUserWithPermission($user, 'edit call flows', $callFlowNode->callFlow->organization_id);
    }

    /**
     * Determine whether the user can delete any call flow nodes.
     *
     * @param User $user
     * @return bool
     */
    public function deleteAny(User $user): bool
    {
        $callFlowId = request('call_flow_id');
        if (!$callFlowId) {
            return false;
        }
        $callFlow = CallFlow::findOrFail($callFlowId);
        return $this->isOrgUserWithPermission($user, 'edit call flows', $callFlow->organization_id);
    }
}
