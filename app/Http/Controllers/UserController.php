<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use App\Services\UserService;
use App\Http\Controllers\BaseController;
use App\Http\Resources\UserResources;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\UpdateUserRequest;

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
     * @return UserResources
     */
    public function showUsers(): UserResources
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
        return $this->prepareOutput($this->service->createUser($request->validated()));
    }

    /**
     * Delete multiple users.
     *
     * @return JsonResponse
     */
    public function deleteUsers(): JsonResponse
    {
        $userIds = request()->input('user_ids', []);
        return $this->prepareOutput($this->service->deleteUsers($userIds));
    }

    /**
     * Show single user.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function showUser(User $user): JsonResponse
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
     * @param User $user
     * @return JsonResponse
     */
    public function deleteUser(User $user): JsonResponse
    {
        return $this->prepareOutput($this->service->deleteUser($user->id));
    }
}
