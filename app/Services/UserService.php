<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use App\Enums\SystemRole;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserResources;

class UserService extends BaseService
{
    /**
     * Show Users.
     *
     * @return UserResources|array
     */
    public function showUsers(): UserResources|array
    {
        $query = User::query()->when(!request()->has('_sort'), fn($query) => $query->latest());
        return $this->setQuery($query)->getOutput();
    }

    /**
     * Create User.
     *
     * @param array $data
     * @return array
     */
    public function createUser(array $data): array
    {
        $data['password'] = Hash::make($data['password']);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => $data['password']
        ]);

        if($data['type'] === SystemRole::SUPER_ADMIN->value) {

            $superAdminRole = Role::where('name', SystemRole::SUPER_ADMIN->value)->whereNull('organization_id');
            $user->assignRole($superAdminRole);

        }else if (isset($data['organization_id']) && $data['type'] === SystemRole::REGULAR->value) {

            $user->organizations()->syncWithoutDetaching([$data['organization_id']]);
            //  Add the role assigned

        }

        // Optionally dispatch email here...

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
