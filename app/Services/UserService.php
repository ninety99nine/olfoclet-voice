<?php

namespace App\Services;

use Exception;
use App\Models\Role;
use App\Models\User;
use App\Enums\SystemRole;
use Illuminate\Support\Str;
use App\Models\Organization;
use App\Mail\UserAccountCreated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Mail;
use App\Http\Resources\UserResources;

class UserService extends BaseService
{
    /**
     * Show Users.
     *
     * @param string|null $organizationId
     * @return UserResources|array
     */
    public function showUsers(?string $organizationId = null): UserResources|array
    {
        $query = User::query()
            ->when($organizationId, function ($query) use ($organizationId) {
                $query->whereHas('organizations', fn($q) => $q->where('organization_id', $organizationId));
            })
            ->when(!request()->has('_sort'), fn($query) => $query->latest());

        return $this->setQuery($query)->getOutput();
    }

    /**
     * Create User.
     *
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function createUser(array $data): array
    {
        $isSuperAdmin = $data['type'] === SystemRole::SUPER_ADMIN->value;

        if ($isSuperAdmin) {
            $superAdminRole = Role::where('name', SystemRole::SUPER_ADMIN->value)
                                  ->whereNull('organization_id')
                                  ->first();

            if (!$superAdminRole) {
                throw new Exception('The Super Admin role does not exist to assign to this user');
            }
        }

        if ($data['role_id']) {
            $role = Role::where('id', $data['role_id'])->where('organization_id', $data['organization_id'])->first();

            if (!$role) {
                throw new Exception('The specified role does not exist or does not belong to the selected organization');
            }
        }

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => null, // No password set initially
        ]);

        $organizationName = null;
        if ($isSuperAdmin) {
            $user->assignRole($superAdminRole);
        } elseif (isset($data['organization_id']) && $data['type'] === SystemRole::REGULAR->value) {
            $user->organizations()->syncWithoutDetaching([$data['organization_id']]);
            $user->assignRole($role);

            // Fetch the organization name if organization_id is provided
            $organization = Organization::find($data['organization_id']);
            $organizationName = $organization ? $organization->name : null;
        }

        // Generate a temporary token for password setup
        $token = Str::random(60);
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            ['token' => hash('sha256', $token), 'created_at' => now()]
        );

        // Send email to the user with a setup link, including organization name if available
        $setupUrl = config('app.frontend_url') . '/setup-account?token=' . $token . '&email=' . urlencode($user->email);

        try {
            Mail::to($user->email)->send(new UserAccountCreated($user->email, $setupUrl, $organizationName));
        } catch (\Exception $e) {
            throw new Exception('Failed to send account email to ' . $user->email . ': ' . $e->getMessage());
        }

        return $this->showCreatedResource($user);
    }

    /**
     * Delete User.
     *
     * @param string $userId
     * @return array
     */
    public function deleteUser(string $userId): array
    {
        $user = User::findOrFail($userId);

        if ($user) {

            $deleted = $user->delete();

            if ($deleted) {
                return ['deleted' => true, 'message' => 'User deleted'];
            } else {
                return ['deleted' => false, 'message' => 'User delete unsuccessful'];
            }

        } else {
            return ['deleted' => false, 'message' => 'This User does not exist'];
        }
    }

    /**
     * Delete Users.
     *
     * @param array $userIds
     * @return array
     */
    public function deleteUsers(array $userIds): array
    {
        $users = User::whereIn('id', $userIds)->get();

        if ($totalUsers = $users->count()) {

            foreach ($users as $user) {
                $user->delete();
            }

            return ['deleted' => true, 'message' => $totalUsers . ($totalUsers == 1 ? ' User' : ' Users') . ' deleted'];

        } else {
            return ['deleted' => false, 'message' => 'No Users deleted'];
        }
    }

    /**
     * Show User.
     *
     * @param string $userId
     * @return UserResource
     */
    public function showUser(string $userId): UserResource
    {
        $user = User::query()
            ->with($this->getRequestRelationships())
            ->withCount($this->getRequestCountableRelationships())
            ->findOrFail($userId);
        return $this->showResource($user);
    }

    /**
     * Update User.
     *
     * @param string $userId
     * @param array $data
     * @return array
     */
    public function updateUser(string $userId, array $data): array
    {
        $user = User::findOrFail($userId);

        if ($user) {

            if (isset($data['password']) && !empty($data['password'])) {
                $data['password'] = \Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            $user->update($data);
            return $this->showUpdatedResource($user);

        } else {

            return ['updated' => false, 'message' => 'This User does not exist'];

        }
    }

}
