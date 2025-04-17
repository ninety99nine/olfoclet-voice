<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Contact;

class ContactPolicy extends BasePolicy
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
     * Determine whether the user can view any contacts.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        // Super admins can view all contacts; others need permission within an organization
        $organizationId = request('organization_id');
        return $organizationId ? $this->isOrgUserWithPermission($user, 'view contacts', $organizationId) : false;
    }

    /**
     * Determine whether the user can view the contact.
     *
     * @param User $user
     * @param Contact $contact
     * @return bool
     */
    public function view(User $user, Contact $contact): bool
    {
        return $this->isOrgUserWithPermission($user, 'view contacts', $contact->organization_id);
    }

    /**
     * Determine whether the user can create contacts.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        // Super admins can create contacts; others need permission within an organization
        $organizationId = request('organization_id');
        return $organizationId ? $this->isOrgUserWithPermission($user, 'create contacts', $organizationId) : false;
    }

    /**
     * Determine whether the user can update the contact.
     *
     * @param User $user
     * @param Contact $contact
     * @return bool
     */
    public function update(User $user, Contact $contact): bool
    {
        return $this->isOrgUserWithPermission($user, 'edit contacts', $contact->organization_id);
    }

    /**
     * Determine whether the user can delete the contact.
     *
     * @param User $user
     * @param Contact $contact
     * @return bool
     */
    public function delete(User $user, Contact $contact): bool
    {
        return $this->isOrgUserWithPermission($user, 'edit contacts', $contact->organization_id);
    }
}
