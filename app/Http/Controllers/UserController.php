<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\UserResources;
use App\Http\Controllers\BaseController;
use App\Http\Requests\User\ShowUsersRequest;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\ShowUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\User\DeleteUserRequest;
use App\Http\Requests\User\DeleteUsersRequest;

class UserController extends BaseController
{
    /**
     * @var UserService
     */
    protected $service;

    /**
     * UserController constructor.
     *
     * @param UserService $service
     */
    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    /**
     * Show users.
     *
     * @param ShowUsersRequest $request
     * @return UserResources|JsonResponse
     */
    public function showUsers(ShowUsersRequest $request): UserResources|JsonResponse
    {
        return $this->prepareOutput($this->service->showUsers());
    }

    /**
     * Create user.
     *
     * @param CreateUserRequest $request
     * @return JsonResponse
     */
    public function createUser(CreateUserRequest $request): JsonResponse
    {
        return $this->prepareOutput($this->service->createUser($request->validated()), 201);
    }

    /**
     * Delete multiple users.
     *
     * @param DeleteUsersRequest $request
     * @return JsonResponse
     */
    public function deleteUsers(DeleteUsersRequest $request): JsonResponse
    {
        $userIds = request()->input('user_ids', []);
        return $this->prepareOutput($this->service->deleteUsers($userIds));
    }

    /**
     * Show single user.
     *
     * @param ShowUserRequest $request
     * @param User $user
     * @return JsonResponse
     */
    public function showUser(ShowUserRequest $request, User $user): JsonResponse
    {
        return $this->prepareOutput($this->service->showUser($user->id));
    }

    /**
     * Update user.
     *
     * @param UpdateUserRequest $request
     * @param User $user
     * @return JsonResponse
     */
    public function updateUser(UpdateUserRequest $request, User $user): JsonResponse
    {
        return $this->prepareOutput($this->service->updateUser($user->id, $request->validated()));
    }

    /**
     * Delete user.
     *
     * @param DeleteUserRequest $request
     * @param User $user
     * @return JsonResponse
     */
    public function deleteUser(DeleteUserRequest $request, User $user): JsonResponse
    {
        return $this->prepareOutput($this->service->deleteUser($user->id));
    }
}
