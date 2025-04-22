<?php

namespace App\Http\Controllers;

use App\Models\CustomAttribute;
use Illuminate\Http\JsonResponse;
use App\Services\CustomAttributeService;
use App\Http\Controllers\BaseController;
use App\Http\Resources\CustomAttributeResources;
use App\Http\Requests\CustomAttribute\ShowCustomAttributeRequest;
use App\Http\Requests\CustomAttribute\ShowCustomAttributesRequest;
use App\Http\Requests\CustomAttribute\CreateCustomAttributeRequest;
use App\Http\Requests\CustomAttribute\UpdateCustomAttributeRequest;
use App\Http\Requests\CustomAttribute\DeleteCustomAttributeRequest;
use App\Http\Requests\CustomAttribute\DeleteCustomAttributesRequest;

class CustomAttributeController extends BaseController
{
    /**
     * @var CustomAttributeService
     */
    protected $service;

    /**
     * CustomAttributeController constructor.
     *
     * @param CustomAttributeService $service
     */
    public function __construct(CustomAttributeService $service)
    {
        $this->service = $service;
    }

    /**
     * Show custom attributes.
     *
     * @param ShowCustomAttributesRequest $request
     * @return CustomAttributeResources|JsonResponse
     */
    public function showCustomAttributes(ShowCustomAttributesRequest $request): CustomAttributeResources|JsonResponse
    {
        return $this->prepareOutput($this->service->showCustomAttributes(request('organization_id')));
    }

    /**
     * Create custom attribute.
     *
     * @param CreateCustomAttributeRequest $request
     * @return JsonResponse
     */
    public function createCustomAttribute(CreateCustomAttributeRequest $request): JsonResponse
    {
        return $this->prepareOutput($this->service->createCustomAttribute(request('organization_id'), $request->validated()), 201);
    }

    /**
     * Show single custom attribute.
     *
     * @param ShowCustomAttributeRequest $request
     * @param CustomAttribute $customAttribute
     * @return JsonResponse
     */
    public function showCustomAttribute(ShowCustomAttributeRequest $request, CustomAttribute $customAttribute): JsonResponse
    {
        return $this->prepareOutput($this->service->showCustomAttribute($customAttribute->id));
    }

    /**
     * Update custom attribute.
     *
     * @param UpdateCustomAttributeRequest $request
     * @param CustomAttribute $customAttribute
     * @return JsonResponse
     */
    public function updateCustomAttribute(UpdateCustomAttributeRequest $request, CustomAttribute $customAttribute): JsonResponse
    {
        return $this->prepareOutput($this->service->updateCustomAttribute($customAttribute->id, $request->validated()));
    }

    /**
     * Delete custom attribute.
     *
     * @param DeleteCustomAttributeRequest $request
     * @param CustomAttribute $customAttribute
     * @return JsonResponse
     */
    public function deleteCustomAttribute(DeleteCustomAttributeRequest $request, CustomAttribute $customAttribute): JsonResponse
    {
        return $this->prepareOutput($this->service->deleteCustomAttribute($customAttribute->id));
    }

    /**
     * Delete multiple custom attributes.
     *
     * @param DeleteCustomAttributesRequest $request
     * @return JsonResponse
     */
    public function deleteCustomAttributes(DeleteCustomAttributesRequest $request): JsonResponse
    {
        $customAttributeIds = request()->input('custom_attribute_ids', []);
        return $this->prepareOutput($this->service->deleteCustomAttributes(request('organization_id'), $customAttributeIds));
    }
}
