<?php

namespace App\Policies;

use App\Models\User;
use App\Models\MediaFile;

class MediaFilePolicy extends BasePolicy
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
     * Determine whether the user can view any media files.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        $organizationId = request('organization_id');
        return $organizationId ? $this->isOrgUserWithPermission($user, 'view media files', $organizationId) : false;
    }

    /**
     * Determine whether the user can view the media file.
     *
     * @param User $user
     * @param MediaFile $mediaFile
     * @return bool
     */
    public function view(User $user, MediaFile $mediaFile): bool
    {
        return $this->isOrgUserWithPermission($user, 'view media files', $mediaFile->organization_id);
    }

    /**
     * Determine whether the user can create media files.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        $organizationId = request('organization_id');
        return $organizationId ? $this->isOrgUserWithPermission($user, 'create media files', $organizationId) : false;
    }

    /**
     * Determine whether the user can update the media file.
     *
     * @param User $user
     * @param MediaFile $mediaFile
     * @return bool
     */
    public function update(User $user, MediaFile $mediaFile): bool
    {
        return $this->isOrgUserWithPermission($user, 'edit media files', $mediaFile->organization_id);
    }

    /**
     * Determine whether the user can delete the media file.
     *
     * @param User $user
     * @param MediaFile $mediaFile
     * @return bool
     */
    public function delete(User $user, MediaFile $mediaFile): bool
    {
        return $this->isOrgUserWithPermission($user, 'edit media files', $mediaFile->organization_id);
    }

    /**
     * Determine whether the user can delete any media files.
     *
     * @param User $user
     * @return bool
     */
    public function deleteAny(User $user): bool
    {
        $organizationId = request('organization_id');
        return $organizationId ? $this->isOrgUserWithPermission($user, 'edit media files', $organizationId) : false;
    }
}
