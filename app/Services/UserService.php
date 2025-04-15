<?php

namespace App\Services;

use App\Enums\UserType;
use App\Models\User;
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
            'type'     => $data['type'],
            'password' => $data['password']
        ]);

        if (isset($data['organization_id']) && $data['type'] === UserType::REGULAR->value) {
            $user->organizations()->syncWithoutDetaching([$data['organization_id']]);
        }

        // Optionally dispatch email here...

        return $this->showCreatedResource($user);
    }


    /**
     * Delete User.
     *
     * @param int $userId
     * @return array
     */
    public function deleteUser(int $userId): array
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
     * @param int $userId
     * @return UserResource
     */
    public function showUser(int $userId): UserResource
    {
        $user = User::findOrFail($userId);
        return $this->showResource($user);
    }

    /**
     * Update User.
     *
     * @param int $userId
     * @param array $data
     * @return array
     */
    public function updateUser(int $userId, array $data): array
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
